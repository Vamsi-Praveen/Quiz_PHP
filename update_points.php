<?php
session_start();
include('config/db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $userId = $_POST['userId'];
    $pointsToAdd = $_POST['points'];

    // Fetch current user points
    $stmt = $conn->prepare("SELECT points FROM user WHERE id = ?");
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $currentPoints = $user['points'];

        // Update user points
        $newPoints = $currentPoints + $pointsToAdd;
        $stmt = $conn->prepare("UPDATE user SET points = ? WHERE id = ?");
        $stmt->bind_param('ii', $newPoints, $userId);
        $stmt->execute();

        echo "Points updated successfully";
    } else {
        echo "User not found";
    }
}
?>
