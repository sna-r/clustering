<?php
$host = "127.0.0.1"; // Use 127.0.0.1 to force TCP connection
$dbname = "cluster_master";
$user = "cluster_master";
$password = "cluster";

// Enable detailed error reporting
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli($host, $user, $password, $dbname);
    echo "Database connection successful!";
} catch (mysqli_sql_exception $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
