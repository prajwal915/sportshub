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

$stmt = $pdo->prepare("DELETE FROM wishlist WHERE user_id = ? AND product_id = ?");
$result = $stmt->execute([$user_id, $product_id]);

if ($result) {
    echo json_encode(['success' => true, 'message' => 'Removed from wishlist']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to remove']);
}
?>