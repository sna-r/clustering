<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/Login-Form-Basic-icons.css">
</head>
<body>
    <?php include 'header.php'; ?>
    <div id="content">
        <div id="panel">
            <?php include 'panel.php'; ?>
        </div>
        <div id="main-content">
            <?php include $content; ?>
        </div>
    </div>
</body>
<script src="../assets/bootstrap/js/bootstrap.min.js"></script>
</html>
