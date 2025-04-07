<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Backend API URL (choose either Node.js or Python)
    $apiUrl = 'http://localhost:3000/add-user';

    // Prepare the data to send
    $data = json_encode(['username' => $username, 'password' => $password]);

    // Initialize cURL
    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Content-Length: ' . strlen($data)
    ]);

    // Execute the request
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // Decode the response
    if ($httpCode === 200) {
        $responseData = json_decode($response, true);
        echo "<p>" . htmlspecialchars($responseData['message']) . "</p>";
    } else {
        echo '<p>Error: Unable to add user.</p>';
    }
}

?>
