<?php
  require_once("lib/connection.php");  
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Web web web</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <script src="css/jquery.min.js"></script>
  <script src="css/popper.min.js"></script>
  <script src="css/bootstrap.min.js"></script>

</head>
<body>

<nav class="navbar navbar-expand-sm bg-dark navbar-dark">
  <!-- Brand -->
  <a class="navbar-brand" href="index.php">AceBear-komang</a>

  <!-- Links -->
  <ul class="navbar-nav">
      <?php
      if ( isset($_SESSION['is_logged']))
      {
        if ( $_SESSION["admin"] )
        {
          echo "<li class='nav-item'>
          <a class='nav-link' href='#' style='position: absolute;top: 8px;right: 210px;font-size: 18px;'>Hello ".htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8')."</a>
          </li>";
          echo "<li class='nav-item'>
          <a class='nav-link' href='upload.php' style='position: absolute;top: 8px;right: 120px;font-size: 18px;'>Upload</a>
          </li>
                <li class='nav-item'>
          <a class='nav-link' href='logout.php' style='position: absolute;top: 8px;right: 16px;font-size: 18px;'>Log out</a>
          </li>"; 
        }
        else
        {
          echo "<li class='nav-item'>
          <a class='nav-link' href='#' style='position: absolute;top: 8px;right: 150px;font-size: 18px;'>Hello ".htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8')."</a>
          </li>
                <li class='nav-item'>
          <a class='nav-link' href='logout.php' style='position: absolute;top: 8px;right: 16px;font-size: 18px;'>Log out</a>
          </li>"; 
        }
      } 
      else
      {
        echo "<li class='nav-item'>
      <a class='nav-link' href='register.php' style='position: absolute;top: 8px;right: 120px;font-size: 18px;'>Register</a>
    </li>
        <li class='nav-item'>
      <a class='nav-link' href='login.php' style='position: absolute;top: 8px;right: 16px;font-size: 18px;'>Login</a>
    </li>";
      }
  ?>
  </ul>
</nav>
<br>
  
