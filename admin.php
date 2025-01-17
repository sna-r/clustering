<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restart HAProxy Service</title>
    <style>
        .popup {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }
        .popup-content {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
        }
        .popup button {
            margin-top: 10px;
        }
    </style>
    <script>
        function showPopup() {
            document.getElementById("popup").style.display = "flex";
        }

        function closePopup() {
            document.getElementById("popup").style.display = "none";
        }

        function submitPassword() {
            var password = document.getElementById("password").value;
            var formData = new FormData();
            formData.append("password", password);

            var xhr = new XMLHttpRequest();
            xhr.open("POST", "", true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    document.getElementById("popup-message").innerHTML = xhr.responseText;
                }
            };
            xhr.send(formData);
        }
    </script>
</head>
<body>

    <button onclick="showPopup()">Restart HAProxy</button>

    <div id="popup" class="popup">
        <div class="popup-content">
            <h3>Enter Password to Restart HAProxy</h3>
            <input type="password" id="password" placeholder="Enter password">
            <br>
            <button onclick="submitPassword()">Submit</button>
            <button onclick="closePopup()">Cancel</button>
            <div id="popup-message"></div>
        </div>
    </div>

</body>
</html>

<?php
// PHP Code to attempt restarting the service and ask for password if necessary

// Check if the form is submitted with the password
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $password = $_POST['password'];

    // Step 1: Try running the command with sudo (without the user's input first)
    $command = 'sudo systemctl restart haproxy';
    $output = shell_exec($command . ' 2>&1'); // Capture both stdout and stderr

    // Check if the command failed (meaning it might be asking for a password)
    if (strpos($output, 'password') !== false) {
        // Step 2: If the command failed because of a password request, run the command again with the password
        // using the `echo` and `sudo` trick to provide the password
        $command_with_password = 'echo "' . escapeshellarg($password) . '" | sudo -S systemctl restart haproxy';
        $output = shell_exec($command_with_password . ' 2>&1');
        
        // Check if restart was successful
        if (empty($output)) {
            echo "HAProxy has been successfully restarted.";
        } else {
            echo "Error restarting HAProxy: " . htmlspecialchars($output);
        }
    } else {
        // Step 3: If the command didn't ask for a password, show success
        echo "HAProxy has been successfully restarted.";
    }
} else {
    echo "Please enter a password to restart the service.";
}
?>
