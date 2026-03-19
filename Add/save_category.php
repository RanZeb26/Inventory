<?php
session_start();
include "../config/db.php";
header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $cat_name = trim($_POST['name']);
    $status = trim($_POST['status']);

    if (empty($cat_name) || empty($status)) {
        $error = "All fields are required.";
        header("Location: Products?error=" . urlencode($error));
        exit();
    }

    try {

        // CHECK if this product already has an adjustment today
        $checkStmt = $pdo->prepare("
            SELECT COUNT(*) AS total
            FROM category_item
            WHERE cat_name = :cat_name
        ");

        $checkStmt->execute([":cat_name" => $cat_name]);
        $count = $checkStmt->fetchColumn();

        if ($count > 0) {
            $error = "This Category already exists";
            header("Location: Products?error=" . urlencode($error));
            exit();
        }

            // INSERT adjustment
            $stmt = $pdo->prepare("
            INSERT INTO category_item 
                (cat_name, status)
            VALUES 
                (:cat_name, :status)
        ");
            $stmt->execute([
                ":cat_name" => $cat_name,
                ":status" => $status
            ]);
                $success = "Category recorded successfully!";   
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
