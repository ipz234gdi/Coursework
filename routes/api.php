<?php

//Main
$router->get('/', 'HomeController@index');

$router->get('/home', 'HomeController@index');

//Login
$router->get('/register', 'AuthController@showRegister');
$router->post('/register', 'AuthController@register');

$router->get('/login', 'AuthController@showLogin');
$router->post('/login', 'AuthController@login');

//Posts
$router->get('/posts/create', 'PostController@showCreatePost');   // форма
$router->post('/posts/create', 'PostController@createPost');   // обробка
$router->get('/posts', 'PostController@index');           // список JSON


?>