<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Invitation extends API
{
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->helper(['url', 'file']);
	}

	/**
	 * GET /api/v1/invitation/{slug}?to={guest_code}
	 * Endpoint utama untuk frontend menampilkan undangan digital secara lengkap
	 */
	public function detail_get($slug = null)
	{
		if (empty($slug)) {
			$slug = $this->get('slug');
		}

		if (empty($slug)) {
			$this->response([
				'status'  => false,
				'message' => 'Parameter slug undangan wajib disertakan'
			], API::HTTP_BAD_REQUEST);
			return;
		}

		// 1. Ambil data induk undangan
		$this->db->select('invitations.*, templates.name as template_name, templates.slug as template_slug, templates.category as template_category, templates.default_config as template_default_config');
		$this->db->from('invitations');
		$this->db->join('templates', 'templates.id = invitations.template_id', 'left');
		$this->db->where('invitations.slug', $slug);
		$invitation = $this->db->get()->row();

		if (!$invitation) {
			$this->response([
				'status'  => false,
				'message' => 'Undangan dengan slug "' . $slug . '" tidak ditemukan'
			], API::HTTP_NOT_FOUND);
			return;
		}

		// Update total pengunjung
		$this->db->where('id', $invitation->id)->update('invitations', [
			'view_count' => $invitation->view_count + 1
		]);

		// 2. Data Tamu Personalisasi (Jika ada parameter ?to=... atau ?guest=...)
		$guest_code = $this->get('to') ?: $this->get('guest');
		$guest_data = null;
		if (!empty($guest_code)) {
			$guest = $this->db->get_where('invitation_guests', [
				'invitation_id' => $invitation->id,
				'guest_code'    => $guest_code
			])->row();

			if ($guest) {
				$guest_data = [
					'id'              => (int) $guest->id,
					'name'            => $guest->guest_name,
					'code'            => $guest->guest_code,
					'category'        => $guest->group_category,
					'seat_allocation' => (int) $guest->seat_allocation
				];
			} else {
				// Tamu custom dari slug URL
				$guest_data = [
					'id'              => null,
					'name'            => ucwords(str_replace(['-', '_', '+'], ' ', $guest_code)),
					'code'            => $guest_code,
					'category'        => 'Tamu Undangan',
					'seat_allocation' => 1
				];
			}
		}

		// 3. Data Aset Musik & Background (Dari tabel musics & backgrounds yang sudah dipisah)
		$music_data = null;
		if (!empty($invitation->music_id)) {
			$music = $this->db->get_where('musics', ['id' => $invitation->music_id])->row();
			if ($music) {
				$music_data = [
					'id'        => (int) $music->id,
					'title'     => $music->title,
					'artist'    => $music->artist,
					'file_url'  => base_url('uploads/musics/' . $music->file_url),
					'category'  => $music->category
				];
			}
		}

		$background_data = null;
		if (!empty($invitation->background_id)) {
			$bg = $this->db->get_where('backgrounds', ['id' => $invitation->background_id])->row();
			if ($bg) {
				$background_data = [
					'id'        => (int) $bg->id,
					'name'      => $bg->name,
					'image_url' => base_url('uploads/backgrounds/' . $bg->image_url),
					'category'  => $bg->category
				];
			}
		}

		// 4. Data Mempelai
		$couple = $this->db->get_where('invitation_couples', ['invitation_id' => $invitation->id])->row();
		$couple_data = null;
		if ($couple) {
			$couple_data = [
				'couple_order' => $couple->couple_order,
				'groom' => [
					'nickname'    => $couple->groom_nickname,
					'fullname'    => $couple->groom_full_name,
					'child_order' => $couple->groom_child_order,
					'father_name' => $couple->groom_father_name,
					'mother_name' => $couple->groom_mother_name,
					'parents'     => 'Putra dari Bpk. ' . $couple->groom_father_name . ' & Ibu ' . $couple->groom_mother_name,
					'instagram'   => $couple->groom_instagram,
					'photo_url'   => $couple->groom_photo ? base_url('uploads/invitation_couples/' . $couple->groom_photo) : null
				],
				'bride' => [
					'nickname'    => $couple->bride_nickname,
					'fullname'    => $couple->bride_full_name,
					'child_order' => $couple->bride_child_order,
					'father_name' => $couple->bride_father_name,
					'mother_name' => $couple->bride_mother_name,
					'parents'     => 'Putri dari Bpk. ' . $couple->bride_father_name . ' & Ibu ' . $couple->bride_mother_name,
					'instagram'   => $couple->bride_instagram,
					'photo_url'   => $couple->bride_photo ? base_url('uploads/invitation_couples/' . $couple->bride_photo) : null
				]
			];
		}

		// 5. Rangkaian Acara
		$events_db = $this->db->order_by('sort_order', 'ASC')
							  ->order_by('event_date', 'ASC')
							  ->get_where('invitation_events', ['invitation_id' => $invitation->id])
							  ->result();
		$events_data = [];
		foreach ($events_db as $ev) {
			$events_data[] = [
				'id'                 => (int) $ev->id,
				'event_name'         => $ev->event_name,
				'date'               => $ev->event_date,
				'time_start'         => substr($ev->time_start, 0, 5),
				'time_end'           => $ev->time_end ? substr($ev->time_end, 0, 5) : null,
				'timezone'           => $ev->timezone,
				'venue_name'         => $ev->venue_name,
				'address'            => $ev->address,
				'latitude'           => $ev->latitude ? (float) $ev->latitude : null,
				'longitude'          => $ev->longitude ? (float) $ev->longitude : null,
				'google_maps_url'    => $ev->google_maps_url,
				'live_streaming_url' => $ev->live_streaming_url
			];
		}

		// 6. Galeri Foto & Video
		$galleries_db = $this->db->order_by('sort_order', 'ASC')
								 ->get_where('invitation_galleries', ['invitation_id' => $invitation->id])
								 ->result();
		$galleries_data = [];
		foreach ($galleries_db as $g) {
			$file_url = $g->media_type == 'image' ? base_url('uploads/invitation_galleries/' . $g->file_url) : $g->file_url;
			$galleries_data[] = [
				'id'         => (int) $g->id,
				'media_type' => $g->media_type,
				'url'        => $file_url,
				'caption'    => $g->caption
			];
		}

		// 7. Kisah Cinta (Stories)
		$stories_db = $this->db->order_by('sort_order', 'ASC')
							   ->get_where('invitation_stories', ['invitation_id' => $invitation->id])
							   ->result();
		$stories_data = [];
		foreach ($stories_db as $st) {
			$stories_data[] = [
				'id'        => (int) $st->id,
				'year_date' => $st->year_date,
				'title'     => $st->title,
				'story'     => $st->story,
				'image_url' => $st->image_url ? base_url('uploads/invitation_stories/' . $st->image_url) : null
			];
		}

		// 8. Amplop Digital & QRIS
		$wallets_db = $this->db->order_by('sort_order', 'ASC')
							   ->get_where('invitation_wallets', ['invitation_id' => $invitation->id])
							   ->result();
		$wallets_data = [];
		foreach ($wallets_db as $w) {
			$wallets_data[] = [
				'id'             => (int) $w->id,
				'wallet_type'    => $w->wallet_type,
				'provider_name'  => $w->provider_name,
				'account_number' => $w->account_number,
				'account_holder' => $w->account_holder,
				'qris_image_url' => $w->qris_image ? base_url('uploads/invitation_wallets/' . $w->qris_image) : null,
				'notes'          => $w->notes
			];
		}

		// 9. Cuplikan Doa & Ucapan Terkini
		$recent_wishes = $this->db->order_by('id', 'DESC')
								  ->limit(10)
								  ->get_where('invitation_rsvps', [
									  'invitation_id' => $invitation->id,
									  'is_approved'   => 1
								  ])->result();
		$wishes_data = [];
		foreach ($recent_wishes as $rw) {
			$wishes_data[] = [
				'id'                => (int) $rw->id,
				'name'              => $rw->sender_name,
				'attendance_status' => $rw->attendance_status,
				'pax'               => (int) $rw->pax,
				'message'           => $rw->message,
				'created_at'        => $rw->created_at
			];
		}

		// Parse Theme Config JSON
		$theme_config = [];
		if (!empty($invitation->theme_config)) {
			$parsed = json_decode($invitation->theme_config, true);
			if (json_last_error() === JSON_ERROR_NONE) {
				$theme_config = $parsed;
			}
		}

		// Susun Payload Lengkap
		$response_data = [
			'invitation_id' => (int) $invitation->id,
			'slug'          => $invitation->slug,
			'title'         => $invitation->title,
			'cover_photo'   => $invitation->cover_photo ? base_url('uploads/invitations/' . $invitation->cover_photo) : null,
			'status'        => $invitation->status,
			'published_at'  => $invitation->published_at,
			'expired_at'    => $invitation->expired_at,
			'view_count'    => (int) $invitation->view_count + 1,
			'guest'         => $guest_data,
			'template'      => [
				'name'           => $invitation->template_name,
				'slug'           => $invitation->template_slug,
				'category'       => $invitation->template_category,
				'default_config' => json_decode($invitation->template_default_config, true) ?: []
			],
			'theme_config' => $theme_config,
			'background'   => $background_data,
			'music'        => $music_data,
			'couples'      => $couple_data,
			'events'       => $events_data,
			'galleries'    => $galleries_data,
			'stories'      => $stories_data,
			'wallets'      => $wallets_data,
			'recent_wishes'=> $wishes_data
		];

		$this->response([
			'status'  => true,
			'message' => 'Data undangan berhasil diambil',
			'data'    => $response_data
		], API::HTTP_OK);
	}

	/**
	 * POST /api/v1/invitation/{slug}/rsvp
	 * Endpoint bagi tamu untuk konfirmasi kehadiran dan kirim doa/ucapan
	 */
	public function rsvp_post($slug = null)
	{
		if (empty($slug)) {
			$slug = $this->post('slug');
		}

		$invitation = $this->db->get_where('invitations', ['slug' => $slug])->row();
		if (!$invitation) {
			$this->response([
				'status'  => false,
				'message' => 'Undangan tidak ditemukan'
			], API::HTTP_NOT_FOUND);
			return;
		}

		$sender_name = trim($this->post('sender_name') ?: $this->post('name'));
		$attendance_status = trim($this->post('attendance_status') ?: $this->post('status'));
		$pax = (int) ($this->post('pax') ?: 1);
		$message = trim($this->post('message') ?: $this->post('wish'));
		$guest_code = trim($this->post('guest_code') ?: $this->post('to'));

		if (empty($sender_name)) {
			$this->response([
				'status'  => false,
				'message' => 'Nama pengirim wajib diisi'
			], API::HTTP_BAD_REQUEST);
			return;
		}

		if (!in_array($attendance_status, ['attending', 'not_attending', 'uncertain'])) {
			$attendance_status = 'attending';
		}

		$guest_id = null;
		if (!empty($guest_code)) {
			$guest = $this->db->get_where('invitation_guests', [
				'invitation_id' => $invitation->id,
				'guest_code'    => $guest_code
			])->row();
			if ($guest) {
				$guest_id = $guest->id;
			}
		}

		$save_data = [
			'invitation_id'     => $invitation->id,
			'guest_id'          => $guest_id,
			'sender_name'       => $sender_name,
			'attendance_status' => $attendance_status,
			'pax'               => $pax,
			'message'           => $message,
			'is_approved'       => 1,
			'created_at'        => date('Y-m-d H:i:s')
		];

		$this->db->insert('invitation_rsvps', $save_data);
		$insert_id = $this->db->insert_id();

		$this->response([
			'status'  => true,
			'message' => 'Konfirmasi kehadiran dan ucapan Anda berhasil terkirim. Terima kasih!',
			'data'    => [
				'id'                => $insert_id,
				'sender_name'       => $sender_name,
				'attendance_status' => $attendance_status,
				'pax'               => $pax,
				'message'           => $message,
				'created_at'        => $save_data['created_at']
			]
		], API::HTTP_OK);
	}

	/**
	 * GET /api/v1/invitation/{slug}/wishes?page=1&limit=20
	 * Endpoint daftar ucapan doa dari para tamu (dengan pagination)
	 */
	public function wishes_get($slug = null)
	{
		if (empty($slug)) {
			$slug = $this->get('slug');
		}

		$invitation = $this->db->get_where('invitations', ['slug' => $slug])->row();
		if (!$invitation) {
			$this->response([
				'status'  => false,
				'message' => 'Undangan tidak ditemukan'
			], API::HTTP_NOT_FOUND);
			return;
		}

		$page = max(1, (int) ($this->get('page') ?: 1));
		$limit = max(1, min(100, (int) ($this->get('limit') ?: 20)));
		$offset = ($page - 1) * $limit;

		$total = $this->db->where('invitation_id', $invitation->id)
						  ->where('is_approved', 1)
						  ->count_all_results('invitation_rsvps');

		$wishes = $this->db->order_by('id', 'DESC')
						   ->limit($limit, $offset)
						   ->get_where('invitation_rsvps', [
							   'invitation_id' => $invitation->id,
							   'is_approved'   => 1
						   ])->result();

		$items = [];
		foreach ($wishes as $w) {
			$items[] = [
				'id'                => (int) $w->id,
				'sender_name'       => $w->sender_name,
				'attendance_status' => $w->attendance_status,
				'pax'               => (int) $w->pax,
				'message'           => $w->message,
				'reply_message'     => $w->reply_message,
				'created_at'        => $w->created_at
			];
		}

		$this->response([
			'status'  => true,
			'message' => 'Daftar ucapan berhasil diambil',
			'data'    => [
				'items'       => $items,
				'total_items' => (int) $total,
				'page'        => $page,
				'limit'       => $limit,
				'total_pages' => ceil($total / $limit)
			]
		], API::HTTP_OK);
	}

	/**
	 * GET /api/v1/catalog/templates
	 * Endpoint daftar tema aktif untuk katalog di frontend
	 */
	public function catalog_templates_get()
	{
		$templates = $this->db->order_by('id', 'ASC')->get_where('templates', ['is_active' => 1])->result();
		$items = [];
		foreach ($templates as $t) {
			$items[] = [
				'id'             => (int) $t->id,
				'name'           => $t->name,
				'slug'           => $t->slug,
				'category'       => $t->category,
				'preview_image'  => $t->preview_image ? base_url($t->preview_image) : null,
				'demo_url'       => $t->demo_url,
				'tier'           => $t->tier,
				'price'          => isset($t->price) ? (float) $t->price : 0.0,
				'default_config' => json_decode($t->default_config, true) ?: []
			];
		}

		$this->response([
			'status'  => true,
			'message' => 'Daftar template berhasil diambil',
			'data'    => $items
		], API::HTTP_OK);
	}

	/**
	 * GET /api/v1/catalog/musics?category=Romantic|Acoustic|Islamic
	 * Endpoint katalog playlist lagu pernikahan
	 */
	public function catalog_musics_get()
	{
		$category = $this->get('category');
		$this->db->where('is_active', 1);
		if (!empty($category)) {
			$this->db->where('category', $category);
		}
		$musics = $this->db->order_by('id', 'DESC')->get('musics')->result();
		$items = [];
		foreach ($musics as $m) {
			$items[] = [
				'id'       => (int) $m->id,
				'title'    => $m->title,
				'artist'   => $m->artist,
				'category' => $m->category,
				'file_url' => base_url('uploads/musics/' . $m->file_url)
			];
		}

		$this->response([
			'status'  => true,
			'message' => 'Daftar playlist musik berhasil diambil',
			'data'    => $items
		], API::HTTP_OK);
	}

	/**
	 * GET /api/v1/catalog/backgrounds?category=Rustic|Modern|Floral|Islamic
	 * Endpoint katalog gambar latar belakang
	 */
	public function catalog_backgrounds_get()
	{
		$category = $this->get('category');
		$this->db->where('is_active', 1);
		if (!empty($category)) {
			$this->db->where('category', $category);
		}
		$backgrounds = $this->db->order_by('id', 'DESC')->get('backgrounds')->result();
		$items = [];
		foreach ($backgrounds as $b) {
			$items[] = [
				'id'        => (int) $b->id,
				'name'      => $b->name,
				'category'  => $b->category,
				'image_url' => base_url('uploads/backgrounds/' . $b->image_url)
			];
		}

		$this->response([
			'status'  => true,
			'message' => 'Daftar background berhasil diambil',
			'data'    => $items
		], API::HTTP_OK);
	}

	/**
	 * GET /api/v1/catalog/packages
	 * Endpoint daftar paket layanan
	 */
	public function catalog_packages_get()
	{
		$this->response([
			'status'  => true,
			'message' => 'Layanan paket telah dialihkan ke pembelian template langsung (Template Marketplace)',
			'data'    => []
		], API::HTTP_OK);
	}

	/**
	 * POST /api/v1/upload
	 * Endpoint upload gambar untuk user (Cover, Mempelai, Galeri, Kisah Cinta, QRIS, Bukti Pembayaran)
	 * Params (multipart/form-data):
	 * - image (atau file/photo) : file gambar (jpg, jpeg, png, webp, gif)
	 * - type : 'cover' | 'couple' | 'gallery' | 'story' | 'wallet' | 'order'
	 */
	public function upload_post()
	{
		$type = $this->post('type') ?: $this->get('type');
		$folder_map = [
			'cover'        => 'invitations',
			'invitation'   => 'invitations',
			'couple'       => 'couples',
			'groom'        => 'couples',
			'bride'        => 'couples',
			'gallery'      => 'galleries',
			'story'        => 'stories',
			'wallet'       => 'wallets',
			'qris'         => 'wallets',
			'order'        => 'orders',
			'proof'        => 'orders',
			'general'      => 'tmp'
		];

		$folder = isset($folder_map[$type]) ? $folder_map[$type] : 'invitations';
		$target_dir = FCPATH . 'uploads/' . $folder . '/';

		if (!is_dir($target_dir)) {
			mkdir($target_dir, 0777, true);
		}

		$file_field = null;
		if (!empty($_FILES['image']['name'])) {
			$file_field = 'image';
		} elseif (!empty($_FILES['file']['name'])) {
			$file_field = 'file';
		} elseif (!empty($_FILES['photo']['name'])) {
			$file_field = 'photo';
		}

		if (!$file_field) {
			$this->response([
				'status'  => false,
				'message' => 'File gambar tidak ditemukan. Kirim file melalui parameter multipart "image" atau "file"'
			], API::HTTP_BAD_REQUEST);
			return;
		}

		$config = [
			'upload_path'   => $target_dir,
			'allowed_types' => 'jpg|jpeg|png|webp|gif',
			'max_size'      => 10240, // Max 10MB
			'encrypt_name'  => TRUE
		];

		$this->load->library('upload', $config);
		$this->upload->initialize($config);

		if (!$this->upload->do_upload($file_field)) {
			$error = $this->upload->display_errors('', '');
			$this->response([
				'status'  => false,
				'message' => 'Gagal mengunggah file: ' . $error
			], API::HTTP_BAD_REQUEST);
			return;
		}

		$data = $this->upload->data();
		$file_url = base_url('uploads/' . $folder . '/' . $data['file_name']);

		$this->response([
			'status'  => true,
			'message' => 'Gambar berhasil diunggah',
			'data'    => [
				'file_name'    => $data['file_name'],
				'file_url'     => $file_url,
				'file_path'    => 'uploads/' . $folder . '/' . $data['file_name'],
				'folder'       => $folder,
				'type'         => $type ?: 'invitation',
				'file_type'    => $data['file_type'],
				'file_size'    => (int) $data['file_size'],
				'image_width'  => (int) $data['image_width'],
				'image_height' => (int) $data['image_height']
			]
		], API::HTTP_OK);
	}
}
