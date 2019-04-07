<?php
  include("header.php"); 

  

  if ( !isset($_SESSION["is_logged"]))
  {
  	header("Location: login.php");
  	exit();
  }
?>

<?php 
  if (!$_SESSION["admin"])
  {
    echo "<!DOCTYPE html>
<html>
<head>
  <title>Lala</title>
</head>
<body>
  <center>
    <iframe width=\"668\" height=\"376\" src=\"https://www.youtube.com/embed/IHNzOHi8sJs?autoplay=1\" frameborder=\"0\" allow=\"autoplay; encrypted-media\" allowfullscreen></iframe>
  </center>
  <center>
    <strong>You are guest, only admin can do something special to get flag :) Relax and listen to this song.</strong>
  </center>
</body>
</html>";
  }
  else
  {
    echo "<!DOCTYPE html>
<html>
<head>
  <title>Lala</title>
</head>
<body>
  <center>
    <iframe width=\"668\" height=\"376\" src=\"https://www.youtube.com/embed/NeDeZUqNiVo?autoplay=1\" frameborder=\"0\" allow=\"autoplay; encrypted-media\" allowfullscreen></iframe>
  </center>
  <center>
    <strong>You are admin, there is an function that is misimplemented for you :) Relax and listen to this song.</strong>
  </center>
</body>
</html>";
  }
?>