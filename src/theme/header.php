<?php
  require __DIR__.'/../class/db.php';
?>
<html>
<head>
  <base href="<?php echo HOMEURI?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <link rel="stylesheet" href="theme/style/main.css">
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
      <li><a href="page/login.php"><b>Login</b></a></li>
    </ul>
  </header>
<main style="padding: 30px;">