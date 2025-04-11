<?php
$title = 'Головна - Pingora';

ob_start();

require __DIR__ . '/../Modules/main.php';

$ListPost = ob_get_clean();

?>