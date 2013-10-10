<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
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
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There area two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/

## modul menu ##
$route['admincontrol/menu'] = "admin_menu/index";
$route['admincontrol/menu/pages/(:any)'] = "admin_menu/grid/$1";
$route['admincontrol/menu/pages'] = "admin_menu/index";
$route['admincontrol/menu/search'] = "admin_menu/search";
$route['admincontrol/menu/edit/(:num)'] = "admin_menu/edit/$1";
$route['admincontrol/menu/delete/(:num)'] = "admin_menu/delete/$1";
$route['admincontrol/menu/ajaxsort'] = "admin_menu/ajaxsort";
## modul menu ##

## modul menu auth ##
$route['admincontrol/menu_auth'] = "admin_menu_auth/index";
$route['admincontrol/menu_auth/pages/(:any)'] = "admin_menu_auth/grid/$1";
$route['admincontrol/menu_auth/pages'] = "admin_menu_auth/index";
$route['admincontrol/menu_auth/search'] = "admin_menu_auth/search";
$route['admincontrol/menu_auth/edit/(:num)'] = "admin_menu_auth/edit/$1";
$route['admincontrol/menu_auth/delete/(:num)'] = "admin_menu_auth/delete/$1";
$route['admincontrol/menu_auth/ajaxRequest1/(:num)'] = "admin_menu_auth/ajaxRequest1/$1";
## modul menu auth ##

## modul adminusers level ##
$route['admincontrol/adminusers_level'] = "admin_adminusers_level/index";
$route['admincontrol/adminusers_level/pages/(:any)'] = "admin_adminusers_level/grid/$1";
$route['admincontrol/adminusers_level/pages'] = "admin_adminusers_level/index";
$route['admincontrol/adminusers_level/search'] = "admin_adminusers_level/search";
$route['admincontrol/adminusers_level/edit/(:num)'] = "admin_adminusers_level/edit/$1";
$route['admincontrol/adminusers_level/delete/(:num)'] = "admin_adminusers_level/delete/$1";
## moduladminusers level ##

## modul adminusers auth ##
$route['admincontrol/adminusers_auth'] = "admin_adminusers_auth/index";
$route['admincontrol/adminusers_auth/pages/(:any)'] = "admin_adminusers_auth/grid/$1";
$route['admincontrol/adminusers_auth/pages'] = "admin_adminusers_auth/index";
$route['admincontrol/adminusers_auth/search'] = "admin_adminusers_auth/search";
$route['admincontrol/adminusers_auth/edit/(:num)'] = "admin_adminusers_auth/edit/$1";
$route['admincontrol/adminusers_auth/edit_account/(:num)'] = "admin_adminusers_auth/edit_account/$1";
$route['admincontrol/adminusers_auth/delete/(:num)'] = "admin_adminusers_auth/delete/$1";
## moduladminusers auth ##


## modul card ##
$route['admincontrol/card'] = "admin_card/admin_card";
$route['admincontrol/card/pages/(:any)'] = "admin_card/grid/$1";
$route['admincontrol/card/pages'] = "admin_card/index";
$route['admincontrol/card/search'] = "admin_card/search";
$route['admincontrol/card/edit/(:num)'] = "admin_card/edit/$1";
$route['admincontrol/card/delete/(:num)'] = "admin_card/delete/$1";
## modul card ##


## modul configs ##
$route['admincontrol/configs'] = "admin_configs/index";
## modul configs ##

## modul default ##
$route['admincontrol/dashboard'] = "admin_dashboard/index";
$route['admincontrol/login'] = "admin_login/index";
$route['admincontrol/logout'] = "admin_login/logout";
$route['admincontrol/index'] = "admin_dashboard/index";
$route['admincontrol'] = "admin_login";
## modul default ##

## publish ##
$route['admincontrol/widget/publish/(:any)'] = "admin_widget/publish/$1";
$route['admincontrol/widget/publish_parent/(:any)'] = "admin_widget/publish_parent/$1";
## publish ##

## featured ##
$route['admincontrol/widget/featured/(:any)'] = "admin_widget/featured/$1";
## featured ##

## new ##
$route['admincontrol/widget/new/(:any)'] = "admin_widget/new_product/$1";
## new ##

$route['default_controller'] = "admin_login";
$route['404_override'] = '';


/* End of file routes.php */
/* Location: ./application/config/routes.php */