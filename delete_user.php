<?php
require_once 'config.php';
header('Content-Type: application/json');

$user_id = $_POST['user_id'] ?? 0;

if ($user_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid user ID']);
    exit();
}

// Check if user is admin (don't delete admin)
$stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if ($user && $user['role'] === 'admin') {
    echo json_encode(['success' => false, 'message' => 'Cannot delete admin user']);
    exit();
}

// Delete user's cart first
$stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
$stmt->execute([$user_id]);

// Delete user's orders
$stmt = $pdo->prepare("DELETE FROM orders WHERE user_id = ?");
$stmt->execute([$user_id]);

// Delete user
$stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
$result = $stmt->execute([$user_id]);

if ($result) {
    echo json_encode(['success' => true, 'message' => 'User deleted successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to delete user']);
}
?>