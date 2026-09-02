<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reset_pw extends CI_Controller {
    public function index() {
        $this->load->library('aauth');
        $email = 'yordan@gmail.com';
        $user = $this->aauth->get_user(false, $email);
        if ($user) {
            $new_pass = '123456';
            if ($this->aauth->update_user($user->id, $user->email, $new_pass)) {
                echo 'Password berhasil direset menjadi: ' . $new_pass;
            } else {
                echo 'Gagal: ' . $this->aauth->print_errors();
            }
        } else {
            echo 'User tidak ditemukan!';
        }
    }
}
