<?php
	require_once("lib/connection.php");
	if ( isset($_SESSION["is_logged"]))
	{
		session_destroy();
		setcookie("session_remember","",time()-3600);
		header("Location: login.php");
		exit();
	}
	else
	{
		header("Locaion: index.php");
		exit();
	}
?>