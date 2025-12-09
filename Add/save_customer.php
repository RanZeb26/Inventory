<?php
session_start();
include "../config/db.php";
header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $customer_name = trim($_POST['customer_name']);
    $company_name = trim($_POST['company_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $user_id = $_SESSION['id'] ?? null;

    if (empty($customer_name) || empty($company_name) || empty($email) || empty($phone) || empty($address)) {
        $error = "All fields are required.";
        header("Location: Customer?error=" . urlencode($error));
        exit();
    }

    try {

        // CHECK if this product already has an adjustment today
        $checkStmt = $pdo->prepare("
            SELECT COUNT(*) AS total
            FROM customers
            WHERE customer_name = :customer_name
        ");

        $checkStmt->execute([":customer_name" => $customer_name]);
        $count = $checkStmt->fetchColumn();

        if ($count > 0) {
            $error = "This Customer already exists";
            header("Location: Customer?error=" . urlencode($error));
            exit();
        }

        // INSERT adjustment
        $stmt = $pdo->prepare("
            INSERT INTO customers 
                (customer_name, company_name, email, phone, address, added_by)
            VALUES 
                (:customer_name, :company_name, :email, :phone, :address, :user_id)
        ");
        $stmt->execute([
            ":customer_name" => $customer_name,
            ":company_name" => $company_name,
            ":email" => $email,
            ":phone" => $phone,
            ":address" => $address,
            ":user_id" => $user_id,
        ]);

        $success = "Customer recorded successfully!";
        header("Location: Customer?success=" . urlencode($success));
        exit();
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        echo json_encode([
            "status" => "error",
            "message" => "❌ Database error: " . $e->getMessage()
        ]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "❌ Invalid request."]);
}
