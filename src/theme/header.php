<?php
  require __DIR__.'/../class/threej.php';
?>
<html>
<head>
  <base href="<?php echo HOMEURI?>">
  <link rel="stylesheet" href="theme/style/brands.min.css">
  <link rel="stylesheet" href="theme/style/fontawesome.min.css">
  <link rel="stylesheet" href="theme/style/main.css">
  <script src="theme/script/brands.min.js"></script>
  <script src="theme/script/fontawesome.min.js"></script>
  <script rel="preload" as="script" src="theme/script/jquery-3.6.0.min.js"></script>
  <script src="theme/script/main.js"></script>
</head>
<body>
  <header class="flexrow white">
    <div class="flexrow flexasc">
      <a href="index.php" class="flexrow">
        <img class="logo brad50" src="theme/img/logo.png" alt="Blockchain Based Voting system" height="40px" width="40px">
        <h3 style="font-weight: 800;letter-spacing: -1px;">BBVS</h3>
      </a>
    </div>
    <ul class="flexrow">
      <li><a href="#"><b>About</b></a></li>
      <?php 
        echo isset($_SESSION['username']) ? 
        '<li><a href="page/logout.php"><b><i class="fa fa-sign-out-alt fa-sm"></i> Logout</b></a></li>':
        '<li><a href="page/login.php"><b><i class="fa fa-sign-in-alt fa-sm"></i> Login</b></a></li>';
      ?>
      
    </ul>
  </header>
<main style="padding: 30px;">