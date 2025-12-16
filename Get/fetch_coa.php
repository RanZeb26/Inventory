<?php 
include "config/db.php"; // Database connection

try {
    $stmt = $pdo->prepare("
        SELECT id,account_code, account_name 
        FROM chart_of_accounts 
        ORDER BY account_name ASC
    ");
    $stmt->execute();
    $category = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Generate category options dynamically
    $categoryOptions = "";
    foreach ($category as $categories) {
        $categoryOptions .= "<option value='{$categories['id']}'>" . htmlspecialchars($categories['account_code']) . " - " . htmlspecialchars($categories['account_name']) . "</option>";
    }

    $categoryKey = 1; // Example dynamic key (you may set this dynamically)
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>