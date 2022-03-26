<?php
  require __DIR__.'/../class/threej.php';
  require ROOTDIR .'class/settings.php';
  $t->query('SELECT * FROM settings',);
  $t->execute();
  $data = $t->fetchAll();
  $website = [];
  
  foreach($data as $e => $v){$website[$v['FIELD']] = $v['VALUE'];}
  
?>
<html>
<head>
  <title><?= $website['title'] ?></title>
  <base href="<?= HOMEURI ?>">
  <link rel="stylesheet" href="theme/style/brands.min.css">
  <link rel="stylesheet" href="theme/style/fontawesome.min.css">
  <link rel="stylesheet" href="theme/style/main.css">
  <link rel="shortcut icon" href="theme/img/logo.png" type="image/png">
  <script src="theme/script/brands.min.js"></script>
  <script src="theme/script/fontawesome.min.js"></script>
  <script rel="preload" as="script" src="theme/script/jquery-3.6.0.min.js"></script>
  <script src="theme/script/web3.min.js"></script>
  <script src="theme/script/main.js"></script>
  <style>
    header span.profile{
      border-radius:10px 10px 0 0;
      display:flex;
      padding-right:10px;
      column-gap: 10px;
      padding: 10px;
    }
    header span.profile:hover{
      background-color: #e3e3e3;
      cursor: pointer;
    }
    header span.profile:hover li.dropdown{
      display: block;
    }
    li.dropdown{
      display: none;
      position: absolute;
      top: calc(100% - 0%);
      background-color: #e3e3e3;
      width: 100%;
      padding: 15px 0 20px 17px;
      border-radius: 0 0 10px 10px;
      left: 0px;
    }
  </style>
</head>
<body>
  <header class="flexrow white">
    <div class="flexrow flexasc">
      <a href="index.php" class="flexrow" style="text-decoration: none;">
        <img class="logo brad50" src="theme/img/logo.png" alt="Blockchain Based Voting system" height="40px" width="40px">
        <h3 style="font-weight: 800;letter-spacing: -1px;"><?= $website['title'] ?></h3>
      </a>
    </div>
    <ul class="flexrow">
      <?php 
        if(isset($_SESSION['username'])){
          $pp = 'contents/img/profilepic/'.$_SESSION['username'];
          is_file(ROOTDIR.$pp.'.jpg') 
          ? $pp = $pp.'.jpg' 
          : (is_file(ROOTDIR.$pp.'.jpeg') 
            ? $pp = $pp.'.jpeg' 
            : (is_file(ROOTDIR.$pp.'.png') 
              ? $pp = $pp.'.png' 
              : $pp = 'contents/img/profilepic/boy.jpg'))
          ;
        }
        echo isset($_SESSION['username']) ? 
        '<span class="flexacc profile">
          <a href="page/dashboard.php" class="flexrow flexacc" style="column-gap:10px;">
            <img src="'.$pp.'" class="brad50" style="border:1px solid grey;" height="35px" width="35px" />'.$_SESSION['username'].'
          </a>
          <li class="dropdown">
            
            <i class="fa fa-poll fa-md" style="padding-right:5px"></i>
            <a href="page/dashboard.php?show=mypolls">
              My polls
            </a><hr>
            
            <i class="fa fa-user fa-md" style="padding-right:5px"></i>
            <a href="page/dashboard.php?show=profile">
              Profile
            </a><hr>
            <i class="fa fa-sign-out-alt fa-sm" style="padding-right:5px"></i>
            <a href="page/logout.php">
              Log out
            </a>
          </li>
        </span>':
        '<li><a href="page/login.php"><i class="fa fa-sign-in-alt fa-sm"></i> Log In</a></li>';
      ?>
      <li><a href="page/dashboard.php?show=newpoll"><button class="blue"><i class="fa fa-plus" ></i> Create Poll</button></a></li>
    </ul>
  </header>
<main style="padding: 30px;">