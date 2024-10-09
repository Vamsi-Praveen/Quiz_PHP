<?php
session_start();
if (!(isset($_SESSION['adminID']))) {
    // header("location:login.php");
    echo "<script>window.location.href='login.php'</script>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
    <div class="min-h-screen w-full flex flex-col lg:flex-row">
        <!-- Sidebar -->
        <?php include('./includes/sidebar.php')?>

        <!-- Main Content -->
        <div class="content flex-1 bg-[#ecf0f5] flex items-center justify-center p-6">
            <div class="text-center">
                <h1 class="text-4xl font-semibold text-gray-800 mb-4">Welcome to the Admin Panel</h1>
                <p class="text-lg text-gray-600">Manage your system efficiently and securely</p>
            </div>
        </div>
    </div>
</body>

</html>
