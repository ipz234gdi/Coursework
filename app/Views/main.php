<!DOCTYPE html>
<html lang="en">

<head>
  <link rel="preload" href="/Fonts/pt-root-ui_medium.woff2" as="font" type="font/woff2" crossorigin="anonymous">

  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- <script type="importmap">
      {
        "imports": {
          "three": "https://cdn.jsdelivr.net/npm/three@0.150.1/build/three.module.js"
        }
      }
    </script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script> -->

  <link rel="stylesheet" href="/Styles/style.css">
  <link rel="stylesheet" href="/Styles/header.css">
  <link rel="stylesheet" href="/Styles/main.css">
  <link rel="stylesheet" href="/Styles/aside.css">
  <link rel="stylesheet" href="/Styles/createChannel.css">


  <script src="/Scripts/script.js"></script>
  <script src="/Scripts/aside.js"></script>
  <script src="/Scripts/profileModal.js"></script>
  <!-- <script src="/Scripts/BlackSquare.js" type="module"></script> -->
  <!-- <script src="/Scripts/Prototype1.js" type="module"></script> -->
  <!-- <script src="/Scripts/laser.js"></script> -->
  <!-- <script src="/Scripts/starspace.js"></script> -->
  <!-- <script src="/Scripts/backgraund.js"></script> -->

  <title><?= $title ?? 'Pingora' ?></title>
</head>

<body>
  <!-- <img src="/1.png" alt="" class="bg-img"> -->
  <!-- <script type="module" src="https://unpkg.com/@splinetool/viewer@1.9.82/build/spline-viewer.js"></script>
  <spline-viewer url="https://prod.spline.design/PtBQemhIWok0Kdac/scene.splinecode"></spline-viewer> -->
  <!-- <canvas id="canvas"></canvas> -->

  <?php include __DIR__ . '/../Modules/header.php'; ?>
  <?php include __DIR__ . '/../Views/channels/create.php'; ?>


  <div class="container">
    <?= $ListPost ?? '' ?>
    <?= $Register ?? '' ?>
    <?= $Login ?? '' ?>
    <?= $CreatePost ?? '' ?>
  </div>

</body>

</html>