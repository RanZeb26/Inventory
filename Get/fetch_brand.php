<?php 
include "config/db.php"; // Database connection

try {
    $stmt = $pdo->prepare("
        SELECT id, cat_name 
        FROM category_item 
        ORDER BY cat_name ASC
    ");
    $stmt->execute();
    $category = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Generate category options dynamically
    $categoryOptions = "";
    foreach ($category as $categories) {
        $categoryOptions .= "<option value='{$categories['id']}'>" . htmlspecialchars($categories['cat_name']) . "</option>";
    }

    $categoryKey = 1; // Example dynamic key (you may set this dynamically)
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>