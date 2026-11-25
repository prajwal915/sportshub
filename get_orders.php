<?php
require_once 'config.php';
header('Content-Type: application/json');

$stmt = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC");
$orders = $stmt->fetchAll();

echo json_encode($orders);
?>