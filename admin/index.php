<?php
	session_start();
	if(!(isset($_SESSION['adminID']))){
	header("location:login.php");
	exit();
}
?>