<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Legacy (CI3-style) Routes
|--------------------------------------------------------------------------
| File ini dibaca oleh LegacyRouter/UnifiedRouter untuk menentukan
| default controller dan rute $route[...] bergaya CI3.
| Rute modern (fluent Route::get) berada di application/routes/web.php,
| sedangkan rute API/REST dipisah ke application/routes/api.php.
*/

$route['default_controller'] = 'App\Controllers\Welcome/index';
$route['404_override']       = '';
$route['translate_uri_dashes'] = FALSE;
