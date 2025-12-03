<?php
include "config/db.php";
header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sku = trim($_POST['sku']);
    $barcode = trim($_POST['barcode']);
    $name = trim($_POST['name']);
    $category = trim($_POST['category']);
    $brand = trim($_POST['brand']);
    $unit = trim($_POST['unit']);
    $status = trim($_POST['status']);
    $cost_price = trim($_POST['cost_price']);
    $selling_price = trim($_POST['selling_price']);
    $reorder_level = trim($_POST['stock_level']);
    $description = trim($_POST['description']);
    $image = '';

    if (empty($sku)) {
        $error = "All fields are required.";
        header("Location: Products?error=" . urlencode($error));
        exit();
    }

    try {
        // ✅ Check if item name already exists
        $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE name = :name");
        $checkStmt->execute([":name" => $name]);
        $count = $checkStmt->fetchColumn();

        if ($count > 0) {
            $error = "Item name already exists.";
            header("Location: Products?error=" . urlencode($error));
            exit();
        }
        if (isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
            $targetDir = "images/";
            $image = $targetDir . basename($_FILES["image"]["name"]);
            move_uploaded_file($_FILES["image"]["tmp_name"], $image);
        // ✅ Insert the new item if no duplicate is found
        $stmt = $pdo->prepare("INSERT INTO products (sku, barcode, name, category, brand, unit, status, cost_price, selling_price, reorder_level, description, image) 
                               VALUES (:sku, :barcode, :name, :category, :brand, :unit, :status, :cost_price, :selling_price, :reorder_level, :description, :image)");

        $stmt->execute([
            ":sku" => $sku,
            ":barcode" => $barcode,
            ":name" => $name,
            ":category" => $category,
            ":brand" => $brand,
            ":unit" => $unit,
            ":status" => $status,
            ":cost_price" => $cost_price,
            ":selling_price" => $selling_price,
            ":reorder_level" => $reorder_level,
            ":description" => $description,
            ":image" => $image
        ]);
        $error = "Item added successfully!";
        header("Location: Products?success=" . urlencode($error));
} else {
             // ✅ Insert the new item if no image is found
        $stmt = $pdo->prepare("INSERT INTO products (sku, barcode, name, category, brand, unit, status, cost_price, selling_price, reorder_level, description) 
                               VALUES (:sku, :barcode, :name, :category, :brand, :unit, :status, :cost_price, :selling_price, :reorder_level, :description)");

        $stmt->execute([
            ":sku" => $sku,
            ":barcode" => $barcode,
            ":name" => $name,
            ":category" => $category,
            ":brand" => $brand,
            ":unit" => $unit,
            ":status" => $status,
            ":cost_price" => $cost_price,
            ":selling_price" => $selling_price,
            ":reorder_level" => $reorder_level,
            ":description" => $description
            ]);

        $error = "Item added successfully!";
        header("Location: Products?success=" . urlencode($error));
    }
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        echo json_encode(["status" => "error", "message" => "❌ Database error: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "❌ Invalid request."]);
}
?>
