<?php
$router->get('/', 'HomeController@index');

$router->get('/home', 'HomeController@index');

$router->get('/register', 'AuthController@showRegister');
$router->post('/register', 'AuthController@register');

$router->get('/login', 'AuthController@showLogin');
$router->post('/login', 'AuthController@login');
?>