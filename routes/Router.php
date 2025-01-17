<?php

class Router
{
    public function handleRequest()
    {
        session_start(); // Start session if not already started

        $route = isset($_GET['route']) ? $_GET['route'] : 'home';

        if ($this->isLoggedIn()) {
            // Handle authenticated routes
            $this->dispatchAuthenticatedRoutes($route);
        } else {
            // If not logged in, always route to login
            $this->login();
        }
    }

    // Check if the user is logged in (e.g., check session variable)
    private function isLoggedIn()
    {
        return isset($_SESSION['user_id']);
    }

    // Dispatch routes for logged-in users
    private function dispatchAuthenticatedRoutes($route)
    {
        switch ($route) {
            case 'dashboard':
                $this->dashboard();
                break;
            case 'home':
            default:
                $this->home();
                break;
        }
    }

    // Handle home route
    private function home()
    {
        $this->renderView('accueil.php', 'Home Page');
    }

    // Handle dashboard route
    private function dashboard()
    {
        $this->renderView('dashboard.php', 'Dashboard');
    }

    // Handle login route
    private function login()
    {
        $this->renderView('login.php', 'Login', false); // Skip template for login
    }

    // Render the view with a default layout
    private function renderView($view, $title = '', $useTemplate = true)
    {
        $content = "../views/$view";

        if ($useTemplate) {
            include "../views/layout.php"; // Load the main layout
        } else {
            include $content; // Load view without template
        }
    }
}
