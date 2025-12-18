<?php 
include "config/db.php"; // Database connection

try {
    $stmt = $pdo->prepare("
        SELECT id,name
        FROM payments_option 
        ORDER BY name ASC
    ");
    $stmt->execute();
    $payment = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Generate payment options dynamically
    $paymentOptions = "";
    foreach ($payment as $payments) {
        $paymentOptions .= "<option value='{$payments['id']}'>" . htmlspecialchars($payments['name']) . "</option>";
    }

    $categoryKey = 1; // Example dynamic key (you may set this dynamically)
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}