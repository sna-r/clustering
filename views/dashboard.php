<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Server Dashboard</title>
    <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.min.css" />
    <link rel="stylesheet" href="../assets/css/adminlte.css" />
    <link rel="stylesheet" href="../assets/css/adminlte.css.map" />
    <link rel="stylesheet" href="../assets/css/adminlte.min.css" />
</head>
<body>
    <div class="container mt-5">
        <?php
        // Function to fetch JSON data from the API
        function fetch_servers($url) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            curl_close($ch);
            return json_decode($response, true);
        }

        // Fetch all server data from the API
        $api_url = "http://192.168.0.108:3000/servers"; // Replace with your API URL
        $servers_data = fetch_servers($api_url);

        // Loop through each server type and display a table
        foreach ($servers_data as $server_type => $servers) {
            echo '<div class="card mb-4">';
            echo '<div class="card-header">';
            echo '<h3 class="card-title">' . ucfirst($server_type) . '</h3>';
            echo '<div class="card-tools">';
            echo '<button type="button" class="btn btn-tool" data-lte-toggle="card-collapse">';
            echo '<i data-lte-icon="expand" class="bi bi-plus-lg"></i>';
            echo '<i data-lte-icon="collapse" class="bi bi-dash-lg"></i>';
            echo '</button>';
            echo '<button type="button" class="btn btn-tool" data-lte-toggle="card-remove">';
            echo '<i class="bi bi-x-lg"></i>';
            echo '</button>';
            echo '</div>';
            echo '</div>';
            echo '<div class="card-body p-0">';
            echo '<div class="table-responsive">';
            echo '<table class="table m-0">';
            echo '<thead>';
            echo '<tr>';
            echo '<th>Name</th>';
            echo '<th>IP</th>';
            echo '<th>Port</th>';
            echo '<th>Status</th>';
            echo '<th>Backup</th>';
            echo '<th>Last Checked</th>';
            echo '<th>Action</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';

            foreach ($servers as $server) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($server['name']) . '</td>';
                echo '<td>' . htmlspecialchars($server['ip']) . '</td>';
                echo '<td>' . htmlspecialchars($server['port']) . '</td>';
                echo '<td><span class="badge ' . ($server['status'] === 'online' ? 'text-bg-success' : 'text-bg-danger') . '">' . ucfirst($server['status']) . '</span></td>';
                echo '<td>' . htmlspecialchars($server['backup']) . '</td>';
                echo '<td>' . htmlspecialchars($server['lastChecked']) . '</td>';
                echo '<td>';
                echo '<a href="#" class="btn btn-sm btn-primary">Modify</a>';
                echo '</td>';
                echo '</tr>';
            }

            echo '</tbody>';
            echo '</table>';
            echo '</div>';
            echo '</div>';
            echo '<div class="card-footer clearfix">';
            echo '<a href="#" class="btn btn-sm btn-secondary float-end">Add New ' . ucfirst($server_type) . '</a>';
            echo '</div>';
            echo '</div>';
        }
        ?>
    </div>

    <!-- Include JavaScript for AdminLTE -->
    <script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/adminlte.js"></script>
</body>
</html>