<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/


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

// ==========================================
// InfoMase Wedding Digital API v1 (Full Specification)
// ==========================================

// A. Auth
$route['api/v1/auth/register']      = 'api/v1/auth_register';
$route['api/v1/auth/login']         = 'api/v1/auth_login';
$route['api/v1/auth/me']            = 'api/v1/auth_me';
$route['api/v1/auth/refresh-token'] = 'api/v1/auth_refresh_token';
$route['api/v1/auth/logout']        = 'api/v1/auth_logout';

// B. Templates
$route['api/v1/templates']          = 'api/v1/templates';

// C. Invitations CRUD & Actions
$route['api/v1/invitations/check-slug']                   = 'api/v1/invitations_check_slug';
$route['api/v1/invitations/(:any)/stats']                = 'api/v1/invitations_stats/$1';
$route['api/v1/invitations/(:any)/upload/photo']         = 'api/v1/upload_photo/$1';
$route['api/v1/invitations/(:any)/upload/gallery-batch'] = 'api/v1/upload_gallery_batch/$1';
$route['api/v1/invitations/(:any)/upload/audio']         = 'api/v1/upload_audio/$1';
$route['api/v1/invitations/(:any)/assets']               = 'api/v1/delete_asset/$1';

// F. Guest Management
$route['api/v1/invitations/(:any)/guests/(:any)/wa-message'] = 'api/v1/guests_wa_message/$1/$2';
$route['api/v1/invitations/(:any)/guests/bulk']              = 'api/v1/guests_bulk/$1';
$route['api/v1/invitations/(:any)/guests/(:any)']            = 'api/v1/guests_item/$1/$2';
$route['api/v1/invitations/(:any)/guests']                   = 'api/v1/guests_index/$1';

// G. RSVP & Guestbook Management
$route['api/v1/invitations/(:any)/rsvps/export'] = 'api/v1/rsvps_export/$1';
$route['api/v1/invitations/(:any)/rsvps/(:any)']  = 'api/v1/rsvps_item/$1/$2';
$route['api/v1/invitations/(:any)/rsvps']         = 'api/v1/rsvps_index/$1';

// C. Invitations Detail / Update / Delete & List / Create
$route['api/v1/invitations/(:any)'] = 'api/v1/invitations_item/$1';
$route['api/v1/invitations']        = 'api/v1/invitations_index';

// D. Public Endpoints
$route['api/v1/public/invitations/(:any)/rsvp']   = 'api/v1/public_rsvp/$1';
$route['api/v1/public/invitations/(:any)/wishes'] = 'api/v1/public_wishes/$1';
$route['api/v1/public/invitations/(:any)']        = 'api/v1/public_invitation/$1';

// Legacy endpoints for backward compatibility
$route['api/v1/invitation/(:any)/rsvp']   = 'api/invitation/rsvp/$1';
$route['api/v1/invitation/(:any)/wishes'] = 'api/invitation/wishes/$1';
$route['api/v1/invitation/(:any)']        = 'api/invitation/detail/$1';
$route['api/v1/catalog/templates']        = 'api/invitation/catalog_templates';
$route['api/v1/catalog/musics']           = 'api/invitation/catalog_musics';
$route['api/v1/catalog/backgrounds']      = 'api/invitation/catalog_backgrounds';
$route['api/v1/catalog/packages']         = 'api/invitation/catalog_packages';
$route['api/v1/upload']                   = 'api/invitation/upload';


$route['api/(:any)'] = '$1/api/$1';
$route['api/(:any)/(:any)'] = '$1/api/$1/$2';
$route['api/(:any)/(:any)/(:any)'] = '$1/api/$1/$2/$3';
$route['api/(:any)/(:any)/(:any)/(:any)'] = '$1/api/$1/$2/$3/$3';

