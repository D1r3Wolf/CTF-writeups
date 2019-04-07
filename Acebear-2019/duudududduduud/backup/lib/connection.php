<?php 
session_start();
include "aes.php";
use PhpAes\Aes;
$servername = "localhost";
$username = "";
$password = "";
$dbname = "";
$key = "";
$flag = "flag_here";
$conn = new mysqli($servername, $username, $password, $dbname) or die("Fail");

function check_input($inp)
{
	if (!preg_match("/[^a-z0-9A-Z]/i", $inp))
	{
		return 1;
	}
	return 0;
}

function gen_cookie($username,$key)
{
	$username = $username."|thisisareallyreallylongstringasfalsfassfasfaasff";
	$aes = new Aes($key, 'CBC', $key);
	return base64_encode($aes->encrypt($username));
}

function check_cookie($token,$key)
{
	$aes = new Aes($key, 'CBC', $key);
	$token = base64_decode($token);
	return $aes->decrypt($token);
}

?>