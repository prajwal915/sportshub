<?php
require_once 'config.php';
header('Content-Type: application/json');

$product_id = $_POST['product_id'] ?? 0;

if ($product_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid product ID']);
    exit();
}

// First delete from cart
$stmt = $pdo->prepare("DELETE FROM cart WHERE product_id = ?");
$stmt->execute([$product_id]);

// Then delete product
$stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
$result = $stmt->execute([$product_id]);

if ($result) {
    echo json_encode(['success' => true, 'message' => 'Product deleted successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to delete product']);
}
?>