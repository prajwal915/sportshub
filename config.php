<?php
// Database configuration
$host = 'localhost';
$dbname = 'sportshub';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function redirect($url) {
    header("Location: $url");
    exit();
}

function getCartCount() {
    global $pdo;
    if (!isset($_SESSION['user_id'])) return 0;
    $stmt = $pdo->prepare("SELECT SUM(quantity) FROM cart WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return (int)($stmt->fetchColumn() ?? 0);
}
// Add this function to your existing config.php

function getProductImage($productName, $category) {
    // Encode the product name for URL
    $searchTerm = urlencode($productName . ' ' . $category . ' sport');
    
    // Use Unsplash Source API (free, no API key needed)
    return "https://source.unsplash.com/400x400/?" . $searchTerm;
}

// Alternative: Use Lorem Picsum (simple placeholders)
function getPlaceholderImage($width = 400, $height = 400) {
    return "https://picsum.photos/id/" . rand(1, 200) . "/$width/$height";
}
?>