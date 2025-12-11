<?php
include "../config/db.php";
header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $reason = trim($_POST['reason']);
    $previous_qty = trim($_POST['previous_qty']);
    $adjustment_qty = trim($_POST['adjustment_qty']);
    $adj_id = trim($_POST['adj_id']);
    $user_id = $_SESSION['id'] ?? null;

    // Fetch product name
    $stmt = $pdo->prepare("SELECT i.product_id, i.adjustment_qty, a.quantity FROM products a INNER JOIN products_adjustments i ON a.product_id = i.product_id WHERE i.adj_id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    $productID = $row['product_id'];
    $productqty = $row['adjustment_qty'];
    $current_qty = $row['quantity'];

    // Update main item table
    $update = $pdo->prepare("UPDATE products SET quantity = ? WHERE product_id = ?");
    $newQty = $previous_qty + $adjustment_qty;
    $update->execute([$newQty, $productID]);

    // Update all product details
    $stmt = $pdo->prepare("UPDATE products_adjustments SET reason=?, adjustment_qty=?, previous_qty=? WHERE adj_id=?");
    $newQty = $previous_qty + $adjustment_qty;
    $success = $stmt->execute([
        $reason,
        $newQty,
        $previous_qty,
        $adj_id

    ]);
    if ($success) {
        $error = "Item updated successfully!";
        header("Location: QuantityAdjustment?success=" . urlencode($error));
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to update product."]);
    }
}
