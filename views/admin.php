<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update HAProxy</title>
    <script>
        // Function to prompt for password and send it to the backend
        async function reloadHAProxy() {
            // Prompt the user for a password
            const password = prompt("Enter the password to reload HAProxy configuration:");
            if (!password) {
                alert("Password cannot be empty.");
                return;
            }

            try {
                // Send the password via POST request to the backend
                const response = await fetch('http://localhost:3000/update-haproxy', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ password: password })
                });

                // Check the response status
                if (response.ok) {
                    const data = await response.json();
                    alert(data.message); // Show success message
                } else {
                    const errorData = await response.json();
                    alert(`Error: ${errorData.error}`); // Show error message
                }
            } catch (error) {
                console.error("Error:", error);
                alert("An error occurred while communicating with the server.");
            }
        }
    </script>
</head>
<body>
    <button onclick="reloadHAProxy()">Reload HAProxy Configuration</button>
</body>
</html>