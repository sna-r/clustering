<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require 'config.php';

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    echo "Starting login process...<br>"; // Debugging point 1

    // Prepare the SQL statement
    $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    echo "Prepared statement created.<br>"; // Debugging point 2

    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    echo "Query executed successfully.<br>"; // Debugging point 3

    // Verify user exists and check password
    if ($result->num_rows > 0) {
        echo "User found.<br>";
        $user = $result->fetch_assoc();
        $hashed_password = $user['password'];

        if ($password == $hashed_password) { // Replace this with password_verify if hashed
            echo "Password matches.<br>";
            $_SESSION['user_id'] = $user['id'];
            header("Location: accueil.php");
            exit();
        } else {
            echo "Invalid password.";
        }
    } else {
        echo "No user found with that username.";
    }

    $stmt->close();
    $conn->close();
}
?>
