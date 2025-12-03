<?php
require 'config/db.php';  

header("Content-Type: application/json");

try {
    $stmt = $pdo->query("SELECT product_id, sku, name, category, brand, unit, quantity, selling_price FROM products ORDER BY product_id ASC");
    $inventory = $stmt->fetchAll(PDO::FETCH_ASSOC);
   
    // Sanitize and format inventory data to prevent XSS
    $formattedInventory = array_map(function($row) {
        $price = floatval($row["selling_price"]);
        $qty = floatval($row["quantity"]);
        return [
            "id" => htmlspecialchars($row["product_id"], ENT_QUOTES, 'UTF-8'),
            "name" => htmlspecialchars($row["name"], ENT_QUOTES, 'UTF-8'),
            "brand" => htmlspecialchars($row["brand"], ENT_QUOTES, 'UTF-8'),
            "quantity" => htmlspecialchars(trim($row["quantity"] . ' ' . ($row["unit"] ?? '')), ENT_QUOTES, 'UTF-8'),
            "items_cost" => "₱ " . number_format(floatval($row["selling_price"]), 2),
            "total_cost" => "₱ " . number_format($price * $qty, 2),  
            "category" => htmlspecialchars($row["category"], ENT_QUOTES, 'UTF-8')
        ];
    }, $inventory);

    echo json_encode($formattedInventory, JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    echo json_encode([]);
}
?>