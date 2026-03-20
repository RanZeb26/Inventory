<?php 
include "config/db.php"; // Database connection

try {
    $stmt = $pdo->prepare("
        SELECT id, unit_name 
        FROM unit_item 
        ORDER BY unit_name ASC
    ");
    $stmt->execute();
    $unit = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Generate unit options dynamically
    $unitOptions = "";
    foreach ($unit as $units) {
        $unitOptions .= "<option value='{$units['id']}'>" . htmlspecialchars($units['unit_name']) . "</option>";
    }

    $categoryKey = 1; // Example dynamic key (you may set this dynamically)
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>