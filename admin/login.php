<?php
session_start();
if(isset($_SESSION['adminID'])){
    header("location:index.php");
}
$title="Login";
include('../includes/header.php');
?>
<?php
include('../config/db.php');
$loginError=false;
$error_message = "";
if($_SERVER['REQUEST_METHOD']=='POST'){
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = "SELECT * FROM admin WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();
    if($result->num_rows > 0){
        $res = $result->fetch_assoc();
        $dbPassword = $res['password'];

        if(password_verify($password, $dbPassword)){
            $_SESSION['adminUsername'] = $username;
            $_SESSION['adminID'] = $res['ID'];
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
<div class="min-h-screen flex items-center justify-center bg-gray-100 px-4 sm:px-0">
    <div class="w-full max-w-sm bg-white p-6 rounded-lg shadow-md">
        <h1 class="text-2xl font-semibold text-center text-gray-700 mb-4">Admin Login</h1>
        <form method="post" action="" class="space-y-4">
            <p class="text-center text-gray-600 text-sm">Sign in to start your session</p>
            <?php if($loginError): ?>
                <p class="text-sm text-red-500 text-center"><?php echo $error_message; ?></p>
            <?php endif; ?>
            <div class="space-y-3">
                <div>
                    <input type="text" name="username" placeholder="Username" class="w-full p-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
                <div>
                    <input type="password" name="password" placeholder="Password" class="w-full p-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
            </div>
            <button type="submit" class="w-full bg-blue-500 text-white py-2 rounded-md hover:bg-blue-600 transition duration-150">Sign In</button>
        </form>
    </div>
</div>
<?php include('../includes/footer.php');?>
