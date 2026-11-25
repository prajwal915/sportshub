<?php
require_once 'config.php';
header('Content-Type: application/json');

$name = $_POST['name'] ?? '';
$category = $_POST['category'] ?? '';
$price = $_POST['price'] ?? 0;
$stock = $_POST['stock'] ?? 0;
$image = $_POST['image'] ?? 'default.jpg';
$brand = $_POST['brand'] ?? '';

if (empty($name) || empty($category) || $price <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid product data']);
    exit();
}

$stmt = $pdo->prepare("INSERT INTO products (name, category, price, stock, image, brand) VALUES (?, ?, ?, ?, ?, ?)");
$result = $stmt->execute([$name, $category, $price, $stock, $image, $brand]);

if ($result) {
    echo json_encode(['success' => true, 'message' => 'Product added successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to add product']);
}
?>