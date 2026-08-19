<?php
define('BASEPATH', '1');
$_SERVER['HTTP_HOST'] = 'ci3_metafora.test:8443';
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
$_SERVER['REQUEST_URI'] = '/administrator/dashboard';
require_once 'index.php';

$CI =& get_instance();
$CI->aauth->login_fast(25); // yordan (Manager)

echo "User: " . get_user_data('full_name') . "\n";
echo "Is Admin: " . ($CI->aauth->is_admin() ? 'YES' : 'NO') . "\n\n";

$menus = display_menu_admin(0, 1);
preg_match_all('/<span>(.*?)<\/span>/', $menus, $matches);
echo "Rendered Sidebar Menus for Manager:\n";
foreach (array_unique($matches[1]) as $m) {
    echo "- " . $m . "\n";
}
