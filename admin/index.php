<?php
session_start();
if (!(isset($_SESSION['adminID']))) {
    header("location:login.php");
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
        <div class="sidebar w-full lg:w-[20%] bg-[#222d32] text-white flex flex-col">
            <div class="text-center bg-blue-500 py-4">
                <a href="index.php" class="font-bold text-lg">ADMIN PANEL</a>
            </div>
            <ul class="mt-6 space-y-2 flex-1">
                <a href="add.php">
                    <li class="border-b border-[#2d3c42] cursor-pointer hover:bg-[#2d3c42] py-3 pl-4 transition-colors duration-150">
                        Add New Test
                    </li>
                </a>
                <a href="addusers.php">
                    <li class="border-b border-[#2d3c42] cursor-pointer hover:bg-[#2d3c42] py-3 pl-4 transition-colors duration-150">
                        Add Users
                    </li>
                </a>
                <a href="genreport.php">
                    <li class="border-b border-[#2d3c42] cursor-pointer hover:bg-[#2d3c42] py-3 pl-4 transition-colors duration-150">
                        Generate Report
                    </li>
                </a>
            </ul>
            <div class="mb-6 text-center">
                <a class="bg-red-500 text-white py-2 px-6 rounded-lg hover:bg-red-600 transition duration-150" href="logout.php">Logout</a>
            </div>
        </div>

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
