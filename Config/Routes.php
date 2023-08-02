<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (is_file(SYSTEMPATH . 'Config/Routes.php')) {
    require SYSTEMPATH . 'Config/Routes.php';
}

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
// The Auto Routing (Legacy) is very dangerous. It is easy to create vulnerable apps
// where controller filters or CSRF protection are bypassed.
// If you don't want to define all routes, please use the Auto Routing (Improved).
// Set `$autoRoutesImproved` to true in `app/Config/Feature.php` and set the following to true.
// $routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->match(['post','get'],'/', 'Users');
$routes->get('/(:num)', 'Home::index/$1'); // da eliminare
$routes->get('/(:num)/(:num)', 'Home::index/$1/$2');
$routes->match(['post','get'],'/add_to_cart_product/(:num)', 'Home::add_to_cart_product/$1');
//$routes->get('/(:segment)/(:segment)', 'Home::index/$1/$2');

$routes->get('/checkout', 'Checkout::checkout',['filter'=>'minprice']);
$routes->match(['post','get'],'/compra_ora/(:segment)', 'Checkout::compra_ora/$1', ['filter'=>'minprice']);
$routes->get('/delete_cart_product/(:num)', 'Checkout::delete_cart_product/$1');

$routes->match(['post','get'],'/login','Users', ['filter'=>'maintenance']);
$routes->match(['post','get'],'/logout','Users::logout', ['filter'=>'maintenance']);
$routes->match(['post','get'],'/profile','Users::profile',['filter'=>'gestore']);

$routes->match(['post','get'],'/orders','Backend::orders',['filter'=>'auth', 'filter' => 'gestore']);
$routes->match(['post','get'],'/magazzino','Backend::magazzino',['filter'=>'auth', 'filter' => 'gestore']);
$routes->match(['post','get'],'/magazzini','Backend::magazzini',['filter'=>'auth', 'filter' => 'gestore']);
$routes->match(['post','get'],'/tables','Backend::tables',['filter'=>'auth', 'filter' => 'gestore']);
$routes->match(['post','get'],'/deletetable/(:num)','Backend::deletetable/$1',['filter'=>'auth', 'filter' => 'gestore']);
$routes->match(['post','get'],'/deleteproduct/(:num)','Backend::deleteproduct/$1',['filter'=>'auth', 'filter' => 'gestore']);
$routes->match(['post','get'],'/deletecategory/(:num)','Backend::deletecategory/$1',['filter'=>'auth', 'filter' => 'gestore']);
$routes->match(['post','get'],'/deletemagazzino/(:num)','Backend::deletemagazzino/$1',['filter'=>'auth', 'filter' => 'gestore']);
$routes->match(['post','get'],'/categories','Backend::categories',['filter'=>'auth', 'filter' => 'gestore']);

$routes->match(['post','get'],'/registration', 'Registration',['filter'=>'maintenance']);
$routes->match(['post','get'],'/registration/(:segment)', 'Registration::$1',['filter'=>'maintenance']);

$routes->add('forgot_username','Forgot_username::index', ['filter'=>'maintenanceNoAuth']);
$routes->match(['post','get'],'/forgot_username/success', 'Forgot_username::success',['filter'=>'maintenance']);
$routes->match(['post','get'],'/forgot_username/error', 'Forgot_username::error',['filter'=>'maintenance']);
$routes->match(['post','get'],'/forgot_username/(:segment)', 'Forgot_username::$1',['filter'=>'maintenance']);

$routes->add('forgot_pass','Forgot_pass::index', ['filter'=>'maintenanceNoAuth']);
// $routes->get('/forgot_pass', 'Forgot_pass::index',['filter'=>'maintenanceNoAuth']);
$routes->match(['post','get'],'/forgot_pass/success', 'Forgot_pass::success',['filter'=>'maintenance']);
$routes->match(['post','get'],'/forgot_pass/error', 'Forgot_pass::error',['filter'=>'maintenance']);
$routes->match(['post','get'],'/forgot_pass/(:segment)', 'Forgot_pass::$1',['filter'=>'maintenance']);

$routes->add('nuova_password','Nuova_password::index', ['filter'=>'maintenanceNoAuth']);
$routes->match(['post','get'],'/nuova_password/(:segment)', 'Nuova_password::$1',['filter'=>'maintenance']);

$routes->add('ticketsPrResume','PR::ticketsPrResume', ['filter'=>'maintenanceNoAuth']);
$routes->add('payTicket/(:any)','PR::payTicket/$1', ['filter'=>'maintenanceNoAuth']);
$routes->match(['post','get'],'/createEvents', 'PRRestaurant::createEvents',['filter'=>'maintenance']);
$routes->match(['post','get'],'/showEvent/(:num)', 'PRRestaurant::showEvent/$1',['filter'=>'maintenance']);

$routes->match(['post','get'],'/analytics', 'Backend::analytics',['filter'=>'auth', 'filter' => 'gestore']);
$routes->match(['post','get'],'/soldProducts/(:segment)/(:segment)', 'Backend::soldProducts/$1/$2',['filter'=>'auth', 'filter' => 'gestore']);
$routes->match(['post','get'],'/SoldTables/(:segment)/(:segment)', 'Backend::soldTables/$1/$2',['filter'=>'auth', 'filter' => 'gestore']);



//$routes->get('/stripe', 'StripeController::index');
//$routes->post('/stripe/create-charge', 'StripeController::createCharge');


$routes->match(['post','get'],'/stripe','Stripe::index');








/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
