<?php
session_start();
include('../config/db.php');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!(isset($_SESSION['adminID']))) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] == 0) {
        $file = $_FILES['csv_file']['tmp_name'];

        if (($handle = fopen($file, 'r')) !== FALSE) {
            $users = [];
            $duplicateUsernames = []; 
            $insertedUsers = [];      

            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                if (count($data) >= 3) {
                    $name = $data[0];
                    $username = $data[1];
                    $password = $data[2];
                    
                    $stmt = $conn->prepare("SELECT * FROM user WHERE username = ?");
                    $stmt->bind_param('s', $username);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        $duplicateUsernames[] = $username;
                    } else {
                        $users[] = [
                            'name' => $name,
                            'username' => $username,
                            'password' => password_hash($password, PASSWORD_DEFAULT)
                        ];
                    }
                }
            }
            fclose($handle);

            foreach ($users as $user) {
                $stmt = $conn->prepare("INSERT INTO user (name, username, password) VALUES (?, ?, ?)");
                $stmt->bind_param('sss', $user['name'], $user['username'], $user['password']);
                if ($stmt->execute()) {
                    $insertedUsers[] = $user['username'];
                } else {
                    echo "<script>alert('Error inserting user: " . htmlspecialchars($user['username']) . " - " . $stmt->error . "');</script>";
                }
            }

            $message = "Users uploaded successfully!\n";
            if (!empty($insertedUsers)) {
                $message .= "Inserted Users: " . implode(", ", $insertedUsers) . ".\n";
            }
            if (!empty($duplicateUsernames)) {
                $message .= "Duplicate Usernames (skipped): " . implode(", ", $duplicateUsernames) . ".";
            }

            echo "<script>
            alert(\"" . str_replace("\n", "\\n", $message) . "\");
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
