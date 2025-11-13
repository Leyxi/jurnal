<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'pkl_hero_hub');

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to utf8
$conn->set_charset("utf8");

// Start session
session_start();

// Helper functions
function sanitize_input($data) {
    global $conn;
    return $conn->real_escape_string(trim($data));
}

function hash_password($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

function verify_password($password, $hash) {
    return password_verify($password, $hash);
}

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function get_user_role() {
    return $_SESSION['role'] ?? null;
}

function redirect_if_not_logged_in() {
    if (!is_logged_in()) {
        header("Location: login.php");
        exit();
    }
}

function redirect_if_not_role($role) {
    if (get_user_role() !== $role) {
        header("Location: index.php");
        exit();
    }
}

function redirect_based_on_role() {
    if (!is_logged_in()) {
        header("Location: login.php");
        exit();
    }

    $role = get_user_role();
    if ($role === 'siswa') {
        header("Location: siswa_dashboard.php");
    } elseif ($role === 'pembimbing') {
        header("Location: pembimbing_dashboard.php");
    } elseif ($role === 'guru') {
        header("Location: admin_dashboard.php");
    } else {
        header("Location: index.php");
    }
    exit();
}
?>
