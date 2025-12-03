<?php
header('Content-Type: application/json');

$conn = new mysqli("localhost", "root", "", "inventory");
if ($conn->connect_error) {
    echo json_encode(['status' => 'error', 'message' => 'Connection failed']);
    exit();
}

$id = $_POST['id'];

// 1. Fetch product info first
$stmt = $conn->prepare("SELECT name FROM products WHERE product_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($productName);
$stmt->fetch();
$stmt->close();

if (!$productName) {
    echo json_encode(['status' => 'error', 'message' => 'Product not found']);
    exit();
}

// 2. Log deletion
$deleted_by = 'admin'; // Replace with session username if available
$log = $conn->prepare("INSERT INTO deletion_logs (product_id, product_name, deleted_by) VALUES (?, ?, ?)");
$log->bind_param("iss", $id, $productName, $deleted_by);
$log->execute();
$log->close();

// 3. Delete product
$stmt = $conn->prepare("DELETE FROM products WHERE product_id = ?");
$stmt->bind_param("i", $id);
if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Product deleted']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to delete product']);
}

$stmt->close();
$conn->close();
