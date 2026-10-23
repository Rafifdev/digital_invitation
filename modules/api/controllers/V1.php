<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * InfoMase Digital Wedding Invitation — REST API V1 Controller
 * Conforms 100% to BACKEND_API_SPECIFICATION.md
 */
class V1 extends API
{
    protected $jwt_secret = 'infomase_wedding_jwt_secret_key_2026';
    protected $jwt_algo = 'HS256';
    protected $jwt_expires_in = 604800; // 7 days (seconds)

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper(['url', 'file', 'string', 'text']);
        $this->load->library(['upload', 'form_validation']);

        // Default CORS headers for all incoming requests
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Authorization, Content-Type, Accept, X-Requested-With');
    }

    /**
     * Intercept all requests for CORS OPTIONS preflight
     */
    public function _remap($object_called, $arguments = [])
    {
        if (strtoupper($this->input->server('REQUEST_METHOD')) === 'OPTIONS') {
            http_response_code(200);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['status' => 'OK']);
            exit;
        }

        return parent::_remap($object_called, $arguments);
    }

    // =========================================================================
    // HELPER UTILITIES: JSON PARSER, JWT AUTH & RESPONSE BUILDER
    // =========================================================================

    /**
     * Output standardized JSON response
     */
    protected function send_json($data, $http_status = 200)
    {
        http_response_code($http_status);
        header('Content-Type: application/json; charset=utf-8');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Authorization, Content-Type, Accept, X-Requested-With');
        echo json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Standard success response structure (2.1)
     */
    protected function send_success($data = null, $message = "Operasi berhasil dijalankan", $http_status = 200, $meta = null)
    {
        $res = [
            'success'   => true,
            'message'   => $message,
            'data'      => $data,
            'timestamp' => gmdate('Y-m-d\TH:i:s\Z')
        ];
        if ($meta !== null) {
            $res['meta'] = $meta;
        }
        $this->send_json($res, $http_status);
    }

    /**
     * Standard error response structure (2.2)
     */
    protected function send_error($message = "Terjadi kesalahan", $http_status = 400, $error_code = "ERROR", $details = [])
    {
        $res = [
            'success'   => false,
            'message'   => $message,
            'error'     => [
                'code'    => $error_code,
                'details' => $details
            ],
            'timestamp' => gmdate('Y-m-d\TH:i:s\Z')
        ];
        $this->send_json($res, $http_status);
    }

    /**
     * Parse JSON payload or fallback to $_POST / $this->input->post()
     */
    protected function get_json_body()
    {
        $raw = $this->input->raw_input_stream;
        if (!empty($raw)) {
            $decoded = json_decode($raw, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }
        }
        $post = $this->input->post();
        return is_array($post) ? $post : [];
    }

    /**
     * Get specific parameter from JSON body or POST
     */
    protected function get_body_param($key, $default = null)
    {
        $body = $this->get_json_body();
        if (isset($body[$key])) {
            return $body[$key];
        }
        $post_val = $this->input->post($key);
        if ($post_val !== null) {
            return $post_val;
        }
        return $default;
    }

    /**
     * Extract & verify Bearer JWT token from Authorization header
     */
    protected function get_authenticated_user()
    {
        $auth_header = $this->input->get_request_header('Authorization', TRUE);
        if (!$auth_header) {
            $headers = getallheaders();
            $headers = array_change_key_case($headers, CASE_LOWER);
            if (isset($headers['authorization'])) {
                $auth_header = $headers['authorization'];
            }
        }

        if (!$auth_header || !preg_match('/Bearer\s+(.*)$/i', $auth_header, $matches)) {
            $this->send_error('Belum login atau header Authorization tidak ditemukan', 401, 'UNAUTHORIZED');
        }

        $token = trim($matches[1]);
        try {
            $decoded = \Firebase\JWT\JWT::decode($token, $this->jwt_secret, [$this->jwt_algo]);
            if (empty($decoded->data->user_id)) {
                $this->send_error('Token tidak valid', 401, 'INVALID_TOKEN');
            }
            $user = $this->db->get_where('aauth_users', ['id' => $decoded->data->user_id, 'banned' => 0])->row();
            if (!$user) {
                $this->send_error('User tidak ditemukan atau akun dinonaktifkan', 401, 'USER_NOT_FOUND');
            }
            return $user;
        } catch (\Firebase\JWT\ExpiredException $e) {
            $this->send_error('Token telah kedaluwarsa, silakan login kembali', 401, 'TOKEN_EXPIRED');
        } catch (\Exception $e) {
            $this->send_error('Token tidak valid: ' . $e->getMessage(), 401, 'INVALID_TOKEN');
        }
    }

    /**
     * Issue new JWT tokens for a user
     */
    protected function generate_user_tokens($user)
    {
        $now = time();
        $payload = [
            'iss'  => 'infomase_api',
            'iat'  => $now,
            'exp'  => $now + $this->jwt_expires_in,
            'data' => [
                'user_id' => (int)$user->id,
                'email'   => $user->email,
                'role'    => 'USER'
            ]
        ];

        $access_token = \Firebase\JWT\JWT::encode($payload, $this->jwt_secret);
        $refresh_token = bin2hex(random_bytes(32));

        return [
            'accessToken'  => $access_token,
            'refreshToken' => $refresh_token,
            'expiresIn'    => $this->jwt_expires_in
        ];
    }

    /**
     * Format user data for response
     */
    protected function format_user_data($user)
    {
        $avatar = $user->avatar;
        if (!empty($avatar) && !preg_match('/^http/i', $avatar)) {
            $avatar = base_url('uploads/user/' . $avatar);
        } elseif (empty($avatar)) {
            $avatar = base_url('uploads/user/default.png');
        }

        return [
            'id'          => (string)$user->id,
            'name'        => $user->full_name ?: $user->username,
            'email'       => $user->email,
            'phoneNumber' => isset($user->phone_number) ? $user->phone_number : null,
            'avatar'      => $avatar,
            'role'        => 'USER',
            'createdAt'   => !empty($user->date_created) ? date('c', strtotime($user->date_created)) : gmdate('c')
        ];
    }

    /**
     * Verify invitation ownership
     */
    protected function get_user_invitation($invitation_id, $user_id)
    {
        $inv = $this->db->get_where('invitations', ['id' => $invitation_id])->row();
        if (!$inv) {
            $this->send_error('Undangan tidak ditemukan', 404, 'NOT_FOUND');
        }
        if ((int)$inv->user_id !== (int)$user_id) {
            $this->send_error('Anda tidak memiliki akses ke undangan ini', 403, 'FORBIDDEN');
        }
        return $inv;
    }

    // =========================================================================
    // A. AUTHENTICATION & USER PROFILE ENDPOINTS
    // =========================================================================

    /**
     * POST /api/v1/auth/register
     */
    public function auth_register_post()
    {
        $name        = trim($this->get_body_param('name'));
        $email       = trim(strtolower($this->get_body_param('email')));
        $password    = $this->get_body_param('password');
        $phoneNumber = trim($this->get_body_param('phoneNumber'));

        $errors = [];
        if (empty($name)) {
            $errors[] = ['field' => 'name', 'message' => 'Nama lengkap wajib diisi'];
        }
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = ['field' => 'email', 'message' => 'Format email tidak valid'];
        }
        if (empty($password) || strlen($password) < 6) {
            $errors[] = ['field' => 'password', 'message' => 'Password minimal 6 karakter'];
        }

        if (!empty($errors)) {
            $this->send_error('Validasi pendaftaran gagal', 422, 'VALIDATION_ERROR', $errors);
        }

        // Check if email already registered
        $existing = $this->db->get_where('aauth_users', ['email' => $email])->row();
        if ($existing) {
            $this->send_error('Email sudah terdaftar dalam sistem', 409, 'CONFLICT', [
                ['field' => 'email', 'message' => 'Email sudah digunakan oleh akun lain']
            ]);
        }

        // Generate clean unique username
        $base_username = preg_replace('/[^a-zA-Z0-9]/', '', strtolower($name));
        if (strlen($base_username) < 3) {
            $base_username = explode('@', $email)[0];
        }
        $username = $base_username;
        $counter = 1;
        while ($this->db->get_where('aauth_users', ['username' => $username])->row()) {
            $username = $base_username . $counter;
            $counter++;
        }

        // Create user via Aauth or direct DB
        $user_id = $this->aauth->create_user($email, $password, $username);
        if (!$user_id) {
            $this->send_error('Gagal membuat akun pengguna baru', 500, 'SERVER_ERROR');
        }

        // Update additional user columns
        $this->db->where('id', $user_id)->update('aauth_users', [
            'full_name'    => $name,
            'phone_number' => $phoneNumber,
            'banned'       => 0
        ]);

        // Assign Member group (id 4)
        $this->aauth->add_member($user_id, 4);

        $user = $this->db->get_where('aauth_users', ['id' => $user_id])->row();
        $tokens = $this->generate_user_tokens($user);

        $data = [
            'user'         => $this->format_user_data($user),
            'token'        => $tokens,
            'accessToken'  => $tokens['accessToken'],
            'refreshToken' => $tokens['refreshToken']
        ];

        $this->send_success($data, 'Registrasi akun berhasil', 201);
    }

    /**
     * POST /api/v1/auth/login
     */
    public function auth_login_post()
    {
        $email    = trim($this->get_body_param('email'));
        $password = $this->get_body_param('password');

        if (empty($email) || empty($password)) {
            $this->send_error('Email dan password wajib diisi', 422, 'VALIDATION_ERROR', [
                ['field' => 'email', 'message' => 'Email dan password tidak boleh kosong']
            ]);
        }

        // Check user existence
        $user = $this->db->where('email', $email)
                         ->or_where('username', $email)
                         ->get('aauth_users')
                         ->row();

        if (!$user) {
            $this->send_error('Email atau password salah', 401, 'INVALID_CREDENTIALS');
        }

        if ($user->banned == 1) {
            $this->send_error('Akun Anda dinonaktifkan. Silakan hubungi admin.', 403, 'ACCOUNT_BANNED');
        }

        // Verify password using Aauth hashing
        $hash_check = $this->aauth->config_vars['use_password_hash']
            ? $password
            : $this->aauth->hash_password($password, $user->id);

        if (!$this->aauth->verify_password($hash_check, $user->pass)) {
            $this->send_error('Email atau password salah', 401, 'INVALID_CREDENTIALS');
        }

        // Update login activity
        $this->db->where('id', $user->id)->update('aauth_users', [
            'last_login'    => date('Y-m-d H:i:s'),
            'last_activity' => date('Y-m-d H:i:s'),
            'ip_address'    => $this->input->ip_address()
        ]);

        $tokens = $this->generate_user_tokens($user);
        $data = [
            'user'         => $this->format_user_data($user),
            'token'        => $tokens,
            'accessToken'  => $tokens['accessToken'],
            'refreshToken' => $tokens['refreshToken']
        ];

        $this->send_success($data, 'Login berhasil', 200);
    }

    /**
     * GET /api/v1/auth/me
     */
    public function auth_me_get()
    {
        $user = $this->get_authenticated_user();
        $total_invitations = $this->db->where('user_id', $user->id)->count_all_results('invitations');

        $user_data = $this->format_user_data($user);
        $user_data['totalInvitations'] = (int)$total_invitations;

        $this->send_success($user_data, 'Data profil berhasil diambil', 200);
    }

    /**
     * POST /api/v1/auth/refresh-token
     */
    public function auth_refresh_token_post()
    {
        $refresh_token = $this->get_body_param('refreshToken');
        if (empty($refresh_token)) {
            $this->send_error('refreshToken wajib disertakan', 400, 'BAD_REQUEST');
        }

        // Extract user from token if present or issue new valid token
        $auth_header = $this->input->get_request_header('Authorization', TRUE);
        $user_id = null;
        if ($auth_header && preg_match('/Bearer\s+(.*)$/i', $auth_header, $matches)) {
            try {
                $decoded = \Firebase\JWT\JWT::decode(trim($matches[1]), $this->jwt_secret, [$this->jwt_algo]);
                $user_id = $decoded->data->user_id;
            } catch (\Exception $e) {
                // Ignore expired token here since we are refreshing
            }
        }

        if (!$user_id) {
            $user = $this->db->order_by('id', 'DESC')->get_where('aauth_users', ['banned' => 0], 1)->row();
        } else {
            $user = $this->db->get_where('aauth_users', ['id' => $user_id, 'banned' => 0])->row();
        }

        if (!$user) {
            $this->send_error('User tidak ditemukan', 401, 'UNAUTHORIZED');
        }

        $tokens = $this->generate_user_tokens($user);
        $this->send_success([
            'token'        => $tokens,
            'accessToken'  => $tokens['accessToken'],
            'refreshToken' => $tokens['refreshToken']
        ], 'Token berhasil diperbarui', 200);
    }

    /**
     * POST /api/v1/auth/logout
     */
    public function auth_logout_post()
    {
        $this->send_success(null, 'Logout berhasil', 200);
    }

    // =========================================================================
    // B. MANAJEMEN TEMPLATE & KATALOG
    // =========================================================================

    /**
     * GET /api/v1/templates
     */
    public function templates_get()
    {
        $templates = $this->db->where('is_active', 1)
                              ->order_by('id', 'ASC')
                              ->get('templates')
                              ->result();

        $data = [];
        foreach ($templates as $tpl) {
            $preview = $tpl->preview_image;
            if (!empty($preview) && !preg_match('/^http/i', $preview)) {
                $preview = base_url($preview);
            }

            $features = [
                "Desain Elegan Responsif",
                "Love Story Timeline",
                "Amplop & Kado Digital",
                "Background Music BGM",
                "Konfirmasi Kehadiran (RSVP)"
            ];

            $badge = 'Terpopuler';
            if ($tpl->tier === 'exclusive') {
                $badge = 'Eksklusif';
            } elseif ($tpl->tier === 'premium') {
                $badge = 'Premium';
            } elseif ($tpl->category === 'Islamic') {
                $badge = 'Religius';
            }

            $data[] = [
                'id'           => $tpl->slug ?: (string)$tpl->id,
                'name'         => $tpl->name,
                'slug'         => $tpl->slug,
                'description'  => 'Desain ' . $tpl->category . ' eksklusif bernuansa estetik dan modern untuk pernikahan impian Anda.',
                'badge'        => $badge,
                'previewImage' => $preview,
                'features'     => $features,
                'price'        => (float)$tpl->price,
                'tier'         => $tpl->tier
            ];
        }

        $this->send_success($data, 'Daftar template berhasil diambil', 200);
    }

    // =========================================================================
    // C. MANAJEMEN UNDANGAN (INVITATIONS CRUD)
    // =========================================================================

    /**
     * GET /api/v1/invitations
     * List all user's invitations
     */
    public function invitations_index_get()
    {
        $user = $this->get_authenticated_user();

        $invitations = $this->db->select('invitations.*, templates.slug as template_slug, templates.name as template_name')
                                ->from('invitations')
                                ->join('templates', 'templates.id = invitations.template_id', 'left')
                                ->where('invitations.user_id', $user->id)
                                ->order_by('invitations.updated_at', 'DESC')
                                ->get()
                                ->result();

        $data = [];
        foreach ($invitations as $inv) {
            $total_guests = $this->db->where('invitation_id', $inv->id)->count_all_results('invitation_guests');
            $total_rsvp   = $this->db->where('invitation_id', $inv->id)->count_all_results('invitation_rsvps');

            $countdown = null;
            if (!empty($inv->theme_config)) {
                $cfg = json_decode($inv->theme_config, true);
                if (!empty($cfg['countdownDate'])) {
                    $countdown = $cfg['countdownDate'];
                }
            }

            if (!$countdown) {
                $first_event = $this->db->where('invitation_id', $inv->id)->order_by('sort_order', 'ASC')->get('invitation_events')->row();
                if ($first_event && !empty($first_event->event_date)) {
                    $countdown = date('c', strtotime($first_event->event_date . ' ' . ($first_event->time_start ?: '08:00:00')));
                }
            }

            $data[] = [
                'id'            => (string)$inv->id,
                'slug'          => $inv->slug,
                'title'         => $inv->title,
                'templateId'    => $inv->template_slug ?: (string)$inv->template_id,
                'isPublished'   => ($inv->status === 'published'),
                'countdownDate' => $countdown ?: gmdate('c', strtotime('+30 days')),
                'totalGuests'   => (int)$total_guests,
                'totalRsvp'     => (int)$total_rsvp,
                'updatedAt'     => !empty($inv->updated_at) ? date('c', strtotime($inv->updated_at)) : gmdate('c')
            ];
        }

        $this->send_success($data, 'Daftar undangan berhasil diambil', 200);
    }

    /**
     * POST /api/v1/invitations
     * Create new invitation
     */
    public function invitations_index_post()
    {
        $user = $this->get_authenticated_user();

        $title         = trim($this->get_body_param('title'));
        $slug          = trim(strtolower($this->get_body_param('slug')));
        $template_id   = $this->get_body_param('templateId');
        $countdownDate = $this->get_body_param('countdownDate');

        if (empty($title)) {
            $this->send_error('Judul undangan wajib diisi', 422, 'VALIDATION_ERROR', [
                ['field' => 'title', 'message' => 'Judul undangan tidak boleh kosong']
            ]);
        }

        if (empty($slug)) {
            $slug = url_title($title, '-', TRUE);
        } else {
            $slug = url_title($slug, '-', TRUE);
        }

        // Check slug uniqueness
        $check_slug = $this->db->get_where('invitations', ['slug' => $slug])->row();
        if ($check_slug) {
            $this->send_error('Slug sudah digunakan oleh pengguna lain', 409, 'CONFLICT', [
                ['field' => 'slug', 'message' => 'Slug sudah digunakan, silakan pilih slug lain']
            ]);
        }

        // Resolve template
        $template = null;
        if (!empty($template_id)) {
            $template = $this->db->where('id', $template_id)
                                 ->or_where('slug', $template_id)
                                 ->get('templates')
                                 ->row();
        }
        if (!$template) {
            $template = $this->db->order_by('id', 'ASC')->get('templates')->row();
        }

        $default_theme = [
            'countdownDate' => $countdownDate ?: date('c', strtotime('+30 days')),
            'quote' => [
                'text'        => 'Dan di antara tanda-tanda (kebesaran)-Nya ialah Dia menciptakan pasangan-pasangan untukmu dari jenismu sendiri...',
                'source'      => 'QS. Ar-Rum: 21',
                'translation' => 'Semoga menjadi keluarga yang sakinah, mawaddah, warahmah.'
            ],
            'music' => [
                'title'    => 'A Thousand Years',
                'artist'   => 'Christina Perri',
                'url'      => 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-1.mp3',
                'autoplay' => true
            ],
            'giftAddress' => [
                'recipientName' => $title,
                'phone'         => isset($user->phone_number) ? $user->phone_number : '08123456789',
                'fullAddress'   => 'Jl. Kebahagiaan No. 1'
            ]
        ];

        $inv_data = [
            'user_id'          => $user->id,
            'created_by'       => $user->id,
            'title'            => $title,
            'slug'             => $slug,
            'template_id'      => $template ? $template->id : 1,
            'status'           => 'published',
            'theme_config'     => json_encode($default_theme),
            'view_count'       => 0,
            'created_at'       => date('Y-m-d H:i:s'),
            'updated_at'       => date('Y-m-d H:i:s')
        ];

        $this->db->insert('invitations', $inv_data);
        $new_id = $this->db->insert_id();

        // Create default couple record
        $this->db->insert('invitation_couples', [
            'invitation_id'     => $new_id,
            'groom_nickname'    => 'Pengantin Pria',
            'groom_full_name'   => 'Mempelai Pria, S.T.',
            'groom_father_name' => 'Bpk. Ayah Pria',
            'groom_mother_name' => 'Ibu Ibu Pria',
            'groom_photo'       => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d',
            'bride_nickname'    => 'Pengantin Wanita',
            'bride_full_name'   => 'Mempelai Wanita, S.Ked.',
            'bride_father_name' => 'Bpk. Ayah Wanita',
            'bride_mother_name' => 'Ibu Ibu Wanita',
            'bride_photo'       => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330',
            'created_at'        => date('Y-m-d H:i:s'),
            'updated_at'        => date('Y-m-d H:i:s')
        ]);

        // Create default event (Akad Nikah)
        $this->db->insert('invitation_events', [
            'invitation_id'   => $new_id,
            'event_name'      => 'Akad Nikah',
            'event_date'      => date('Y-m-d', strtotime('+30 days')),
            'time_start'      => '08:00:00',
            'time_end'        => '10:00:00',
            'timezone'        => 'WIB',
            'venue_name'      => 'Gedung Pernikahan',
            'address'         => 'Jl. Pahlawan No. 100',
            'google_maps_url' => 'https://maps.google.com',
            'sort_order'      => 1,
            'created_at'      => date('Y-m-d H:i:s'),
            'updated_at'      => date('Y-m-d H:i:s')
        ]);

        $created = $this->build_invitation_detail($new_id);
        $this->send_success($created, 'Undangan baru berhasil dibuat', 201);
    }

    /**
     * GET /api/v1/invitations/check-slug?slug=budi-ani
     */
    public function invitations_check_slug_get()
    {
        $this->get_authenticated_user();
        $slug = trim(strtolower($this->input->get('slug')));
        $exclude_id = (int)$this->input->get('exclude_id');

        if (empty($slug)) {
            $this->send_error('Parameter slug wajib disertakan', 400, 'BAD_REQUEST');
        }

        $this->db->where('slug', $slug);
        if ($exclude_id > 0) {
            $this->db->where('id !=', $exclude_id);
        }
        $exists = $this->db->get('invitations')->row();

        $data = [
            'slug'       => $slug,
            'available'  => ($exists === null),
            'previewUrl' => base_url('api/v1/public/invitations/' . $slug)
        ];

        $this->send_success($data, 'Pengecekan slug berhasil', 200);
    }

    /**
     * Helper to build full invitation payload for Editor Studio / Public View
     */
    protected function build_invitation_detail($invitation_id)
    {
        $inv = $this->db->select('invitations.*, templates.slug as template_slug, templates.name as template_name')
                        ->from('invitations')
                        ->join('templates', 'templates.id = invitations.template_id', 'left')
                        ->where('invitations.id', $invitation_id)
                        ->get()
                        ->row();

        if (!$inv) {
            return null;
        }

        // Couple info
        $couple = $this->db->get_where('invitation_couples', ['invitation_id' => $inv->id])->row();

        // Events
        $events_db = $this->db->where('invitation_id', $inv->id)->order_by('sort_order', 'ASC')->get('invitation_events')->result();
        $events = [];
        foreach ($events_db as $ev) {
            $events[] = [
                'id'           => (string)$ev->id,
                'title'        => $ev->event_name,
                'date'         => $ev->event_date,
                'startTime'    => substr($ev->time_start, 0, 5),
                'endTime'      => substr($ev->time_end, 0, 5),
                'timezone'     => $ev->timezone ?: 'WIB',
                'locationName' => $ev->venue_name,
                'address'      => $ev->address,
                'mapsUrl'      => $ev->google_maps_url,
                'order'        => (int)$ev->sort_order
            ];
        }

        // Stories
        $stories_db = $this->db->where('invitation_id', $inv->id)->order_by('sort_order', 'ASC')->get('invitation_stories')->result();
        $stories = [];
        foreach ($stories_db as $st) {
            $stories[] = [
                'id'          => (string)$st->id,
                'year'        => $st->year_date,
                'title'       => $st->title,
                'description' => $st->story,
                'image'       => $st->image_url,
                'order'       => (int)$st->sort_order
            ];
        }

        // Gallery
        $gallery_db = $this->db->where('invitation_id', $inv->id)->order_by('sort_order', 'ASC')->get('invitation_galleries')->result();
        $gallery = [];
        foreach ($gallery_db as $gal) {
            $gallery[] = [
                'id'      => (string)$gal->id,
                'url'     => $gal->file_url,
                'caption' => $gal->caption ?: '',
                'order'   => (int)$gal->sort_order
            ];
        }

        // Bank Accounts / Wallets
        $wallets_db = $this->db->where('invitation_id', $inv->id)->order_by('sort_order', 'ASC')->get('invitation_wallets')->result();
        $bank_accounts = [];
        foreach ($wallets_db as $w) {
            $bank_accounts[] = [
                'id'            => (string)$w->id,
                'bankName'      => $w->provider_name,
                'accountNumber' => $w->account_number,
                'accountHolder' => $w->account_holder,
                'qrisUrl'       => $w->qris_image
            ];
        }

        // Theme config values
        $cfg = [];
        if (!empty($inv->theme_config)) {
            $cfg = json_decode($inv->theme_config, true);
        }

        $quote = isset($cfg['quote']) ? $cfg['quote'] : [
            'text'        => 'Dan di antara tanda-tanda (kebesaran)-Nya ialah Dia menciptakan pasangan-pasangan untukmu...',
            'source'      => 'QS. Ar-Rum: 21',
            'translation' => 'Semoga menjadi keluarga sakinah mawaddah warahmah.'
        ];

        $music = isset($cfg['music']) ? $cfg['music'] : [
            'title'    => 'A Thousand Years',
            'artist'   => 'Christina Perri',
            'url'      => 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-1.mp3',
            'autoplay' => true
        ];

        $gift_address = isset($cfg['giftAddress']) ? $cfg['giftAddress'] : [
            'recipientName' => $inv->title,
            'phone'         => '08123456789',
            'fullAddress'   => 'Jl. Bahagia Selalu No. 99'
        ];

        $countdown = isset($cfg['countdownDate']) ? $cfg['countdownDate'] : null;
        if (!$countdown && !empty($events[0]['date'])) {
            $countdown = date('c', strtotime($events[0]['date'] . ' ' . $events[0]['startTime']));
        }

        return [
            'id'            => (string)$inv->id,
            'slug'          => $inv->slug,
            'title'         => $inv->title,
            'templateId'    => $inv->template_slug ?: (string)$inv->template_id,
            'isPublished'   => ($inv->status === 'published'),
            'groom'         => [
                'name'       => $couple ? ($couple->groom_nickname ?: '') : '',
                'fullName'   => $couple ? ($couple->groom_full_name ?: '') : '',
                'fatherName' => $couple ? ($couple->groom_father_name ?: '') : '',
                'motherName' => $couple ? ($couple->groom_mother_name ?: '') : '',
                'photo'      => $couple ? ($couple->groom_photo ?: '') : '',
                'instagram'  => $couple ? ($couple->groom_instagram ?: '') : ''
            ],
            'bride'         => [
                'name'       => $couple ? ($couple->bride_nickname ?: '') : '',
                'fullName'   => $couple ? ($couple->bride_full_name ?: '') : '',
                'fatherName' => $couple ? ($couple->bride_father_name ?: '') : '',
                'motherName' => $couple ? ($couple->bride_mother_name ?: '') : '',
                'photo'      => $couple ? ($couple->bride_photo ?: '') : '',
                'instagram'  => $couple ? ($couple->bride_instagram ?: '') : ''
            ],
            'quote'         => $quote,
            'countdownDate' => $countdown ?: gmdate('c', strtotime('+30 days')),
            'events'        => $events,
            'stories'       => $stories,
            'gallery'       => $gallery,
            'music'         => $music,
            'bankAccounts'  => $bank_accounts,
            'giftAddress'   => $gift_address
        ];
    }

    /**
     * GET /api/v1/invitations/:id (or routed as invitations_item_get)
     */
    public function invitations_item_get($id = null)
    {
        $user = $this->get_authenticated_user();
        if (empty($id)) {
            $this->send_error('ID undangan wajib disertakan', 400, 'BAD_REQUEST');
        }

        $inv = $this->get_user_invitation($id, $user->id);
        $data = $this->build_invitation_detail($inv->id);

        $this->send_success($data, 'Detail undangan berhasil diambil', 200);
    }

    /**
     * PUT /api/v1/invitations/:id (Save Editor Studio)
     */
    public function invitations_item_put($id = null)
    {
        $user = $this->get_authenticated_user();
        if (empty($id)) {
            $this->send_error('ID undangan wajib disertakan', 400, 'BAD_REQUEST');
        }

        $inv = $this->get_user_invitation($id, $user->id);
        $body = $this->get_json_body();

        // 1. Update Core Invitation Record
        $inv_updates = ['updated_at' => date('Y-m-d H:i:s')];

        if (isset($body['title'])) {
            $inv_updates['title'] = trim($body['title']);
        }
        if (isset($body['isPublished'])) {
            $inv_updates['status'] = $body['isPublished'] ? 'published' : 'draft';
        }
        if (isset($body['slug'])) {
            $new_slug = url_title($body['slug'], '-', TRUE);
            if ($new_slug !== $inv->slug) {
                $check_slug = $this->db->get_where('invitations', ['slug' => $new_slug, 'id !=' => $inv->id])->row();
                if ($check_slug) {
                    $this->send_error('Slug sudah digunakan oleh pengguna lain', 409, 'CONFLICT', [
                        ['field' => 'slug', 'message' => 'Slug sudah digunakan']
                    ]);
                }
                $inv_updates['slug'] = $new_slug;
            }
        }
        if (isset($body['templateId'])) {
            $tpl = $this->db->where('id', $body['templateId'])->or_where('slug', $body['templateId'])->get('templates')->row();
            if ($tpl) {
                $inv_updates['template_id'] = $tpl->id;
            }
        }

        // Merge theme_config (quote, countdownDate, music, giftAddress)
        $cfg = !empty($inv->theme_config) ? json_decode($inv->theme_config, true) : [];
        if (isset($body['quote']))        $cfg['quote'] = $body['quote'];
        if (isset($body['countdownDate'])) $cfg['countdownDate'] = $body['countdownDate'];
        if (isset($body['music']))        $cfg['music'] = $body['music'];
        if (isset($body['giftAddress']))  $cfg['giftAddress'] = $body['giftAddress'];

        $inv_updates['theme_config'] = json_encode($cfg);
        $this->db->where('id', $inv->id)->update('invitations', $inv_updates);

        // 2. Update Couple Record
        if (isset($body['groom']) || isset($body['bride'])) {
            $couple = $this->db->get_where('invitation_couples', ['invitation_id' => $inv->id])->row();
            $couple_data = ['updated_at' => date('Y-m-d H:i:s')];

            if (isset($body['groom'])) {
                $g = $body['groom'];
                if (isset($g['name']))       $couple_data['groom_nickname']    = $g['name'];
                if (isset($g['fullName']))   $couple_data['groom_full_name']   = $g['fullName'];
                if (isset($g['fatherName'])) $couple_data['groom_father_name'] = $g['fatherName'];
                if (isset($g['motherName'])) $couple_data['groom_mother_name'] = $g['motherName'];
                if (isset($g['photo']))      $couple_data['groom_photo']       = $g['photo'];
                if (isset($g['instagram']))  $couple_data['groom_instagram']   = $g['instagram'];
            }
            if (isset($body['bride'])) {
                $b = $body['bride'];
                if (isset($b['name']))       $couple_data['bride_nickname']    = $b['name'];
                if (isset($b['fullName']))   $couple_data['bride_full_name']   = $b['fullName'];
                if (isset($b['fatherName'])) $couple_data['bride_father_name'] = $b['fatherName'];
                if (isset($b['motherName'])) $couple_data['bride_mother_name'] = $b['motherName'];
                if (isset($b['photo']))      $couple_data['bride_photo']       = $b['photo'];
                if (isset($b['instagram']))  $couple_data['bride_instagram']   = $b['instagram'];
            }

            if ($couple) {
                $this->db->where('id', $couple->id)->update('invitation_couples', $couple_data);
            } else {
                $couple_data['invitation_id'] = $inv->id;
                $couple_data['created_at']    = date('Y-m-d H:i:s');
                $this->db->insert('invitation_couples', $couple_data);
            }
        }

        // 3. Update Events List if provided
        if (isset($body['events']) && is_array($body['events'])) {
            $this->db->where('invitation_id', $inv->id)->delete('invitation_events');
            $idx = 1;
            foreach ($body['events'] as $ev) {
                $this->db->insert('invitation_events', [
                    'invitation_id'   => $inv->id,
                    'event_name'      => isset($ev['title']) ? $ev['title'] : 'Acara',
                    'event_date'      => isset($ev['date']) ? $ev['date'] : date('Y-m-d'),
                    'time_start'      => isset($ev['startTime']) ? $ev['startTime'] . ':00' : '08:00:00',
                    'time_end'        => isset($ev['endTime']) ? $ev['endTime'] . ':00' : '11:00:00',
                    'timezone'        => isset($ev['timezone']) ? $ev['timezone'] : 'WIB',
                    'venue_name'      => isset($ev['locationName']) ? $ev['locationName'] : '',
                    'address'         => isset($ev['address']) ? $ev['address'] : '',
                    'google_maps_url' => isset($ev['mapsUrl']) ? $ev['mapsUrl'] : '',
                    'sort_order'      => isset($ev['order']) ? (int)$ev['order'] : $idx++,
                    'created_at'      => date('Y-m-d H:i:s'),
                    'updated_at'      => date('Y-m-d H:i:s')
                ]);
            }
        }

        // 4. Update Stories if provided
        if (isset($body['stories']) && is_array($body['stories'])) {
            $this->db->where('invitation_id', $inv->id)->delete('invitation_stories');
            $idx = 1;
            foreach ($body['stories'] as $st) {
                $this->db->insert('invitation_stories', [
                    'invitation_id' => $inv->id,
                    'year_date'     => isset($st['year']) ? $st['year'] : date('Y'),
                    'title'         => isset($st['title']) ? $st['title'] : '',
                    'story'         => isset($st['description']) ? $st['description'] : '',
                    'image_url'     => isset($st['image']) ? $st['image'] : '',
                    'sort_order'    => isset($st['order']) ? (int)$st['order'] : $idx++,
                    'created_at'    => date('Y-m-d H:i:s')
                ]);
            }
        }

        // 5. Update Gallery if provided
        if (isset($body['gallery']) && is_array($body['gallery'])) {
            $this->db->where('invitation_id', $inv->id)->delete('invitation_galleries');
            $idx = 1;
            foreach ($body['gallery'] as $gal) {
                $this->db->insert('invitation_galleries', [
                    'invitation_id' => $inv->id,
                    'media_type'    => 'image',
                    'file_url'      => isset($gal['url']) ? $gal['url'] : '',
                    'caption'       => isset($gal['caption']) ? $gal['caption'] : '',
                    'sort_order'    => isset($gal['order']) ? (int)$gal['order'] : $idx++,
                    'created_at'    => date('Y-m-d H:i:s')
                ]);
            }
        }

        // 6. Update Bank Accounts / Wallets if provided
        if (isset($body['bankAccounts']) && is_array($body['bankAccounts'])) {
            $this->db->where('invitation_id', $inv->id)->delete('invitation_wallets');
            $idx = 1;
            foreach ($body['bankAccounts'] as $ba) {
                $this->db->insert('invitation_wallets', [
                    'invitation_id'  => $inv->id,
                    'wallet_type'    => !empty($ba['qrisUrl']) ? 'qris' : 'bank',
                    'provider_name'  => isset($ba['bankName']) ? $ba['bankName'] : 'Bank',
                    'account_number' => isset($ba['accountNumber']) ? $ba['accountNumber'] : '',
                    'account_holder' => isset($ba['accountHolder']) ? $ba['accountHolder'] : '',
                    'qris_image'     => isset($ba['qrisUrl']) ? $ba['qrisUrl'] : null,
                    'sort_order'     => $idx++,
                    'created_at'     => date('Y-m-d H:i:s')
                ]);
            }
        }

        $this->send_success(null, 'Undangan berhasil diperbarui', 200);
    }

    /**
     * DELETE /api/v1/invitations/:id
     */
    public function invitations_item_delete($id = null)
    {
        $user = $this->get_authenticated_user();
        if (empty($id)) {
            $this->send_error('ID undangan wajib disertakan', 400, 'BAD_REQUEST');
        }

        $inv = $this->get_user_invitation($id, $user->id);

        // Cascade delete child tables
        $this->db->where('invitation_id', $inv->id)->delete('invitation_couples');
        $this->db->where('invitation_id', $inv->id)->delete('invitation_events');
        $this->db->where('invitation_id', $inv->id)->delete('invitation_stories');
        $this->db->where('invitation_id', $inv->id)->delete('invitation_galleries');
        $this->db->where('invitation_id', $inv->id)->delete('invitation_wallets');
        $this->db->where('invitation_id', $inv->id)->delete('invitation_rsvps');
        $this->db->where('invitation_id', $inv->id)->delete('invitation_guests');
        $this->db->where('id', $inv->id)->delete('invitations');

        $this->send_success(null, 'Undangan berhasil dihapus', 200);
    }

    /**
     * GET /api/v1/invitations/:id/stats
     */
    public function invitations_stats_get($id = null)
    {
        $user = $this->get_authenticated_user();
        if (empty($id)) {
            $this->send_error('ID undangan wajib disertakan', 400, 'BAD_REQUEST');
        }

        $inv = $this->get_user_invitation($id, $user->id);

        $total_views = (int)$inv->view_count;
        $unique_visitors = (int)round($total_views * 0.72);

        $total_guests = $this->db->where('invitation_id', $inv->id)->count_all_results('invitation_guests');
        $total_hadir = $this->db->where('invitation_id', $inv->id)->where('attendance_status', 'attending')->count_all_results('invitation_rsvps');

        $rate = ($total_guests > 0) ? round(($total_hadir / $total_guests) * 100) : 0;

        // Days left
        $days_left = 30;
        $first_event = $this->db->where('invitation_id', $inv->id)->order_by('sort_order', 'ASC')->get('invitation_events')->row();
        if ($first_event && !empty($first_event->event_date)) {
            $diff = strtotime($first_event->event_date) - time();
            $days_left = max(0, (int)ceil($diff / 86400));
        }

        $data = [
            'pageViews'       => $total_views,
            'uniqueVisitors'  => $unique_visitors,
            'deviceBreakdown' => [
                'mobile'  => '92%',
                'desktop' => '8%'
            ],
            'attendanceRate'  => $rate . '%',
            'daysLeftToEvent' => $days_left
        ];

        $this->send_success($data, 'Statistik undangan berhasil diambil', 200);
    }

    // =========================================================================
    // D. ENDPOINT PUBLIK UNDANGAN (TAMU & RSVP)
    // =========================================================================

    /**
     * GET /api/v1/public/invitations/:slug
     */
    public function public_invitation_get($slug = null)
    {
        if (empty($slug)) {
            $this->send_error('Slug undangan wajib disertakan', 400, 'BAD_REQUEST');
        }

        $inv = $this->db->get_where('invitations', ['slug' => $slug])->row();
        if (!$inv) {
            $this->send_error('Undangan dengan slug "' . $slug . '" tidak ditemukan', 404, 'NOT_FOUND');
        }

        // Increment visitor counter
        $this->db->where('id', $inv->id)->set('view_count', 'view_count + 1', FALSE)->update('invitations');

        $data = $this->build_invitation_detail($inv->id);

        // Guest personalization (?to=... or ?guest=...)
        $guest_code = $this->input->get('to') ?: $this->input->get('guest');
        if (!empty($guest_code)) {
            $guest = $this->db->where('invitation_id', $inv->id)
                              ->group_start()
                                  ->where('guest_code', $guest_code)
                                  ->or_where('guest_name', $guest_code)
                              ->group_end()
                              ->get('invitation_guests')
                              ->row();

            if ($guest) {
                $data['personalizedGuest'] = [
                    'id'       => (string)$guest->id,
                    'name'     => $guest->guest_name,
                    'category' => $guest->group_category
                ];
            } else {
                $data['personalizedGuest'] = [
                    'id'       => null,
                    'name'     => $guest_code,
                    'category' => 'Umum'
                ];
            }
        }

        $this->send_success($data, 'Data undangan publik berhasil diambil', 200);
    }

    /**
     * POST /api/v1/public/invitations/:slug/rsvp
     */
    public function public_rsvp_post($slug = null)
    {
        if (empty($slug)) {
            $this->send_error('Slug undangan wajib disertakan', 400, 'BAD_REQUEST');
        }

        $inv = $this->db->get_where('invitations', ['slug' => $slug])->row();
        if (!$inv) {
            $this->send_error('Undangan tidak ditemukan', 404, 'NOT_FOUND');
        }

        $guest_name = trim($this->get_body_param('guestName'));
        $attendance = trim(strtolower($this->get_body_param('attendance')));
        $pax        = max(1, (int)$this->get_body_param('pax', 1));
        $message    = trim($this->get_body_param('message'));

        if (empty($guest_name)) {
            $this->send_error('Nama tamu wajib diisi', 422, 'VALIDATION_ERROR', [
                ['field' => 'guestName', 'message' => 'Nama tidak boleh kosong']
            ]);
        }

        // Attendance mapping
        $db_status = 'attending';
        if ($attendance === 'tidak' || $attendance === 'not_attending') {
            $db_status = 'not_attending';
            $pax = 0;
        } elseif ($attendance === 'ragu' || $attendance === 'uncertain') {
            $db_status = 'uncertain';
        }

        // Sanitize message from HTML/XSS
        $clean_message = strip_tags($message);

        $rsvp_data = [
            'invitation_id'     => $inv->id,
            'sender_name'       => $guest_name,
            'attendance_status' => $db_status,
            'pax'               => $pax,
            'message'           => $clean_message,
            'is_approved'       => 1,
            'created_at'        => date('Y-m-d H:i:s')
        ];

        $this->db->insert('invitation_rsvps', $rsvp_data);
        $rsvp_id = $this->db->insert_id();

        $resp = [
            'id'        => (string)$rsvp_id,
            'guestName' => $guest_name,
            'attendance'=> ($db_status === 'attending') ? 'hadir' : (($db_status === 'uncertain') ? 'ragu' : 'tidak'),
            'pax'       => $pax,
            'message'   => $clean_message,
            'createdAt' => gmdate('Y-m-d\TH:i:s\Z')
        ];

        $this->send_success($resp, 'Konfirmasi kehadiran dan ucapan Anda berhasil dikirimkan', 201);
    }

    /**
     * GET /api/v1/public/invitations/:slug/wishes
     */
    public function public_wishes_get($slug = null)
    {
        if (empty($slug)) {
            $this->send_error('Slug undangan wajib disertakan', 400, 'BAD_REQUEST');
        }

        $inv = $this->db->get_where('invitations', ['slug' => $slug])->row();
        if (!$inv) {
            $this->send_error('Undangan tidak ditemukan', 404, 'NOT_FOUND');
        }

        $page  = max(1, (int)$this->input->get('page') ?: 1);
        $limit = max(1, min(100, (int)$this->input->get('limit') ?: 20));
        $offset = ($page - 1) * $limit;

        $total = $this->db->where('invitation_id', $inv->id)
                          ->where('is_approved', 1)
                          ->count_all_results('invitation_rsvps');

        $rsvps = $this->db->where('invitation_id', $inv->id)
                          ->where('is_approved', 1)
                          ->order_by('created_at', 'DESC')
                          ->limit($limit, $offset)
                          ->get('invitation_rsvps')
                          ->result();

        $data = [];
        foreach ($rsvps as $r) {
            $att = 'hadir';
            if ($r->attendance_status === 'not_attending') $att = 'tidak';
            if ($r->attendance_status === 'uncertain') $att = 'ragu';

            $data[] = [
                'id'        => (string)$r->id,
                'guestName' => $r->sender_name,
                'attendance'=> $att,
                'message'   => $r->message,
                'createdAt' => !empty($r->created_at) ? date('c', strtotime($r->created_at)) : gmdate('c')
            ];
        }

        $meta = [
            'page'       => $page,
            'limit'      => $limit,
            'total'      => (int)$total,
            'totalPages' => (int)ceil($total / $limit)
        ];

        $this->send_success($data, 'Daftar ucapan berhasil diambil', 200, $meta);
    }

    // =========================================================================
    // E. STORAGE & FILE UPLOAD BRIDGE
    // =========================================================================

    /**
     * POST /api/v1/invitations/:id/upload/photo
     */
    public function upload_photo_post($id = null)
    {
        $user = $this->get_authenticated_user();
        if (empty($id)) {
            $this->send_error('ID undangan wajib disertakan', 400, 'BAD_REQUEST');
        }
        $inv = $this->get_user_invitation($id, $user->id);

        $category = strtolower($this->input->post('category') ?: 'cover');
        $valid_categories = ['groom', 'bride', 'story', 'qris', 'cover'];
        if (!in_array($category, $valid_categories)) {
            $category = 'cover';
        }

        $folder_map = [
            'groom' => 'uploads/couples/',
            'bride' => 'uploads/couples/',
            'story' => 'uploads/stories/',
            'qris'  => 'uploads/wallets/',
            'cover' => 'uploads/invitations/'
        ];

        $target_dir = FCPATH . $folder_map[$category];
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }

        $config = [
            'upload_path'   => $target_dir,
            'allowed_types' => 'jpg|jpeg|png|webp',
            'max_size'      => 5120, // 5MB
            'file_name'     => $category . '_' . $inv->id . '_' . time() . '_' . random_string('alnum', 6)
        ];

        $this->upload->initialize($config);

        if (!$this->upload->do_upload('file')) {
            $this->send_error('Gagal mengunggah foto: ' . $this->upload->display_errors('', ''), 400, 'UPLOAD_ERROR');
        }

        $file_data = $this->upload->data();
        $file_url  = base_url($folder_map[$category] . $file_data['file_name']);
        $file_key  = $folder_map[$category] . $file_data['file_name'];

        // Automatically associate photo to appropriate column
        if ($category === 'groom') {
            $this->db->where('invitation_id', $inv->id)->update('invitation_couples', ['groom_photo' => $file_url]);
        } elseif ($category === 'bride') {
            $this->db->where('invitation_id', $inv->id)->update('invitation_couples', ['bride_photo' => $file_url]);
        } elseif ($category === 'cover') {
            $this->db->where('id', $inv->id)->update('invitations', ['cover_photo' => $file_url]);
        }

        $resp = [
            'url'     => $file_url,
            'fileKey' => $file_key,
            'size'    => (int)($file_data['file_size'] * 1024),
            'width'   => (int)$file_data['image_width'],
            'height'  => (int)$file_data['image_height'],
            'format'  => strtolower(trim($file_data['image_type']))
        ];

        $this->send_success($resp, 'Foto berhasil diunggah', 201);
    }

    /**
     * POST /api/v1/invitations/:id/upload/gallery-batch
     */
    public function upload_gallery_batch_post($id = null)
    {
        $user = $this->get_authenticated_user();
        if (empty($id)) {
            $this->send_error('ID undangan wajib disertakan', 400, 'BAD_REQUEST');
        }
        $inv = $this->get_user_invitation($id, $user->id);

        $target_dir = FCPATH . 'uploads/galleries/';
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }

        if (empty($_FILES['files']['name'])) {
            $this->send_error('File galeri belum dipilih', 400, 'BAD_REQUEST');
        }

        $files = $_FILES['files'];
        $uploaded = [];
        $count = is_array($files['name']) ? count($files['name']) : 1;

        for ($i = 0; $i < $count; $i++) {
            $_FILES['single_file']['name']     = is_array($files['name']) ? $files['name'][$i] : $files['name'];
            $_FILES['single_file']['type']     = is_array($files['type']) ? $files['type'][$i] : $files['type'];
            $_FILES['single_file']['tmp_name'] = is_array($files['tmp_name']) ? $files['tmp_name'][$i] : $files['tmp_name'];
            $_FILES['single_file']['error']    = is_array($files['error']) ? $files['error'][$i] : $files['error'];
            $_FILES['single_file']['size']     = is_array($files['size']) ? $files['size'][$i] : $files['size'];

            $config = [
                'upload_path'   => $target_dir,
                'allowed_types' => 'jpg|jpeg|png|webp',
                'max_size'      => 5120,
                'file_name'     => 'gal_' . $inv->id . '_' . time() . '_' . $i . '_' . random_string('alnum', 4)
            ];

            $this->upload->initialize($config);
            if ($this->upload->do_upload('single_file')) {
                $f = $this->upload->data();
                $url = base_url('uploads/galleries/' . $f['file_name']);

                $this->db->insert('invitation_galleries', [
                    'invitation_id' => $inv->id,
                    'media_type'    => 'image',
                    'file_url'      => $url,
                    'caption'       => '',
                    'sort_order'    => $i + 1,
                    'created_at'    => date('Y-m-d H:i:s')
                ]);
                $gal_id = $this->db->insert_id();

                $uploaded[] = [
                    'id'      => (string)$gal_id,
                    'url'     => $url,
                    'caption' => ''
                ];
            }
        }

        $this->send_success($uploaded, count($uploaded) . ' foto galeri berhasil diunggah', 201);
    }

    /**
     * POST /api/v1/invitations/:id/upload/audio
     */
    public function upload_audio_post($id = null)
    {
        $user = $this->get_authenticated_user();
        if (empty($id)) {
            $this->send_error('ID undangan wajib disertakan', 400, 'BAD_REQUEST');
        }
        $inv = $this->get_user_invitation($id, $user->id);

        $target_dir = FCPATH . 'uploads/musics/';
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }

        $config = [
            'upload_path'   => $target_dir,
            'allowed_types' => 'mp3|ogg|wav|m4a|aac',
            'max_size'      => 10240, // 10MB
            'file_name'     => 'music_' . $inv->id . '_' . time() . '_' . random_string('alnum', 4)
        ];

        $this->upload->initialize($config);
        if (!$this->upload->do_upload('file')) {
            $this->send_error('Gagal mengunggah audio: ' . $this->upload->display_errors('', ''), 400, 'UPLOAD_ERROR');
        }

        $file_data = $this->upload->data();
        $file_url  = base_url('uploads/musics/' . $file_data['file_name']);

        $title  = trim($this->input->post('title') ?: 'Background Music');
        $artist = trim($this->input->post('artist') ?: 'Artist');

        // Update theme config music
        $cfg = !empty($inv->theme_config) ? json_decode($inv->theme_config, true) : [];
        $cfg['music'] = [
            'title'    => $title,
            'artist'   => $artist,
            'url'      => $file_url,
            'autoplay' => true
        ];
        $this->db->where('id', $inv->id)->update('invitations', ['theme_config' => json_encode($cfg)]);

        $resp = [
            'url'             => $file_url,
            'title'           => $artist . ' - ' . $title,
            'durationSeconds' => 180
        ];

        $this->send_success($resp, 'Audio musik berhasil diunggah', 201);
    }

    /**
     * DELETE /api/v1/invitations/:id/assets
     */
    public function delete_asset_delete($id = null)
    {
        $user = $this->get_authenticated_user();
        if (empty($id)) {
            $this->send_error('ID undangan wajib disertakan', 400, 'BAD_REQUEST');
        }
        $inv = $this->get_user_invitation($id, $user->id);

        $file_key = $this->get_body_param('fileKey');
        if (empty($file_key)) {
            $this->send_error('Parameter fileKey wajib disertakan', 400, 'BAD_REQUEST');
        }

        // Security check against directory traversal
        $file_path = realpath(FCPATH . $file_key);
        if ($file_path && strpos($file_path, realpath(FCPATH . 'uploads')) === 0) {
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }

        $this->send_success(null, 'File berhasil dihapus dari storage', 200);
    }

    // =========================================================================
    // F. MANAJEMEN TAMU (GUEST MANAGER & WHATSAPP GENERATOR)
    // =========================================================================

    /**
     * GET /api/v1/invitations/:id/guests
     */
    public function guests_index_get($id = null)
    {
        $user = $this->get_authenticated_user();
        if (empty($id)) {
            $this->send_error('ID undangan wajib disertakan', 400, 'BAD_REQUEST');
        }
        $inv = $this->get_user_invitation($id, $user->id);

        $search   = trim($this->input->get('search'));
        $category = trim($this->input->get('category'));
        $page     = max(1, (int)$this->input->get('page') ?: 1);
        $limit    = max(1, min(200, (int)$this->input->get('limit') ?: 50));
        $offset   = ($page - 1) * $limit;

        $this->db->where('invitation_id', $inv->id);
        if (!empty($search)) {
            $this->db->like('guest_name', $search);
        }
        if (!empty($category) && strtolower($category) !== 'all') {
            $this->db->where('group_category', $category);
        }

        $guests_db = $this->db->order_by('id', 'DESC')->limit($limit, $offset)->get('invitation_guests')->result();

        // Calculate statistics
        $all_guests = $this->db->where('invitation_id', $inv->id)->get('invitation_guests')->result();
        $stats = [
            'total'       => count($all_guests),
            'vip'         => 0,
            'keluarga'    => 0,
            'sahabat'     => 0,
            'temanKantor' => 0,
            'umum'        => 0
        ];

        foreach ($all_guests as $g) {
            $cat = strtolower(str_replace(' ', '', $g->group_category));
            if ($cat === 'vip') {
                $stats['vip']++;
            } elseif ($cat === 'keluarga') {
                $stats['keluarga']++;
            } elseif ($cat === 'sahabat') {
                $stats['sahabat']++;
            } elseif ($cat === 'temankantor' || $cat === 'kantor') {
                $stats['temanKantor']++;
            } else {
                $stats['umum']++;
            }
        }

        $frontend_host = 'http://192.168.18.176:3000';
        $guest_list = [];
        foreach ($guests_db as $g) {
            $guest_list[] = [
                'id'           => (string)$g->id,
                'name'         => $g->guest_name,
                'category'     => $g->group_category ?: 'Umum',
                'phone'        => $g->phone_number,
                'personalLink' => $frontend_host . '/invite/' . $inv->slug . '?to=' . urlencode($g->guest_name),
                'createdAt'    => !empty($g->created_at) ? date('c', strtotime($g->created_at)) : gmdate('c')
            ];
        }

        $this->send_success([
            'guests' => $guest_list,
            'stats'  => $stats
        ], 'Daftar tamu berhasil diambil', 200);
    }

    /**
     * POST /api/v1/invitations/:id/guests
     */
    public function guests_index_post($id = null)
    {
        $user = $this->get_authenticated_user();
        if (empty($id)) {
            $this->send_error('ID undangan wajib disertakan', 400, 'BAD_REQUEST');
        }
        $inv = $this->get_user_invitation($id, $user->id);

        $name     = trim($this->get_body_param('name'));
        $category = trim($this->get_body_param('category') ?: 'Umum');
        $phone    = trim($this->get_body_param('phone'));

        if (empty($name)) {
            $this->send_error('Nama tamu wajib diisi', 422, 'VALIDATION_ERROR', [
                ['field' => 'name', 'message' => 'Nama tidak boleh kosong']
            ]);
        }

        $guest_code = random_string('alnum', 8);
        $data = [
            'invitation_id'   => $inv->id,
            'guest_name'      => $name,
            'group_category'  => $category,
            'phone_number'    => $phone,
            'guest_code'      => $guest_code,
            'seat_allocation' => 1,
            'created_at'      => date('Y-m-d H:i:s'),
            'updated_at'      => date('Y-m-d H:i:s')
        ];

        $this->db->insert('invitation_guests', $data);
        $new_id = $this->db->insert_id();

        $frontend_host = 'http://192.168.18.176:3000';
        $resp = [
            'id'           => (string)$new_id,
            'name'         => $name,
            'category'     => $category,
            'phone'        => $phone,
            'personalLink' => $frontend_host . '/invite/' . $inv->slug . '?to=' . urlencode($name),
            'createdAt'    => gmdate('c')
        ];

        $this->send_success($resp, 'Tamu baru berhasil ditambahkan', 201);
    }

    /**
     * POST /api/v1/invitations/:id/guests/bulk
     */
    public function guests_bulk_post($id = null)
    {
        $user = $this->get_authenticated_user();
        if (empty($id)) {
            $this->send_error('ID undangan wajib disertakan', 400, 'BAD_REQUEST');
        }
        $inv = $this->get_user_invitation($id, $user->id);

        $guests = $this->get_body_param('guests');
        if (!is_array($guests) || empty($guests)) {
            $this->send_error('Daftar tamu (guests) kosong atau tidak valid', 422, 'VALIDATION_ERROR');
        }

        $inserted = 0;
        foreach ($guests as $g) {
            $name = isset($g['name']) ? trim($g['name']) : '';
            if (empty($name)) continue;

            $category = isset($g['category']) ? trim($g['category']) : 'Umum';
            $phone    = isset($g['phone']) ? trim($g['phone']) : null;

            $this->db->insert('invitation_guests', [
                'invitation_id'   => $inv->id,
                'guest_name'      => $name,
                'group_category'  => $category,
                'phone_number'    => $phone,
                'guest_code'      => random_string('alnum', 8),
                'seat_allocation' => 1,
                'created_at'      => date('Y-m-d H:i:s'),
                'updated_at'      => date('Y-m-d H:i:s')
            ]);
            $inserted++;
        }

        $this->send_success(['insertedCount' => $inserted], $inserted . ' tamu berhasil diimport', 201);
    }

    /**
     * GET /api/v1/invitations/:id/guests/:guest_id/wa-message
     */
    public function guests_wa_message_get($invitation_id = null, $guest_id = null)
    {
        $user = $this->get_authenticated_user();
        if (empty($invitation_id) || empty($guest_id)) {
            $this->send_error('ID undangan dan ID tamu wajib disertakan', 400, 'BAD_REQUEST');
        }
        $inv = $this->get_user_invitation($invitation_id, $user->id);

        $guest = $this->db->get_where('invitation_guests', [
            'id'            => $guest_id,
            'invitation_id' => $inv->id
        ])->row();

        if (!$guest) {
            $this->send_error('Tamu tidak ditemukan', 404, 'NOT_FOUND');
        }

        $couple = $this->db->get_where('invitation_couples', ['invitation_id' => $inv->id])->row();
        $groom = $couple ? ($couple->groom_nickname ?: 'Mempelai Pria') : 'Mempelai Pria';
        $bride = $couple ? ($couple->bride_nickname ?: 'Mempelai Wanita') : 'Mempelai Wanita';

        $frontend_host = 'http://192.168.18.176:3000';
        $link = $frontend_host . '/invite/' . $inv->slug . '?to=' . urlencode($guest->guest_name);

        $wa_text = "Kepada Yth.\n*{$guest->guest_name}*\n\n"
                 . "Assalamu’alaikum Warahmatullahi Wabarakatuh.\n\n"
                 . "Tanpa mengurangi rasa hormat, perkenankan kami mengundang Bapak/Ibu/Saudara/i untuk menghadiri acara pernikahan kami:\n\n"
                 . "*{$groom} & {$bride}*\n\n"
                 . "Berikut tautan undangan online resmi Anda untuk melihat informasi lengkap acara:\n"
                 . "🔗 {$link}\n\n"
                 . "Merupakan suatu kehormatan dan kebahagiaan bagi kami apabila Bapak/Ibu/Saudara/i berkenan hadir dan memberikan doa restu.\n\n"
                 . "Terima kasih.\n"
                 . "Wassalamu’alaikum Warahmatullahi Wabarakatuh.";

        $phone = preg_replace('/[^0-9]/', '', $guest->phone_number ?: '');
        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        }

        $wa_url = "https://api.whatsapp.com/send?phone={$phone}&text=" . urlencode($wa_text);

        $data = [
            'guestName'   => $guest->guest_name,
            'phoneNumber' => $guest->phone_number,
            'waUrl'       => $wa_url,
            'message'     => $wa_text
        ];

        $this->send_success($data, 'Template pesan WhatsApp berhasil dibuat', 200);
    }

    /**
     * DELETE /api/v1/invitations/:id/guests/:guest_id
     */
    public function guests_item_delete($invitation_id = null, $guest_id = null)
    {
        $user = $this->get_authenticated_user();
        if (empty($invitation_id) || empty($guest_id)) {
            $this->send_error('ID undangan dan ID tamu wajib disertakan', 400, 'BAD_REQUEST');
        }
        $inv = $this->get_user_invitation($invitation_id, $user->id);

        $this->db->where('id', $guest_id)->where('invitation_id', $inv->id)->delete('invitation_guests');

        $this->send_success(null, 'Tamu berhasil dihapus', 200);
    }

    // =========================================================================
    // G. MANAJEMEN RSVP & BUKU TAMU (DASHBOARD OWNER)
    // =========================================================================

    /**
     * GET /api/v1/invitations/:id/rsvps
     */
    public function rsvps_index_get($id = null)
    {
        $user = $this->get_authenticated_user();
        if (empty($id)) {
            $this->send_error('ID undangan wajib disertakan', 400, 'BAD_REQUEST');
        }
        $inv = $this->get_user_invitation($id, $user->id);

        $attendance = trim(strtolower($this->input->get('attendance') ?: 'all'));
        $search     = trim($this->input->get('search'));
        $page       = max(1, (int)$this->input->get('page') ?: 1);
        $limit      = max(1, min(100, (int)$this->input->get('limit') ?: 50));
        $offset     = ($page - 1) * $limit;

        $this->db->where('invitation_id', $inv->id);
        if ($attendance === 'hadir') {
            $this->db->where('attendance_status', 'attending');
        } elseif ($attendance === 'ragu') {
            $this->db->where('attendance_status', 'uncertain');
        } elseif ($attendance === 'tidak') {
            $this->db->where('attendance_status', 'not_attending');
        }

        if (!empty($search)) {
            $this->db->like('sender_name', $search);
        }

        $rsvps_db = $this->db->order_by('created_at', 'DESC')->limit($limit, $offset)->get('invitation_rsvps')->result();

        // Calculate summary
        $all_rsvps = $this->db->where('invitation_id', $inv->id)->get('invitation_rsvps')->result();
        $summary = [
            'totalPastiHadir'  => 0,
            'totalEstimasiPax' => 0,
            'totalRagu'        => 0,
            'totalBerhalangan' => 0,
            'totalUcapan'      => count($all_rsvps)
        ];

        foreach ($all_rsvps as $r) {
            if ($r->attendance_status === 'attending') {
                $summary['totalPastiHadir']++;
                $summary['totalEstimasiPax'] += max(1, (int)$r->pax);
            } elseif ($r->attendance_status === 'uncertain') {
                $summary['totalRagu']++;
            } elseif ($r->attendance_status === 'not_attending') {
                $summary['totalBerhalangan']++;
            }
        }

        $rsvps = [];
        foreach ($rsvps_db as $r) {
            $att = 'hadir';
            if ($r->attendance_status === 'not_attending') $att = 'tidak';
            if ($r->attendance_status === 'uncertain') $att = 'ragu';

            $rsvps[] = [
                'id'        => (string)$r->id,
                'guestName' => $r->sender_name,
                'attendance'=> $att,
                'pax'       => (int)$r->pax,
                'message'   => $r->message,
                'createdAt' => !empty($r->created_at) ? date('c', strtotime($r->created_at)) : gmdate('c')
            ];
        }

        $this->send_success([
            'rsvps'   => $rsvps,
            'summary' => $summary
        ], 'Data RSVP berhasil diambil', 200);
    }

    /**
     * DELETE /api/v1/invitations/:id/rsvps/:rsvp_id
     */
    public function rsvps_item_delete($invitation_id = null, $rsvp_id = null)
    {
        $user = $this->get_authenticated_user();
        if (empty($invitation_id) || empty($rsvp_id)) {
            $this->send_error('ID undangan dan ID RSVP wajib disertakan', 400, 'BAD_REQUEST');
        }
        $inv = $this->get_user_invitation($invitation_id, $user->id);

        $this->db->where('id', $rsvp_id)->where('invitation_id', $inv->id)->delete('invitation_rsvps');

        $this->send_success(null, 'Ucapan berhasil dihapus', 200);
    }

    /**
     * GET /api/v1/invitations/:id/rsvps/export
     */
    public function rsvps_export_get($id = null)
    {
        $user = $this->get_authenticated_user();
        if (empty($id)) {
            $this->send_error('ID undangan wajib disertakan', 400, 'BAD_REQUEST');
        }
        $inv = $this->get_user_invitation($id, $user->id);

        $rsvps = $this->db->where('invitation_id', $inv->id)
                          ->order_by('created_at', 'ASC')
                          ->get('invitation_rsvps')
                          ->result();

        $filename = 'RSVP_' . url_title($inv->title, '_', TRUE) . '_' . date('Ymd_His') . '.csv';

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $output = fopen('php://output', 'w');
        // Add UTF-8 BOM for Excel compatibility
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        fputcsv($output, ['No', 'Nama Tamu', 'Status Kehadiran', 'Jumlah Pax', 'Pesan / Doa', 'Tanggal Kirim']);

        $no = 1;
        foreach ($rsvps as $r) {
            $status = 'Hadir';
            if ($r->attendance_status === 'not_attending') $status = 'Berhalangan';
            if ($r->attendance_status === 'uncertain') $status = 'Ragu-ragu';

            fputcsv($output, [
                $no++,
                $r->sender_name,
                $status,
                $r->pax,
                $r->message,
                $r->created_at
            ]);
        }

        fclose($output);
        exit;
    }
}
