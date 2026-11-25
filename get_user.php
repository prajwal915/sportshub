<?php
require_once 'config.php';
header('Content-Type: application/json');

$stmt = $pdo->query("SELECT id, name, email, role, created_at FROM users ORDER BY id DESC");
$users = $stmt->fetchAll();

echo json_encode($users);
?>