<?php
include "../config/db.php";
header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $customer_name = trim($_POST['customer_name']);
    $company_name = trim($_POST['company_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $customer_id = trim($_POST['customer_id']);
    $user_id = $_SESSION['id'] ?? null;
    
    // Update all product details
    $stmt = $pdo->prepare("UPDATE customers SET customer_name=?, company_name=?, email=?, phone=?, address=? WHERE customer_id=?");
    $success = $stmt->execute([
        $customer_name,
        $company_name,
        $email,
        $phone,
        $address,
        $customer_id

    ]);
    if ($success) {
        $error = "Item updated successfully!";
        header("Location: Customer?success=" . urlencode($error));
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to update product."]);
    }
}
