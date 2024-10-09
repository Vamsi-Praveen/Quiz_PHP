<?php
	session_start();
	unset($_SESSION['userId']);
	unset($_SESSION['username']);
	// header("location:login.php");
	echo "<script>window.location.href='login.php'</script>";


?>