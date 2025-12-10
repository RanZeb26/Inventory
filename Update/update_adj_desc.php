<?php
include "../config/db.php";
header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_id = trim($_POST['product_id']);
    $name = trim($_POST['name']);
    $reason = trim($_POST['reason']);
    $previous_qty = trim($_POST['previous_qty']);
    $adjustment_qty = trim($_POST['adjustment_qty']);
    $id = trim($_POST['adj_id']);
    $user_id = $_SESSION['id'] ?? null;

    // Update main item table
    $update = $pdo->prepare("UPDATE products SET quantity = ? WHERE product_id = ?");
    $newQty = $adjustment_qty - $productqty;
    $update->execute([$newQty, $productID]);
    
    // Update all product details
    $stmt = $pdo->prepare("UPDATE products_adjustments SET reason=?, adjustment_qty=?, previous_qty=? WHERE adj_id=?");
    $newQty = $adjustment_qty - $previous_qty;
    $success = $stmt->execute([
        $reason,
        $newQty,
        $previous_qty,
        $id

    ]);
    if ($success) {
        $error = "Item updated successfully!";
        header("Location: QuantityAdjustment?success=" . urlencode($error));
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to update product."]);
    }
}
