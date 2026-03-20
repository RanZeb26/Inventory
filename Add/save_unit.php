<?php
session_start();
include "../config/db.php";
header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $unit_name = trim($_POST['name']);
    $status = trim($_POST['status']);

    if (empty($unit_name) || empty($status)) {
        $error = "All fields are required.";
        header("Location: Products?error=" . urlencode($error));
        exit();
    }

    try {

        // CHECK if this product already has an adjustment today
        $checkStmt = $pdo->prepare("
            SELECT COUNT(*) AS total
            FROM unit_item
            WHERE unit_name = :unit_name
        ");

        $checkStmt->execute([":unit_name" => $unit_name]);
        $count = $checkStmt->fetchColumn();

        if ($count > 0) {
            $error = "This Unit already exists";
            header("Location: Products?error=" . urlencode($error));
            exit();
        }

            // INSERT adjustment
            $stmt = $pdo->prepare("
            INSERT INTO unit_item 
                (unit_name, status)
            VALUES 
                (:unit_name, :status)
        ");
            $stmt->execute([
                ":unit_name" => $unit_name,
                ":status" => $status
            ]);
                $success = "Unit recorded successfully!";   
            header("Location: Products?success=" . urlencode($success));
            exit();
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        echo json_encode([
            "status" => "error",
            "message" => "❌ Database error: " . $e->getMessage()
        ]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "❌ Invalid request."]);
}
