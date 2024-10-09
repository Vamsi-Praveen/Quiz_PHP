<?php
session_start();
include('../config/db.php');

if (!(isset($_SESSION['adminID']))) {
    // header("Location: login.php");
    echo "<script>window.location.href='login.php'</script>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $testId = $_POST['test_id'];

    $stmt = $conn->prepare("
        SELECT 
            u.username,
            (SELECT COUNT(*) FROM questions WHERE test_id = ?) AS total_questions,
            COUNT(ur.is_correct) AS total_responses,
            SUM(ur.is_correct) AS correct_answers
        FROM 
            user u
        JOIN 
            user_responses ur ON u.id = ur.user_id
        WHERE 
            ur.test_id = ? AND
            JSON_CONTAINS(u.completed_tests,?,'$')
        GROUP BY 
            u.username
        ORDER BY 
            u.username
    ");
    $testIdString = json_encode((string)$testId);
    $stmt->bind_param('iis', $testId,$testId,$testIdString);  
    $stmt->execute();
    $result = $stmt->get_result();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel | Reset Password</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen w-full flex flex-col lg:flex-row">
        <!-- Sidebar -->
        <?php include('./includes/sidebar.php')?>

        <!-- Main Content -->
        <div class="content flex-1 bg-[#ecf0f5] p-4 lg:p-8">
            <div class="w-full">
                <h1 class="text-2xl font-semibold mb-6">Reset User Password</h1>
                <div class="w-full lg:w-1/2 bg-white p-6 rounded-md shadow-lg">
                        <h2 class="text-xl font-medium mb-4 text-center">Password Reset</h2>
                        <div class="space-y-4">
                            <div class="flex flex-col md:flex-row items-center md:space-x-4 space-y-4 md:space-y-0">
                                <label class="w-full md:w-1/4 text-gray-700 font-medium">Username</label>
                                <input type="text" name="username" id="username" class="border w-full md:w-3/4 p-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            </div>
                        </div>
                        <div class="text-right mt-4">
                            <button id="findUser" class="bg-green-500 px-4 py-2 text-white rounded hover:bg-green-600 transition duration-150">Find User</button>
                        </div>
                </div>
                <div id="passwordResetForm" class="mt-8 hidden">
                    <div class="w-full lg:w-1/2 bg-white p-6 rounded-md shadow-lg">
                        <h2 class="text-xl font-medium mb-4 text-center">Set New Password for <span id="displayUsername"></span></h2>
                        <input type="hidden" id="userId">
                        <div class="space-y-4">
                            <div class="flex flex-col md:flex-row items-center md:space-x-4 space-y-4 md:space-y-0">
                                <label class="w-full md:w-1/4 text-gray-700 font-medium">New Password</label>
                                <input type="password" id="newPassword" class="border w-full md:w-3/4 p-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            </div>
                        </div>
                        <div class="text-right mt-4">
                            <button id="updatePassword" class="bg-green-500 px-4 py-2 text-white rounded hover:bg-blue-600 transition duration-150">Update Password</button>
                        </div>
                    </div>
                </div>
                
                
            </div>
        </div>
    </div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const findUserBtn = document.getElementById('findUser');
        const updatePasswordBtn = document.getElementById('updatePassword');
        const usernameInput = document.getElementById('username');
        const userIdInput = document.getElementById('userId');
        const newPasswordInput = document.getElementById('newPassword');
        const passwordResetForm = document.getElementById('passwordResetForm');
        const displayUsername = document.getElementById('displayUsername');

        findUserBtn.addEventListener('click', () => {
            const username = usernameInput.value;

            fetch('./updatepassword.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({ username: username })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    displayUsername.textContent = data.user.username;
                    userIdInput.value = data.user.ID;
                    passwordResetForm.classList.remove('hidden');
                } else {
                    usernameInput.value='';
                    alert(data.message);

                }
            })
            .catch(error => console.error('Error:', error));
        });

        updatePasswordBtn.addEventListener('click', () => {
            const userId = userIdInput.value;
            const newPassword = newPasswordInput.value;

            fetch('./updatepassword.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    user_id: userId,
                    new_password: newPassword
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    alert(data.message);
                    passwordResetForm.classList.add('hidden');
                    window.location.reload();

                } else {
                    alert(data.message);
                    window.location.reload();
                }
            })
            .catch(error => console.error('Error:', error));
        });
    });
</script>

</body>
</html>
