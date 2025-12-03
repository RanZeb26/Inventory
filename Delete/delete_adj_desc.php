<?php
session_start();
include "../config/db.php";
header('Content-Type: application/json');

$id = $_POST['reference_id'] ?? null;

if (!$id) {
    echo json_encode(['status' => 'error', 'message' => 'Missing product ID']);
    exit();
}

// 1. Fetch product info first
$stmt = $pdo->prepare("SELECT product_name FROM products_adjustment WHERE reference_id = ?");
$stmt->execute([$id]);
$productName = $stmt->fetchColumn();

if (!$productName) {
    echo json_encode(['status' => 'error', 'message' => 'Product not found']);
    exit();
}

// 2. Log deletion
$deleted_by = $_SESSION['username'] ?? 'admin'; 
$log = $pdo->prepare("INSERT INTO deletion_logs (product_id, product_name, deleted_by) VALUES (?, ?, ?)");
$log->execute([$id, $productName, $deleted_by]);

// 3. Delete product
$stmt = $pdo->prepare("DELETE FROM products_adjustment WHERE reference_id = ?");
if ($stmt->execute([$id])) {
    echo json_encode(['status' => 'success', 'message' => 'Product deleted']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to delete product']);
}
