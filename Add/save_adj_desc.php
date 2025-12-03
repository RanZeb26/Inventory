<?php
session_start(); // ✅ needed to access logged-in user
include "../config/db.php";
header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $reference_id = trim($_POST['reference_id']);
    $name = trim($_POST['name']);
    $reason = trim($_POST['reason']);
    $status = trim($_POST['status']);
    $user_id = $_SESSION['id'] ?? null; // ✅ get logged-in user id

    if (empty($reference_id) || empty($name) || empty($reason)) {
        $error = "All fields are required.";
        header("Location: QuantityAdjustment?error=" . urlencode($error));
        exit();
    }

    try {
        // Check if item name already exists
        $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM adjustments WHERE product_name = :name");
        $checkStmt->execute([":name" => $name]);
        $count = $checkStmt->fetchColumn();

        if ($count > 0) {
            $error = "Item name already exists.";
            header("Location: QuantityAdjustment?error=" . urlencode($error));
            exit();
        }

        // Insert with created_by
        $stmt = $pdo->prepare("INSERT INTO adjustments 
            (reference_id, product_name, reasons, status, created_by) 
            VALUES (:reference_id, :product_name, :reasons, :status, :created_by)");

        $stmt->execute([
            ":reference_id" => $reference_id,
            ":product_name" => $name,
            ":reasons" => $reason,
            ":status" => $status,
            ":created_by" => $user_id   // ✅ logged-in user
        ]);

        $success = "Item added successfully!";
        header("Location: QuantityAdjustment?success=" . urlencode($success));
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        echo json_encode(["status" => "error", "message" => "❌ Database error: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "❌ Invalid request."]);
}
?>