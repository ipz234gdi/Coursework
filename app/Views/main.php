<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">


    <link rel="stylesheet" href="/Styles/style.css">
    <link rel="stylesheet" href="/Styles/header.css">
    <link rel="stylesheet" href="/Styles/main.css">
    <link rel="stylesheet" href="/Styles/aside.css">

    <script src="/Scripts/aside.js"></script>

    <title><?= $title ?? 'Pingora' ?></title>
</head>

<body>
    <?php include __DIR__ . '/../Modules/header.php'; ?>
    
    <div class="container">
        <?= $ListPost ?? '' ?>
        <?= $Register ?? '' ?>
        <?= $Login ?? '' ?>
        <?= $CreatePost ?? '' ?>
    </div>
</body>

</html>