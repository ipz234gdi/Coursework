<!DOCTYPE html>
<html lang="en">

<head>
  <link rel="preload" href="/Fonts/pt-root-ui_medium.woff2" as="font" type="font/woff2" crossorigin="anonymous">
  <link rel="preload" href="/Fonts/bedstead.otf" as="font" type="font/otf" crossorigin="anonymous">
  <link rel="preload" href="/Fonts/DepartureMono-Regular.woff2" as="font" type="font/woff2" crossorigin="anonymous">

  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link rel="stylesheet" href="/Styles/style.css">
  <link rel="stylesheet" href="/Styles/header.css">
  <link rel="stylesheet" href="/Styles/main.css">
  <link rel="stylesheet" href="/Styles/aside.css">
  <link rel="stylesheet" href="/Styles/createChannel.css">


  <script src="/Scripts/script.js"></script>
  <script src="/Scripts/aside.js"></script>
  <script src="/Scripts/profileModal.js"></script>

  <title><?= $title ?? 'Pingora' ?></title>
</head>

<body>
  <?php include __DIR__ . '/header.php'; ?>
  <?php include __DIR__ . '/../channels/create.php'; ?>

  <div class="container">
    <?php include __DIR__ . '/aside.php'; ?>

    <main class="main-content">
      <?= $content /* весь «тултіп» сторінки */ ?>
    </main>
  </div>

</body>

</html>