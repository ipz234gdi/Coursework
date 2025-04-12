<?php
$styles = [
    '/Styles/style.css',
    '/Styles/main.css',
    '/Styles/header.css',
    '/Styles/aside.css'
];
?>

<?php foreach ($styles as $style): ?>
    <link rel="stylesheet" href="<?php echo $style ?>">
<?php endforeach; ?>