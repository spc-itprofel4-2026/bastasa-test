<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');
$routes->get('/hello', 'Home::hello');
$routes->get('/hello/(:segment)', 'Home::hello/$1');
$routes->get('/weather', 'Home::weather');
$routes->get('/weather/logs', 'Home::weatherLogs');
$routes->group(
    'api/v1',
    ['namespace' => 'App\Controllers\Api\V1', 'filter' => 'apikey'],
    static function ($routes) {
        $routes->post('weather/refresh', 'Weather::refresh');
        $routes->get('weather/logs', 'Weather::logs');
        $routes->get('weather/logs/(:num)', 'Weather::show/$1');
    }
);
