<?php
// Frontend Configuration for Payroll Management System

// API Configuration
define('API_BASE_URL', 'http://localhost:8000');
define('API_VERSION', 'api');

// Application Settings
define('APP_NAME', 'Payroll Management System');
define('APP_VERSION', '1.0.0');
define('COMPANY_NAME', 'TechCorp');

// Theme Settings
define('DEFAULT_THEME', 'light'); // light or dark
define('ALLOW_THEME_SWITCH', true);

// Session Configuration
session_start();

// Helper Functions
function getApiUrl($endpoint) {
    return API_BASE_URL . '/' . $endpoint;
}

function getCurrentUser() {
    return $_SESSION['user'] ?? null;
}

function getAuthToken() {
    // Check PHP session first
    if (isset($_SESSION['auth_token']) && !empty($_SESSION['auth_token'])) {
        return $_SESSION['auth_token'];
    }

    // Fallback to localStorage via JavaScript
    if (isset($_COOKIE['auth_token']) && !empty($_COOKIE['auth_token'])) {
        return $_COOKIE['auth_token'];
    }

    return null;
}

function setAuthToken($token) {
    $_SESSION['auth_token'] = $token;
}

function isLoggedIn() {
    // Check if session is active and token exists
    return isset($_SESSION) && isset($_SESSION['auth_token']) && !empty($_SESSION['auth_token']);
}

function logout() {
    // Clear session completely
    session_start(); // Make sure session is started
    $_SESSION = []; // Clear all session variables
    session_destroy();

    // Clear auth cookie
    setcookie('auth_token', '', time() - 3600, '/');

    // Don't redirect here - let the logout.php handle the redirect
}

function requireAuth() {
    if (!isLoggedIn()) {
        header('Location: ../auth/login.php');
        exit;
    }
}

function formatCurrency($amount) {
    return '$' . number_format($amount, 2);
}

function formatDate($date) {
    return date('M d, Y', strtotime($date));
}

function formatDateTime($datetime) {
    return date('M d, Y H:i', strtotime($datetime));
}

// Error Handling
function showError($message) {
    $_SESSION['error'] = $message;
}

function showSuccess($message) {
    $_SESSION['success'] = $message;
}

function getError() {
    $error = $_SESSION['error'] ?? null;
    unset($_SESSION['error']);
    return $error;
}

function getSuccess() {
    $success = $_SESSION['success'] ?? null;
    unset($_SESSION['success']);
    return $success;
}

// API Request Helper
function apiRequest($endpoint, $method = 'GET', $data = null) {
    $url = getApiUrl($endpoint);
    $headers = ['Content-Type: application/json'];

    if (isLoggedIn()) {
        $headers[] = 'Authorization: Bearer ' . getAuthToken();
    }

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    if ($method !== 'GET') {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
    }

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return [
        'success' => $httpCode >= 200 && $httpCode < 300,
        'data' => json_decode($response, true),
        'http_code' => $httpCode
    ];
}
?>