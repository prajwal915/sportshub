<?php
session_start();
require_once 'config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT w.*, p.name, p.price, p.image FROM wishlist w JOIN products p ON w.product_id = p.id WHERE w.user_id = ?");
$stmt->execute([$user_id]);
$wishlist = $stmt->fetchAll();

echo json_encode($wishlist);
?>