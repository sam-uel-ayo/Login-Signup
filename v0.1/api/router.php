<?php

require '../assets/header.php';
use CUtils\CUtils;
use MAuth\MAuth;

// Define protected routes and the login page
$route = $_GET['route'] ?? null;
$protectedRoutes = [
    'auth/edit',
    'cart/add-item'
    // Add other protected routes here
];

// Check if the route is protected and requires authentication
if (in_array($route, $protectedRoutes)) {
    if (!isset($_COOKIE['user_id']) || !isset($_COOKIE['access_token'])) {
        CUtils::outputData(false, "Unauthorized request", null, true, 401);
    }

    $userId = $_COOKIE['user_id'];
    $userToken = $_COOKIE['access_token'];

    // Validate the token and user ID against the database
    $user = MAuth::getAuthDetails($user_id, $access_token);
    if (!$user) {
        // Token is invalid
        CUtils::outputData(false, "Unauthorized request", null, true, 401);
    }

    // Validate IP and User-Agent 
    if ($user->ip_address !== $_SERVER['REMOTE_ADDR'] || $user->user_agent !== $_SERVER['HTTP_USER_AGENT']) {
        // Log mismatch for further investigation
        $logFile = '../logs/router.errors.log';
        $logMessage = sprintf(
            "[%s] Token mismatch for user ID: %s, IP: %s, User-Agent: %s\n",
            date('Y-m-d H:i:s'),
            $userId,
            $_SERVER['REMOTE_ADDR'],
            $_SERVER['HTTP_USER_AGENT']
        );
        file_put_contents($logFile, $logMessage, FILE_APPEND);
        CUtils::outputData(false, "Unauthorized request", null, true, 401);
    }
}

// Forward request to the appropriate endpoint
$endpoint = $route . '.php';
if (file_exists($endpoint)) {
    include $endpoint;
} else {
    CUtils::outputData(false, "Page not found", null, true, 404);
    exit;
}
