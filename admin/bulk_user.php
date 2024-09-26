<?php
session_start();
include('../config/db.php');

if (!(isset($_SESSION['adminID']))) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] == 0) {
        $file = $_FILES['csv_file']['tmp_name'];

        if (($handle = fopen($file, 'r')) !== FALSE) {
            $users = [];

            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                if (count($data) >= 1) {
                    $name = $data[0];
                    $username = $data[1];
                    $password = $data[2];
                    $users[] = [
                        'name' => $name,
                        'username' => $username,
                        'password' => password_hash($password, PASSWORD_DEFAULT)
                    ];
                }
            }
            fclose($handle);

            foreach ($users as $user) {
                $stmt = $conn->prepare("INSERT INTO user (name, username, password) VALUES (?, ?, ?)");
                $stmt->bind_param('sss', $user['name'], $user['username'], $user['password']);
                if (!$stmt->execute()) {
                    echo "<script>alert('Error inserting user: " . htmlspecialchars($user['username']) . " - " . $stmt->error . "');</script>";
                }
            }

            echo "<script>
                    alert('Users uploaded successfully!');
                    window.location.href = 'index.php'; // Redirect to index.php
                  </script>";
            exit;
        } else {
            echo "<script>alert('Error opening file.')</script>";
        }
    } else {
        echo "<script>alert('Error: " . $_FILES['csv_file']['error'] . "');</script>";
    }
}
?>