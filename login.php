<?php
$title="Login";
include('./includes/header.php');
?>
<div class="h-screen w-full bg-[#d2d6de]">
	<div class="container flex flex-col items-center justify-center gap-5">
		<h1 class="text-2xl text-center">Login</h1>
		<div class="bg-white p-2 w-[300px] py-3">
			<div class="space-y-2">
				<p class="text-sm text-center">Sign in to start the session</p>
				<div class="space-y-2 mx-2">
					<input type="text" placeholder="Username" class="outline-none border pl-1 py-1 rounded-sm w-full" />
				<input type="password" placeholder="Password" class="outline-none border pl-1 py-1 rounded-sm w-full" />
				</div>
				<button class="bg-blue-400 text-white p-1 mx-2">Sign In</button>
			</div>
		</div>
	</div>
</div>
<?php include('./includes/footer.php');?>