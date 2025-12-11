<?php
session_start();
include "../config/db.php";
header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $reason          = trim($_POST['reason']);
    $previous_qty    = (int) trim($_POST['previous_qty']);
    $adjustment_qty  = (int) trim($_POST['adjustment_qty']);
    $adj_id          = trim($_POST['adj_id']);
    $productID       = trim($_POST['productID']);
    $user_id         = $_SESSION['id'] ?? null;

    // Get current quantity
    $stmt = $pdo->prepare("SELECT quantity FROM products WHERE product_id = ?");
    $stmt->execute([$productID]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        echo json_encode(["status" => "error", "message" => "Product not found."]);
        exit;
    }

    $current_qty = (int)$row['quantity'];

    // Compute new quantity
    $newQty = $previous_qty + $adjustment_qty;

    // Update products inventory
    $update = $pdo->prepare("UPDATE products SET quantity = ? WHERE product_id = ?");
    $update->execute([$newQty, $productID]);

    // Update adjustment record
    $stmt = $pdo->prepare("
        UPDATE products_adjustments
        SET reason = ?, adjustment_qty = ?, previous_qty = ?
        WHERE adj_id = ?
    ");

    $success = $stmt->execute([
        $reason,
        $adjustment_qty,   // correct value
        $previous_qty,     // correct previous qty
        $adj_id
    ]);

    if ($success) {
        header("Location: QuantityAdjustment?success=" . urlencode("Item updated successfully!"));
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to update product."]);
    }
}
?>
