<?php
session_start();
if(!(isset($_SESSION['adminID']))){
	header("location:login.php");
	exit();
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Admin Panel</title>
	<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
	<div class="min-h-screen w-full flex">
		<div class="sidebar w-[20%] bg-[#222d32] text-white/90 flex flex-col">
			<div class="text-center bg-blue-400 py-3">
				<a href="index.php" class="font-medium text-xl text-center">ADMIN PANEL</a>	
			</div>
			<ul class="mt-3 space-y-2 h-[80%]">
				<a href="add.php">
					<li class="border-b border-[#2d3c42] cursor-pointer hover:bg-[#2d3c42] py-2 pl-2">
						Add New Test
					</li>
				</a>
			</ul>
			<div class=" mx-2">
				<a class="bg-red-400 px-3 py-2 outline-none" href="logout.php">Logout</a>
			</div>
		</div>
		<div class="content flex-1 bg-[#ecf0f5] flex items-center justify-center">
			<h1 class="text-3xl">Welcome to Admin Panel.</h1>
		</div>
	</div>
</body>
</html>