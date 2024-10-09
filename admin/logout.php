<?php
	session_start();
	unset($_SESSION['adminID']);
	unset($_SESSION['adminUsername']);
	// header('location:login.php');
	echo "<script>window.location.href='login.php'</script>";
?>