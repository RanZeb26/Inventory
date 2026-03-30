<?php
include "../config/db.php";
header("Content-Type: application/json");

$id = $_GET['id'] ?? null;

try {
        $stmt = $pdo->prepare("SELECT 
            ih.invoice_id,
            ih.invoice_no,
            ih.invoice_date,
            ih.total_amount,
            c.customer_name
        FROM invoice_header ih
        LEFT JOIN customers c ON c.customer_id = ih.customer_id
        WHERE ih.invoice_id = ?
        ORDER BY ih.invoice_id DESC

    ");
        $stmt->execute([$id]);


    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));

} catch (PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>
