<?php 
include "config/db.php"; // Database connection

try {
    $stmt = $pdo->prepare("
SELECT product_id, name, quantity 
FROM products 
ORDER BY name ASC
    ");
$stmt->execute();
$category = $stmt->fetchAll(PDO::FETCH_ASSOC);

$categoryOptions = "";
foreach ($category as $categories) {
    $categoryOptions .= "
        <option 
            value='{$categories['product_id']}'
            data-qty='{$categories['quantity']}'
        >
            " . htmlspecialchars($categories['name']) . "
        </option>";
}

    $categoryKey = 1; // Example dynamic key (you may set this dynamically)
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>