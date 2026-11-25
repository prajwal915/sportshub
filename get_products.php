<?php
require_once 'config.php';
header('Content-Type: application/json');

$category = $_GET['category'] ?? '';
if ($category && $category !== 'all') {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE category = ? ORDER BY id DESC");
    $stmt->execute([$category]);
} else {
    $stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");
}
$products = $stmt->fetchAll();
echo json_encode($products);
?>