<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

/**
 * Check if user is logged in
 */
function checkAuth() {
    if (!isset($_SESSION['user'])) {
        header("Location: /ServeSmart/login.php");
        exit();
    }
}

/**
 * Restrict access to Distributor only
 */
function isDistributor() {
    checkAuth();
    if ($_SESSION['user']['role'] != 'distributor') {
        echo "<h3 class='text-danger text-center mt-5'>Access Denied: Distributor Only</h3>";
        exit();
    }
}

/**
 * Restrict access to NGO only
 */
function isNGO() {
    checkAuth();
    if ($_SESSION['user']['role'] != 'ngo') {
        echo "<h3 class='text-danger text-center mt-5'>Access Denied: NGO Only</h3>";
        exit();
    }
}

/**
 * Get logged-in user data safely
 */
function currentUser() {
    return $_SESSION['user'] ?? null;
}
?>