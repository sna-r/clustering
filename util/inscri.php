<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require '../conf/config.php';
// require_once '../routes/routes.php';

if (isset($_POST['add'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Prepare the SQL statement
    $result = pg_prepare($conn, "add_query", "INSERT INTO users VALUES (default,$1,$2)");
    $result = pg_execute($conn, "add_query", array($username ,$password));

    if ($result === false) {
        echo "Failed to execute the SQL statement.";
        exit();
    }

    // Check if the insertion was successful
    if (pg_affected_rows($result) > 0) {
        echo "User registered successfully!";
    } else {
        echo "Failed to register the user.";
    }

    
    pg_close($conn);
}
?>
