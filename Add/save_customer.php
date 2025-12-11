<?php
session_start();
include "../config/db.php";
header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $customer_name = trim($_POST['customer_name']);
    $company_name = trim($_POST['company_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $image = '';
    $user_id = $_SESSION['id'] ?? null;

    if (empty($customer_name) || empty($company_name) || empty($email) || empty($phone) || empty($address)) {
        $error = "All fields are required.";
        header("Location: Customer?error=" . urlencode($error));
        exit();
    }

    try {

        // CHECK if this product already has an adjustment today
        $checkStmt = $pdo->prepare("
            SELECT COUNT(*) AS total
            FROM customers
            WHERE customer_name = :customer_name
        ");

        $checkStmt->execute([":customer_name" => $customer_name]);
        $count = $checkStmt->fetchColumn();

        if ($count > 0) {
            $error = "This Customer already exists";
            header("Location: Customer?error=" . urlencode($error));
            exit();
        }

        if (isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
            $targetDir = "../images/";
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }
                    $fileName = uniqid() . "_" . basename($_FILES["image"]["name"]);
        $targetFile = $targetDir . $fileName;
        $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($fileType, $allowedTypes)) {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
                // Delete old image if exists
                if (!empty($oldImage) && file_exists($oldImage)) {
                    unlink($oldImage);
                }
                $image = "images/" . $fileName;
            } else {
                echo json_encode(["status" => "error", "message" => "Failed to upload new image."]);
                exit;
            }
        } else {
            echo json_encode(["status" => "error", "message" => "Invalid image format."]);
            exit;
        }
            // INSERT adjustment
            $stmt = $pdo->prepare("
            INSERT INTO customers 
                (customer_name, company_name, email, phone, address, image, added_by)
            VALUES 
                (:customer_name, :company_name, :email, :phone, :address, :image, :user_id)
        ");
            $stmt->execute([
                ":customer_name" => $customer_name,
                ":company_name" => $company_name,
                ":email" => $email,
                ":phone" => $phone,
                ":address" => $address,
                ":image" => $image,
                ":user_id" => $user_id,
            ]);
            $success = "Customer recorded successfully!";
            header("Location: Customer?success=" . urlencode($success));
            exit();
        } else {
            // ✅ Insert the new item if no image is found
            $stmt = $pdo->prepare("
                INSERT INTO customers 
                    (customer_name, company_name, email, phone, address, added_by)
                VALUES 
                    (:customer_name, :company_name, :email, :phone, :address, :user_id)
            ");
            $stmt->execute([
                ":customer_name" => $customer_name,
                ":company_name" => $company_name,
                ":email" => $email,
                ":phone" => $phone,
                ":address" => $address,
                ":user_id" => $user_id,
            ]);
        }
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
