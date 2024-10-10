<?php
include('../config/db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $username = $_POST['username'];
    $points = $_POST['points'];

    $query = "UPDATE user SET name = ?, username = ?, points = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ssii', $name, $username, $points, $id);

    if ($stmt->execute()) {
        echo 'User updated successfully!';
    } else {
        echo 'Failed to update user.';
    }
}
?>
