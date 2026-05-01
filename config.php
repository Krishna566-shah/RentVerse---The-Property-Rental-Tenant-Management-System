<?php
session_start();

$host = "localhost";
$user = "root";
$pass = "";
$dbname = "rentverse";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ensure upload directories exist
$dirs = ['uploads/properties', 'uploads/payments'];
foreach ($dirs as $dir) {
    if (!file_exists($dir)) {
        mkdir($dir, 0777, true);
    }
}
?>