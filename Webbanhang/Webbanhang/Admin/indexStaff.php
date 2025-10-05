<?php
ob_start(); // GIẢI QUYẾT LỖI HEADER
session_start(); // Gọi session_start() ngay từ đầu file
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Trang quản trị</title>
  <link rel="shortcut icon" type="image/png" href="./assets/images/logos/favicon.png" />
  <link rel="stylesheet" href="./assets/css/Template/styles.min.css" />
  <link rel="stylesheet" href="./assets/css/Template/errors.css" />
  <link rel="stylesheet" href="./assets/css/style.css" />
  <link rel="stylesheet" href="./assets/fontawesome-free-6.7.2-web/fontawesome-free-6.7.2-web/css/all.min.css">
</head>

<body>
    <h1>Xin chào staff</h1>

  <!-- JS -->
  <script src="./assets/libs/jquery/dist/jquery.min.js"></script>
  <script src="./assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
  <script src="./assets/js/sidebarmenu.js"></script>
  <script src="./assets/js/app.min.js"></script>
  <script src="./assets/libs/apexcharts/dist/apexcharts.min.js"></script>
  <script src="./assets/libs/simplebar/dist/simplebar.js"></script>
  <script src="./assets/js/dashboard.js"></script>
  <script src="./assets/js/ajax-load.js"></script>
  <!-- solar icons -->
  <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>

  <!-- JS bạn copy hôm trước để load AJAX -->
  <script src="./assets/js/ajax-load.js"></script>
</body>
</html>
