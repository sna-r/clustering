<?php
// $host = "localhost";
// $dbname = "cluster";
// $user = "cluster";
// $password = "cluster";


// // Create connection
// $conn = pg_connect("host=$host dbname=$dbname user=$user password=$password");

// // Check connection
// if (!$conn) {
//     die("Connection failed: " . pg_last_error());
// }
$host = "localhost";
$dbname = "cluster_master";
$user = "root";
$password = "Admin1234";
// Create connection using mysqli
$conn = new mysqli($host, $user, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "Connected successfully to MySQL database!";
?>
