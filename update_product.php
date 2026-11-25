<?php
require_once 'config.php';
header('Content-Type: application/json');

$product_id = $_POST['product_id'] ?? 0;
$name = $_POST['name'] ?? '';
$category = $_POST['category'] ?? '';
$price = $_POST['price'] ?? 0;
$stock = $_POST['stock'] ?? 0;
$image = $_POST['image'] ?? 'default.jpg';
$brand = $_POST['brand'] ?? '';

if ($product_id <= 0 || empty($name) || empty($category) || $price <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid product data']);
    exit();
}

$stmt = $pdo->prepare("UPDATE products SET name = ?, category = ?, price = ?, stock = ?, image = ?, brand = ? WHERE id = ?");
$result = $stmt->execute([$name, $category, $price, $stock, $image, $brand, $product_id]);

if ($result) {
    echo json_encode(['success' => true, 'message' => 'Product updated successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update product']);
}
?>