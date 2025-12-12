<?php 
include "config/db.php"; // Database connection

try {
    $stmt = $pdo->prepare("
        SELECT customer_id, customer_name, company_name 
        FROM customers 
        ORDER BY customer_name ASC
    ");
    $stmt->execute();
    $category = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Generate category options dynamically
$categoryOptions = "";
foreach ($category as $categories) {
    $categoryOptions .= "
        <option 
            value='{$categories['customer_id']}'
            data-customername='{$categories['customer_name']}'
            data-companyname='{$categories['company_name']}'
        >
            " . htmlspecialchars($categories['customer_name']) . "
        </option>";
}

    $categoryKey = 1; // Example dynamic key (you may set this dynamically)
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
  
}
?>