<?php

//Main
$router->get('/', 'HomeController@index');

$router->get('/home', 'HomeController@index');

//Login
$router->get('/register', 'AuthController@showRegister');
$router->post('/register', 'AuthController@register');

$router->get('/login', 'AuthController@showLogin');
$router->get('/logout', 'AuthController@loguot');
$router->post('/login', 'AuthController@login');

//Posts
$router->get('/posts/create', 'PostController@showCreatePost');
$router->post('/posts/create', 'PostController@createPost');
$router->get('/posts', 'PostController@index');

//Channels
$router->post('/channels/create', 'ChannelController@createChannels');
$router->get('/channels', 'ChannelController@listUserCommunities');
$router->get('/channels/{id}', 'ChannelController@show');
?>