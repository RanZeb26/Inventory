<?php
session_start();
include "../config/db.php";
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    try {
        $pdo->beginTransaction();

        // ---------------------------
        // INSERT HEADER
        // ---------------------------
        $stmt = $pdo->prepare("
            INSERT INTO invoice_header (
                invoice_no,
                order_no,
                customer_id,
                invoice_date,
                due_date,
                subtotal,
                tax_amount,
                discount,
                total_amount,
                amount_paid,
                balance,
                status,
                notes
            ) VALUES (
                :invoice_no,
                :order_no,
                :customer_id,
                :invoice_date,
                :due_date,
                :subtotal,
                :tax_amount,
                :discount,
                :total_amount,
                0,
                :balance,
                'Draft',
                :notes
            )
        ");

        $total = $_POST['total_amount'];

        $stmt->execute([
            ':invoice_no'   => $_POST['invoice_number'],
            ':order_no'     => $_POST['order_number'],
            ':customer_id'  => $_POST['customer_id'],
            ':invoice_date' => $_POST['date'],
            ':due_date'     => $_POST['due_date'],
            ':subtotal'     => $_POST['subtotal'],
            ':tax_amount'   => $_POST['tax_amount'],
            ':discount'     => $_POST['discount_total'],
            ':total_amount' => $total,
            ':balance'      => $total,
            ':notes'        => $_POST['notes'],
        ]);

        // GET INVOICE ID
        $invoice_id = $pdo->lastInsertId();

        // ---------------------------
        // INSERT ITEMS
        // ---------------------------
        $items = json_decode($_POST['items'], true);

$itemStmt = $pdo->prepare("
    INSERT INTO invoice_items 
    (invoice_id, product_id, quantity, unit_price, tax_rate, line_total)
    VALUES 
    (:invoice_id, :product_id, :quantity, :unit_price, :tax_rate, :line_total)
");

foreach ($items as $item) {

    // Skip empty rows
    if (empty($item['product_id'])) continue;

    $itemStmt->execute([
        ':invoice_id' => $invoice_id,
        ':product_id' => $item['product_id'],
        ':quantity'   => $item['qty'],
        ':unit_price' => $item['rate'],
        ':tax_rate'   => $item['tax'],
        ':line_total' => $item['amount'],
    ]);
}

        // ---------------------------
        // COMMIT
        // ---------------------------
        $pdo->commit();

        header("Location: Invoice?success=1");
        exit;

    } catch (Exception $e) {
        $pdo->rollBack();
        echo "Error: " . $e->getMessage();
    }
}