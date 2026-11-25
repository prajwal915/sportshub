<?php
require_once 'config.php';
header('Content-Type: application/json');

$order_id = $_GET['id'] ?? 0;

// Get order details
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch();

if (!$order) {
    echo json_encode(['error' => 'Order not found']);
    exit();
}

// Get order items
$stmt = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
$stmt->execute([$order_id]);
$items = $stmt->fetchAll();

echo json_encode([
    'order' => $order,
    'items' => $items
]);
?>