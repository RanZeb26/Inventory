<?php
session_start();
include "../config/db.php";
header('Content-Type: application/json');

$id = $_POST['id'] ?? null;

if (!$id) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Missing invoice ID'
    ]);
    exit();
}

try {
    // Optional: Start transaction for safety
    $pdo->beginTransaction();

    // ✅ Delete items first (child table)
    $stmt = $pdo->prepare("DELETE FROM invoice_items WHERE invoice_id = ?");
    $stmt->execute([$id]);

    // ✅ Then delete header (parent table)
    $stmt = $pdo->prepare("DELETE FROM invoice_header WHERE invoice_id = ?");
    $stmt->execute([$id]);

    $pdo->commit();

    echo json_encode([
        'status' => 'success',
        'message' => 'Invoice deleted successfully'
    ]);

} catch (Exception $e) {
    $pdo->rollBack();

    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}