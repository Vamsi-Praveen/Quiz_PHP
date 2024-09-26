<?php
session_start();
$title="Login";
include('./includes/header.php');
if(isset($_SESSION['userId'])){
	header('location:index.php');
	exit();
}
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

		if(password_verify($password, $dbPassword)){
			$_SESSION['username'] = $username;
			$_SESSION['userId'] = $res['ID'];

			if(isset($_GET['redirect'])){
				$redirectUrl = urldecode(mysqli_real_escape_string($conn,$_GET['redirect']));
				header('location:'.$redirectUrl);
			}
			else
			{
				header('location: index.php');
			}
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
<div class="h-screen w-full">
	<div class="container flex flex-col items-center justify-center gap-5">
		<div class="space-y-2 text-gray-600">
			<h1 class="text-4xl text-center font-medium">Login to your account.</h1>
			<p class="text-md text-center">Sign in to start the session</p>
		</div>
		<div class="bg-white p-2 w-full py-4 px-6 rounded-lg max-w-md shadow-sm">
			<form method="post" action="">
				<div class="space-y-2">
					<?php echo $loginError==true ? '<p class="text-md text-red-500 text-center font-medium">'.$error_message.'</p>' : ''?>
					<div class="space-y-4 mx-2 text-gray-600">
						<div class="space-y-1">
							<label class="text-[16px] font-medium tracking-wide">Username</label>
							<input type="text" class="appearance-none block w-full px-3 py-2.5 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-gray-500 focus:border-gray-500" name="username" required autocomplete="off" />
						</div>
						<div class="space-y-1">
							<label class="text-[16px] font-medium tracking-wide">Password</label>
							<input type="password" class="appearance-none block w-full px-3 py-2.5 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-gray-500 focus:border-gray-500" name="password" required autocomplete="off" />
						</div>
					</div>
				</div>
				 <div class="px-2 mt-4">
                    <button type="submit"
                            class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-md font-medium text-white bg-green-600 hover:bg-green-500 focus:outline-none">
                        Sign in
                    </button>
                </div>
			</form>
		</div>
	</div>
</div>
<?php include('./includes/footer.php');?>