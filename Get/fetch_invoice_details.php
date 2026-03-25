<?php
include "../config/db.php";
header("Content-Type: application/json");

$id = $_GET['id'];

// HEADER
$stmt = $pdo->prepare("
    SELECT ih.*, c.customer_name, c.company_name
    FROM invoice_header ih
    LEFT JOIN customers c ON c.customer_id = ih.customer_id
    WHERE ih.invoice_id = ?
");
$stmt->execute([$id]);
$header = $stmt->fetch(PDO::FETCH_ASSOC);

// ITEMS
$itemStmt = $pdo->prepare("
    SELECT ii.*, p.name
    FROM invoice_items ii
    LEFT JOIN products p ON p.product_id = ii.product_id
    WHERE ii.invoice_id = ?
");
$itemStmt->execute([$id]);
$items = $itemStmt->fetchAll(PDO::FETCH_ASSOC);

// COMPANY INFO
$company = $pdo->query("SELECT * FROM settings LIMIT 1")
               ->fetch(PDO::FETCH_ASSOC);

// ✅ SINGLE RESPONSE ONLY
echo json_encode([
    "header" => $header,
    "items" => $items,
    "company" => $company
]);