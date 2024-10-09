<?php
session_start();
if (!(isset($_SESSION['adminID']))) {
    // header("Location: login.php");
    echo "<script>window.location.href='login.php'</script>";
    exit();
}
?>
<?php
    include('../config/db.php');

    if($_SERVER['REQUEST_METHOD']=='POST'){
        $name = $_POST['fullname'];
        $username = $_POST['username'];
        $password = $_POST['password'];
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

         $stmt = $conn->prepare("INSERT INTO user (name, username, password) VALUES (?, ?, ?)");
                $stmt->bind_param('sss', $name,$username,$hashedPassword);
                if (!$stmt->execute()) {
                    echo "<script>alert('Error inserting user: " . htmlspecialchars($user['username']) . " - " . $stmt->error . "');</script>";
                }
                echo "<script>alert('Added Successfull');</script>";
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel | Add Users</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen w-full flex flex-col lg:flex-row">
        <!-- Sidebar -->
        <?php include('./includes/sidebar.php')?>

        <!-- Main Content -->
        <div class="content flex-1 bg-[#ecf0f5] p-6 flex items-center justify-center">
            <div class="w-full max-w-2xl">
                <h1 class="text-3xl font-semibold mb-6 text-center">Add Users</h1>
                
                <div class="bg-white p-8 rounded-lg shadow-lg">
                    <!-- Single User Form -->
                    <form class="space-y-6" action="" method="post">
                        <h2 class="text-xl font-medium mb-4">Add Single User</h2>

                        <div class="flex flex-col md:flex-row items-center md:space-x-4 space-y-4 md:space-y-0">
                            <label class="w-full md:w-1/4 text-gray-700 font-medium">Full Name</label>
                            <input type="text" name="fullname" class="border w-full md:w-3/4 p-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        </div>

                        <div class="flex flex-col md:flex-row items-center md:space-x-4 space-y-4 md:space-y-0">
                            <label class="w-full md:w-1/4 text-gray-700 font-medium">Username</label>
                            <input type="text" name="username" class="border w-full md:w-3/4 p-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        </div>
                        
                        <div class="flex flex-col md:flex-row items-center md:space-x-4 space-y-4 md:space-y-0">
                            <label class="w-full md:w-1/4 text-gray-700 font-medium">Password</label>
                            <input type="password" name="password" class="border w-full md:w-3/4 p-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        </div>

                        <div class="text-right">
                            <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 transition duration-150">Add User</button>
                        </div>
                    </form>

                    <div class="mt-10 mb-4 text-center text-gray-500">OR</div>

                    <!-- Bulk Upload Form -->
                    <form class="space-y-6" action="bulk_user.php" method="post" enctype="multipart/form-data">
                        <h2 class="text-xl font-medium mb-4">Add Bulk Users</h2>

                        <div class="flex flex-col md:flex-row items-center md:space-x-4 space-y-4 md:space-y-0">
                            <label class="w-full md:w-1/4 text-gray-700 font-medium">Upload CSV</label>
                            <input type="file" name="csv_file" class="border w-full md:w-3/4 p-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        </div>

                        <div class="text-right">
                            <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 transition duration-150">Upload</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Additional JS logic if needed
    </script>
</body>
</html>
