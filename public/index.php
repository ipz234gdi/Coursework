<?php
// Pingora
session_start();

require '../config/database.php';
require_once '../app/Router.php';
$router = new Router();

require_once '../routes/api.php';

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
?>

