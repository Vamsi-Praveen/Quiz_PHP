<?php
session_start();
include('../config/db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $testId = $_POST['id'];

    $stmt = $conn->prepare("DELETE FROM tests WHERE id=?");
    $stmt->bind_param("i", $testId);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error']);
    }

    $stmt->close();
    $conn->close();
}
?>
