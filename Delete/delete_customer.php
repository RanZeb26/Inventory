<?php
session_start();
include "../config/db.php";
header('Content-Type: application/json');

$customerID = $_POST['customer_id'] ?? null;

if (!$customerID) {
    echo json_encode(['status' => 'error', 'message' => 'Missing customer ID']);
    exit();
}

// Fetch customer details
$stmt = $pdo->prepare("SELECT customer_id, status FROM customers WHERE customer_id = ?");
$stmt->execute([$customerID]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

$customerID = $row['customer_id'];
$status = $row['status'];

// Update Customer status to Inactive
$update = $pdo->prepare("UPDATE customers SET status = ? WHERE customer_id = ?");
$status = 'Inactive';
$update->execute([$status, $customerID]);


echo json_encode(['status' => 'success', 'message' => 'Customer deleted']);