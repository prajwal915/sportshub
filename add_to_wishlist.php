<?php
session_start();
require_once 'config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login']);
    exit();
}

$user_id = $_SESSION['user_id'];
$product_id = $_POST['product_id'] ?? 0;

// Check if already in wishlist
$stmt = $pdo->prepare("SELECT * FROM wishlist WHERE user_id = ? AND product_id = ?");
$stmt->execute([$user_id, $product_id]);
$existing = $stmt->fetch();

if ($existing) {
    echo json_encode(['success' => false, 'message' => 'Already in wishlist']);
    exit();
}

$stmt = $pdo->prepare("INSERT INTO wishlist (user_id, product_id) VALUES (?, ?)");
$result = $stmt->execute([$user_id, $product_id]);

if ($result) {
    echo json_encode(['success' => true, 'message' => 'Added to wishlist']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to add to wishlist']);
}
?>