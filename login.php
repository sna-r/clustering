<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require 'config.php';


if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare the SQL statement
    $result = pg_prepare($conn, "login_query", "SELECT id, password FROM users WHERE username = $1");
    $result = pg_execute($conn, "login_query", array($username));

    // Verify user exists and check password
    if (pg_num_rows($result) > 0) {
        $user = pg_fetch_assoc($result);
        $hashed_password = $user['password'];

        //if (password_verify($password, $hashed_password)) {
          if($password==$hashed_password){
            // Successful login
            $_SESSION['user_id'] = $user['id'];
            header("Location: accueil.php");
            exit();
        } else {
            echo "Invalid password.";
        }
    } else {
        echo "No user found with that username.";
    }
    if ($login_failed) {
    header("Location: index.php?error=Invalid credentials");
    exit();
    }

    pg_free_result($result);
    pg_close($conn);
}
?>
