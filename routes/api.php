<?php
$router->get('/posts/popular', 'PostController@popular');
$router->post('/posts', 'PostController@store');
$router->get('/channels', 'ChannelController@index');

?>