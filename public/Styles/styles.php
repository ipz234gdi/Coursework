<?php
$styles = [
    '/Styles/style.css',
    '/Styles/sty_main.css',
    '/Styles/header.css'
];
?>

<?php foreach ($styles as $style): ?>
    <link rel="stylesheet" href="<?php echo $style ?>">
<?php endforeach; ?>