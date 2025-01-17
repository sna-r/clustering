<?php

class Router
{
    public function handleRequest()
    {
        // Check if the user is logged in
        if ($this->isLoggedIn()) {
            // If logged in, route to home or default route
            $this->home();
        } else {
            // If not logged in, route to login page
            $this->login();
        }
    }

    // Check if the user is logged in (e.g., check session variable)
    private function isLoggedIn()
    {
        return isset($_SESSION['user_id']);
    }

    // Handle home route
    private function home()
    {
        $this->renderView('accueil.php');
    }

    // Handle login route
    private function login()
    {
        // Render login page
        $this->renderView('login.php');
    }

    // Render the view
    private function renderView($view)
    {
        include "../views/$view";
    }
}
