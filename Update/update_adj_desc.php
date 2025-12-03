<?php
include "../config/db.php";
header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $reference_id = trim($_POST['reference_id']);
    $name = trim($_POST['name']);
    $reason = trim($_POST['reason']);
    $status = trim($_POST['status']);
    $user_id = $_SESSION['id'] ?? null; // ✅ get logged-in user id

    // Update all product details
    $stmt = $pdo->prepare("UPDATE products_adjustment SET product_name=?, reasons=?, status=?, updated_at=? WHERE reference_id=?");

    $success = $stmt->execute([
        $name, $reason, $status, date("Y-m-d H:i:s"), $reference_id
    ]);

    if ($success) {
        $error = "Item updated successfully!";
        header("Location: QuantityAdjustment?success=" . urlencode($error));
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to update product."]);
    }
}
?>
