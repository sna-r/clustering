<?php
// public/index.php
// session_start();
// Include the router
// require_once '../routes/routes.php';
require_once '../routes/Router.php';

// Get the requested URI
// $requestUri = $_SERVER['REQUEST_URI'];
// $uri = parse_url($requestUri, PHP_URL_PATH);
// echo $uri;
// Route the request
//route($requestUri);
$router = new Router();
$router->handleRequest();
//route("/");
?>