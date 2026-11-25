<?php
require_once 'config.php';
header('Content-Type: application/json');

$order_id = $_POST['order_id'] ?? 0;
$status = $_POST['status'] ?? '';

$valid_statuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];

if ($order_id <= 0 || !in_array($status, $valid_statuses)) {
    echo json_encode(['success' => false, 'message' => 'Invalid order data']);
    exit();
}

$stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
$result = $stmt->execute([$status, $order_id]);

if ($result) {
    echo json_encode(['success' => true, 'message' => 'Order status updated']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update order status']);
}
?>