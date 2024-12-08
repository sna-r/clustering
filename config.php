<?php
$host = "localhost";
$dbname = "cluster";
$user = "cluster";
$password = "cluster";

// Create connection
$conn = pg_connect("host=$host dbname=$dbname user=$user password=$password");

// Check connection
if (!$conn) {
    die("Connection failed: " . pg_last_error());
}
?>
