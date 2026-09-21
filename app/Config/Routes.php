<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');
$routes->get('/hello', 'Home::hello');
$routes->get('/hello/(:segment)', 'Home::hello/$1');
$routes->get('/weather', 'Home::weather');
$routes->get('/weather/logs', 'Home::weatherLogs');