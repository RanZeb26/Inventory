<?php
include "../config/db.php"; // Your DB connection

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!is_array($data) || count($data) === 0) {
        echo json_encode(["status" => "error", "message" => "No data received"]);
        exit;
    }

    try {
        // Start transaction
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("
            INSERT INTO selected_items (sku, name, quantity, items_cost, total_cost) 
            VALUES (?, ?, ?, ?, ?)
        ");

        foreach ($data as $item) {
            $sku   = $item['sku'] ?? '';
            $name  = $item['name'] ?? '';
            $quantity   = isset($item['quantity']) ? (int)$item['quantity'] : 0;
            $items_cost  = isset($item['items_cost']) ? (float)$item['items_cost'] : 0;
            $total_cost = isset($item['total_cost']) ? (float)$item['total_cost'] : 0;

            $stmt->execute([$sku, $name, $quantity, $items_cost, $total_cost]);
        }

        // Commit transaction
        $pdo->commit();

        echo json_encode(["status" => "success", "message" => "Items saved successfully"]);

    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method"]);
}
?>