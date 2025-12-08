<?php
session_start();
include "../config/db.php";
header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $product_id = trim($_POST['product_id']);
    $name = trim($_POST['name']);
    $reason = trim($_POST['reason']);
    $previous_qty = trim($_POST['previous_qty']);
    $adjustment_qty = trim($_POST['adjustment_qty']);
    $user_id = $_SESSION['id'] ?? null;

    if (empty($product_id) || empty($reason) || empty($adjustment_qty)) {
        $error = "All fields are required.";
        header("Location: QuantityAdjustment?error=" . urlencode($error));
        exit();
    }

    try {

        // CHECK if this product already has an adjustment today
        $checkStmt = $pdo->prepare("
            SELECT COUNT(*) AS total
            FROM products_adjustments
            WHERE product_id = :product_id
            AND DATE(created_at) = CURDATE()
        ");

        $checkStmt->execute([":product_id" => $product_id]);
        $count = $checkStmt->fetchColumn();

        if ($count > 0) {
            $error = "This item already has an adjustment today.";
            header("Location: QuantityAdjustment?error=" . urlencode($error));
            exit();
        }

        // INSERT adjustment
        $stmt = $pdo->prepare("
            INSERT INTO products_adjustments 
                (product_id, reason, adjustment_qty, previous_qty, created_at)
            VALUES 
                (:product_id, :reason, :adjustment_qty, :previous_qty, :created_at)
        ");
        // Update main item table
        $update = $pdo->prepare("UPDATE products SET quantity = ? WHERE product_id = ?");
        $newQty = $previous_qty + $adjustment_qty;
        $update->execute([$newQty, $product_id]);

        $stmt->execute([
            ":product_id" => $product_id,
            ":reason" => $reason,
            ":adjustment_qty" => $adjustment_qty,
            ":previous_qty" => $previous_qty,
            ":created_at" => date('Y-m-d H:i:s')
        ]);

        $success = "Quantity adjustment recorded successfully!";
        header("Location: QuantityAdjustment?success=" . urlencode($success));
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
