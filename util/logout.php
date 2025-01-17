<?php
session_start();
session_unset();
session_destroy();
// require_once '../routes/routes.php';

// route("/login");
header('Location: /clustering/public/');
exit();
?>
