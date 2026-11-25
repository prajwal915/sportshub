<?php
require_once 'config.php';
header('Content-Type: application/json');

$totalProducts = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$totalOrders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalRevenue = $pdo->query("SELECT SUM(total_amount) FROM orders")->fetchColumn();

echo json_encode([
    'totalProducts' => $totalProducts,
    'totalOrders' => $totalOrders,
    'totalUsers' => $totalUsers,
    'totalRevenue' => $totalRevenue ?? 0
]);
?>