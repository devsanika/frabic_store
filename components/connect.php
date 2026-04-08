<?php

// Database credentials
$host     = 'localhost';
$db_name  = 'fabric_store';
$username = 'root';
$password = '';

// PDO Connection
try {
    $conn = new PDO(
        "mysql:host=$host;dbname=$db_name;charset=utf8",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    die('<div style="font-family:monospace;background:#fee;color:#c00;padding:20px;border-radius:8px;margin:20px;">
        <strong>Database Connection Failed</strong><br>
        ' . htmlspecialchars($e->getMessage()) . '<br><br>
        Please ensure MySQL is running and the database <code>fabric_store</code> exists.<br>
        Import <code>fabric_store.sql</code> to set up the database.
    </div>');
}

// Generate unique ID
function unique_id() {
    return uniqid(rand(10, 99));
}

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Initialize message arrays
$success_msg = [];
$warning_msg  = [];

// Pick up session flash messages
if (isset($_SESSION['success_msg'])) {
    $success_msg[] = $_SESSION['success_msg'];
    unset($_SESSION['success_msg']);
}
if (isset($_SESSION['warning_msg'])) {
    $warning_msg[] = $_SESSION['warning_msg'];
    unset($_SESSION['warning_msg']);
}