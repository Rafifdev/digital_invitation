<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Init section


$route_path = APPPATH . 'routes/';
// require_once $route_path . 'routes_landing.php';


$route['default_controller'] = 'auth/backend/auth/login';
$route['404_override'] = 'not_found';
$route['translate_uri_dashes'] = FALSE;

$route['administrator/login'] = 'auth/backend/auth/login';
$route['administrator/register'] = 'auth/backend/auth/register';
$route['administrator/forgot-password'] = 'auth/backend/auth/forgot_password';

$route['page/chart/pie_chart'] = 'page/chart/pie_chart';
$route['page/(:any)'] = 'page/detail/$1';
$route['blog/index'] = 'blog/index';
$route['blog/(:any)'] = 'blog/detail/$1';
$route['administrator/web-page'] = 'page/backend/page/admin';

// Custom Routes for HMVC HR Module
$route[ADMIN_NAMESPACE_URL.'/hr_karyawan'] = 'hr/backend/hr_karyawan/index';
$route[ADMIN_NAMESPACE_URL.'/hr_karyawan/(:any)'] = 'hr/backend/hr_karyawan/$1';
$route[ADMIN_NAMESPACE_URL.'/hr_karyawan/(:any)/(:any)'] = 'hr/backend/hr_karyawan/$1/$2';

$route[ADMIN_NAMESPACE_URL.'/hr_shift'] = 'hr/backend/hr_shift/index';
$route[ADMIN_NAMESPACE_URL.'/hr_shift/(:any)'] = 'hr/backend/hr_shift/$1';
$route[ADMIN_NAMESPACE_URL.'/hr_shift/(:any)/(:any)'] = 'hr/backend/hr_shift/$1/$2';

$route[ADMIN_NAMESPACE_URL.'/hr_kantor'] = 'hr/backend/hr_kantor/index';
$route[ADMIN_NAMESPACE_URL.'/hr_kantor/(:any)'] = 'hr/backend/hr_kantor/$1';
$route[ADMIN_NAMESPACE_URL.'/hr_kantor/(:any)/(:any)'] = 'hr/backend/hr_kantor/$1/$2';

$route[ADMIN_NAMESPACE_URL.'/hr_pengaturan'] = 'hr/backend/hr_pengaturan/index';
$route[ADMIN_NAMESPACE_URL.'/hr_pengaturan/(:any)'] = 'hr/backend/hr_pengaturan/$1';

$route[ADMIN_NAMESPACE_URL.'/hr_absensi'] = 'hr/backend/hr_absensi/index';
$route[ADMIN_NAMESPACE_URL.'/hr_absensi/(:any)'] = 'hr/backend/hr_absensi/$1';

$route[ADMIN_NAMESPACE_URL.'/hr_laporan'] = 'hr/backend/hr_laporan/index';
$route[ADMIN_NAMESPACE_URL.'/hr_laporan/(:any)'] = 'hr/backend/hr_laporan/$1';
$route[ADMIN_NAMESPACE_URL.'/hr_laporan/(:any)/(:any)'] = 'hr/backend/hr_laporan/$1/$2';

$route[ADMIN_NAMESPACE_URL.'/hr_device'] = 'hr/backend/hr_device/index';
$route[ADMIN_NAMESPACE_URL.'/hr_device/(:any)'] = 'hr/backend/hr_device/$1';
$route[ADMIN_NAMESPACE_URL.'/hr_device/(:any)/(:any)'] = 'hr/backend/hr_device/$1/$2';

$route[ADMIN_NAMESPACE_URL.'/hr_payroll'] = 'hr/backend/hr_payroll/index';
$route[ADMIN_NAMESPACE_URL.'/hr_payroll/(:any)'] = 'hr/backend/hr_payroll/$1';
$route[ADMIN_NAMESPACE_URL.'/hr_payroll/(:any)/(:any)'] = 'hr/backend/hr_payroll/$1/$2';

$route[ADMIN_NAMESPACE_URL.'/hr_hari_libur'] = 'hr/backend/hr_hari_libur/index';
$route[ADMIN_NAMESPACE_URL.'/hr_hari_libur/(:any)'] = 'hr/backend/hr_hari_libur/$1';
$route[ADMIN_NAMESPACE_URL.'/hr_hari_libur/(:any)/(:any)'] = 'hr/backend/hr_hari_libur/$1/$2';

