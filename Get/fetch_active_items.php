<?php 
include "config/db.php";

try {
    $stmt = $pdo->prepare("
        SELECT 
            c.cat_name,
            COUNT(*) as total,
            SUM(CASE WHEN p.status = 'active' THEN 1 ELSE 0 END) as active
        FROM products p
        INNER JOIN category_item c ON p.category = c.id
        GROUP BY p.category
    ");
    
    $stmt->execute();
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>