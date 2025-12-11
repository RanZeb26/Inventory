<?php
include "../config/db.php";
header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $customer_name = trim($_POST['customer_name']);
    $company_name = trim($_POST['company_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $customer_id = trim($_POST['customer_id']);
    $image = '';
    $user_id = $_SESSION['id'] ?? null;
    
      // Get current image path
    $stmt = $pdo->prepare("SELECT image FROM customers WHERE customer_id = ?");
    $stmt->execute([$customer_id]);
    $row = $stmt->fetch();
    $oldImage = $row['image'];

        // Set default image path (existing one)
    $image = $oldImage;
    // Handle image upload
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
    }

    // Update all product details
    $stmt = $pdo->prepare("UPDATE customers SET customer_name=?, company_name=?, email=?, phone=?, address=?, image=? WHERE customer_id=?");
    $success = $stmt->execute([
        $customer_name,
        $company_name,
        $email,
        $phone,
        $address,
        $image,
        $customer_id

    ]);
    if ($success) {
        $error = "Item updated successfully!";
        header("Location: Customer?success=" . urlencode($error));
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to update product."]);
    }
}
