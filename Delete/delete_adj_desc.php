<?php
session_start();
include "../config/db.php";
header('Content-Type: application/json');

$id = $_POST['adj_id'] ?? null;

if (!$id) {
    echo json_encode(['status' => 'error', 'message' => 'Missing product ID']);
    exit();
}

// Fetch product name
$stmt = $pdo->prepare("SELECT i.product_id, i.adjustment_qty, a.quantity FROM products a INNER JOIN products_adjustments i ON a.product_id = i.product_id WHERE i.adj_id = ?");
$stmt->execute([$id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

$productID = $row['product_id'];
$productqty = $row['adjustment_qty'];
$current_qty = $row['quantity'];


if (!$productqty) {
    echo json_encode(['status' => 'error', 'message' => 'Product not found']);
    exit();
}

$deleted_by = $_SESSION['username'] ?? 'admin';

// Log deletion
$log = $pdo->prepare("
    INSERT INTO deletion_logs (product_id, product_qty, deleted_by) 
    VALUES (?, ?, ?)
");
$log->execute([$productID, $productqty, $deleted_by]);

// Update main item table
$update = $pdo->prepare("UPDATE products SET quantity = ? WHERE product_id = ?");
$newQty = $current_qty - $productqty;
$update->execute([$newQty, $productID]);

// Delete record
$stmt = $pdo->prepare("DELETE FROM products_adjustments WHERE adj_id = ?");
$stmt->execute([$id]);

echo json_encode(['status' => 'success', 'message' => 'Product deleted']);
