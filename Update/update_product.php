<?php
include "../config/db.php";
header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = trim($_POST['id']);
    $sku = trim($_POST['sku']);
    $barcode = trim($_POST['barcode']);
    $name = trim($_POST['name']);
    $category = trim($_POST['category']);
    $brand = trim($_POST['brand']);
    $unit = trim($_POST['unit']);
    $status = trim($_POST['status']);
    $cost_price = trim($_POST['price']);
    $selling_price = trim($_POST['selling_price']);
    $reorder_level = trim($_POST['stock_level']);
    $description = trim($_POST['description']);

    $image = '';

    // Get current image path
    $stmt = $pdo->prepare("SELECT image FROM products WHERE product_id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    $oldImage = $row['image'];

    // Set default image path (existing one)
    $image = $oldImage;
    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
        $targetDir = "images/";
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
                $image = $targetFile;
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
    $stmt = $pdo->prepare("UPDATE products SET sku=?, barcode=?, name=?, category=?, brand=?, unit=?, status=?, cost_price=?, selling_price=?, reorder_level=?, description=?, image=? WHERE product_id=?");

    $success = $stmt->execute([
        $sku, $barcode, $name, $category, $brand, $unit, $status,
        $cost_price, $selling_price, $reorder_level, $description, $image, $id
    ]);

    if ($success) {
        $error = "Item added successfully!";
        header("Location: Products?success=" . urlencode($error));
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to update product."]);
    }
}
?>
