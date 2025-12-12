<?php
include "config/db.php";

$sql = "SELECT product_id, name, selling_price FROM products ORDER BY name ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute();

$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($items);
?>
