<?php 
include "config/db.php"; // Database connection

try {
    $stmt = $pdo->prepare("
        SELECT id,account_code, account_name 
        FROM chart_of_accounts 
        ORDER BY account_name ASC
    ");
    $stmt->execute();
    $account = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Generate account options dynamically
    $accountOptions = "";
    foreach ($account as $accounts) {
        $accountOptions .= "<option value='{$accounts['id']}'>" . htmlspecialchars($accounts['account_code']) . " - " . htmlspecialchars($accounts['account_name']) . "</option>";
    }

    $categoryKey = 1; // Example dynamic key (you may set this dynamically)
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}