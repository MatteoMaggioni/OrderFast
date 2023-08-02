<?php namespace Config;

use CodeIgniter\Config\BaseConfig;

class Filters extends BaseConfig
{
	// Makes reading things below nicer,
	// and simpler to change out script that's used.
	public $aliases = [
		'csrf'     => \CodeIgniter\Filters\CSRF::class,
		'toolbar'  => \CodeIgniter\Filters\DebugToolbar::class,
		'honeypot' => \CodeIgniter\Filters\Honeypot::class,
		'auth' => [
			\App\Filters\AuthFilter::class,
			\App\Filters\LiberatoriaFilter::class,
		],
		'authNoLiberatoria' => [
			\App\Filters\AuthFilter::class,
		],
		'liberatoria' => \App\Filters\LiberatoriaFilter::class,
		'noauth' => \App\Filters\NoauthFilter::class,
		'admin' => \App\Filters\AdminFilter::class,
		'super' => \App\Filters\SuperFilter::class,
		'maintenance' => \App\Filters\MaintenanceFilter::class,
		'gestore' => \App\Filters\GestoreFilter::class,
		'minprice' => \App\Filters\MinPriceFilter::class,
		'maintenanceNoAuth' => [
			\App\Filters\MaintenanceFilter::class,
			\App\Filters\NoauthFilter::class
		]
	];

	// Always applied before every request
	public $globals = [
		'before' => [
			//'honeypot'
			// 'csrf',
		],
		'after'  => [
			//'toolbar',
			//'honeypot'
		],
	];

	// Works on all of a particular HTTP method
	// (GET, POST, etc) as BEFORE filters only
	//     like: 'post' => ['CSRF', 'throttle'],
	public $methods = [];

	// List filter aliases and any before/after uri patterns
	// that they should run on, like:
	//    'isLoggedIn' => ['before' => ['account/*', 'profiles/*']],
	public $filters = [

	];
}
