<?php
$title="Login";
include('./includes/header.php');
?>
<?php
include('./config/db.php');
$loginError=false;
$error_message = "";
if($_SERVER['REQUEST_METHOD']=='POST'){
	$username = $_POST['username'];
	$password = $_POST['password'];

	$query = "SELECT * FROM user WHERE username = ?";
	$stmt = $conn->prepare($query);
	$stmt->bind_param('s',$username);
	$stmt->execute();
	$result = $stmt->get_result();
	if($result->num_rows > 0){
		$res = $result->fetch_assoc();
		$dbPassword = $res['password'];

		if($dbPassword == $password){
			$_SESSION['username'] = $username;
			$_SESSION['userId'] = $res['ID'];
			header('location: index.php');
			exit();
		} else {
			$loginError = true;
			$error_message = 'Invalid Credentials';
		}
	} else {
		$loginError = true;
		$error_message = "User Not Found";
	}

}

?>
<div class="h-screen w-full bg-[#d2d6de]">
	<div class="container flex flex-col items-center justify-center gap-5">
		<h1 class="text-3xl text-center">Login</h1>
		<div class="bg-white p-2 w-[300px] py-3 rounded-sm">
			<form method="post" action="">
				<div class="space-y-2">
					<p class="text-sm text-center">Sign in to start the session</p>
					<?php echo $loginError==true ? '<p class="text-sm text-red-500 text-center">'.$error_message.'</p>' : ''?>
					<div class="space-y-2 mx-2">
						<input type="text" placeholder="Username" class="outline-none border pl-1 py-1 rounded-sm w-full" name="username" required/>
						<input type="password" placeholder="Password" class="outline-none border pl-1 py-1 rounded-sm w-full" name="password" required />
					</div>
					<button class="bg-blue-400 text-white p-1 mx-2 px-2" type="submit">Sign In</button>
				</div>
			</form>
		</div>
	</div>
</div>
<?php include('./includes/footer.php');?>