$route[ADMIN_NAMESPACE_URL.'/hr_profile'] = 'hr/backend/hr_profile/index';
$route[ADMIN_NAMESPACE_URL.'/hr_profile/(:any)'] = 'hr/backend/hr_profile/$1';
$route[ADMIN_NAMESPACE_URL.'/hr_profile/(:any)/(:any)'] = 'hr/backend/hr_profile/$1/$2';

// Routing baru untuk Portal Karyawan
$route[ADMIN_NAMESPACE_URL.'/absensi'] = 'hr/backend/hr_laporan/index';
$route[ADMIN_NAMESPACE_URL.'/absensi/laporan'] = 'hr/backend/hr_rekap/index';

$route[ADMIN_NAMESPACE_URL.'/manage-form/(:any)'] = 'form/backend/$1';
$route[ADMIN_NAMESPACE_URL.'/manage-form/(:any)/(:any)'] = 'form/backend/$1/$2';
$route[ADMIN_NAMESPACE_URL.'/manage-form/(:any)/(:any)/(:any)'] = 'form/backend/$1/$2/$3';
$route[ADMIN_NAMESPACE_URL.'/manage-form/(:any)/(:any)/(:any)/(:any)'] = 'form/backend/$1/$2/$3/$4';

$route[ADMIN_NAMESPACE_URL.'/(:any)'] = '$1/backend/$1';
$route[ADMIN_NAMESPACE_URL.'/(:any)/(:any)'] = '$1/backend/$1/$2';
$route[ADMIN_NAMESPACE_URL.'/(:any)/(:any)/(:any)'] = '$1/backend/$1/$2/$3';
$route[ADMIN_NAMESPACE_URL.'/(:any)/(:any)/(:any)/(:any)'] = '$1/backend/$1/$2/$3/$3';




$route['api/user/(:any)'] = 'api/user/$1';
$route['api/group/(:any)'] = 'api/group/$1';

$route[ADMIN_NAMESPACE_URL.'/tb_debts'] = 'utang_usaha/backend/utang_usaha';
$route[ADMIN_NAMESPACE_URL.'/tb_debts/(:any)'] = 'utang_usaha/backend/utang_usaha/$1';
$route[ADMIN_NAMESPACE_URL.'/tb_debts/(:any)/(:any)'] = 'utang_usaha/backend/utang_usaha/$1/$2';

$route[ADMIN_NAMESPACE_URL.'/tb_debt_payments'] = 'utang_usaha/backend/utang_usaha/kas_keluar';
$route[ADMIN_NAMESPACE_URL.'/tb_debt_payments/(:any)'] = 'utang_usaha/backend/utang_usaha/kas_keluar/$1';

$route[ADMIN_NAMESPACE_URL.'/tb_receivables'] = 'piutang_usaha/backend/piutang_usaha';
$route[ADMIN_NAMESPACE_URL.'/tb_receivables/(:any)'] = 'piutang_usaha/backend/piutang_usaha/$1';

$route[ADMIN_NAMESPACE_URL.'/tb_receivable_payments'] = 'piutang_usaha/backend/piutang_usaha/kas_masuk';
$route[ADMIN_NAMESPACE_URL.'/tb_receivable_payments/(:any)'] = 'piutang_usaha/backend/piutang_usaha/kas_masuk/$1';

$route[ADMIN_NAMESPACE_URL.'/pembayaran_utang'] = 'utang_usaha/backend/utang_usaha/kas_keluar';
$route[ADMIN_NAMESPACE_URL.'/pembayaran_utang/(:any)'] = 'utang_usaha/backend/utang_usaha/kas_keluar/$1';

$route[ADMIN_NAMESPACE_URL.'/pembayaran_piutang'] = 'piutang_usaha/backend/piutang_usaha/kas_masuk';
$route[ADMIN_NAMESPACE_URL.'/pembayaran_piutang/(:any)'] = 'piutang_usaha/backend/piutang_usaha/kas_masuk/$1';

$route['api/(:any)'] = '$1/api/$1';
$route['api/(:any)/(:any)'] = '$1/api/$1/$2';
$route['api/(:any)/(:any)/(:any)'] = '$1/api/$1/$2/$3';
$route['api/(:any)/(:any)/(:any)/(:any)'] = '$1/api/$1/$2/$3/$3';



