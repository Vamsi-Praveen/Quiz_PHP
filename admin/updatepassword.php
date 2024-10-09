<?php
session_start();
include('../config/db.php');

if (!(isset($_SESSION['adminID']))) {
    echo "<script>window.location.href='login.php'</script>";
    exit();
}

$response = ['status' => 'error', 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['username'])) {
        $username = $_POST['username'];

        // Prepare SQL to search for the user by username
        $stmt = $conn->prepare("SELECT * FROM user WHERE username = ?");
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $response = ['status' => 'success', 'user' => $user];
        } else {
            $response['message'] = 'User not found.';
        }

        echo json_encode($response);
        exit();
    } elseif (isset($_POST['user_id']) && isset($_POST['new_password'])) {
        $userId = $_POST['user_id'];
        $newPassword = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

        // Update the user's password
        $stmt = $conn->prepare("UPDATE user SET password = ? WHERE id = ?");
        $stmt->bind_param('si', $newPassword, $userId);
        
        if ($stmt->execute()) {
            $response = ['status' => 'success', 'message' => 'Password updated successfully.'];
        } else {
            $response['message'] = 'Failed to update password.';
        }

        echo json_encode($response);
        exit();
    }
}
?>
