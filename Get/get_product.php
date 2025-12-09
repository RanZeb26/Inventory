<?php
require 'config/db.php'; // your PDO connection

// ---------------------------
// 🔍 Search logic (safe)
// ---------------------------
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

$search_sql = "";
$search_params = [];

if (!empty($search)) {
    $search_sql = "WHERE name LIKE :search";
    $search_params[':search'] = "%$search%";
}

// ---------------------------
// 📌 Pagination
// ---------------------------
$limit = 8;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// ---------------------------
// 📊 Count total products
// ---------------------------
$total_stmt = $pdo->prepare("SELECT COUNT(*) AS total FROM products $search_sql");
$total_stmt->execute($search_params);
$total_products = $total_stmt->fetchColumn();
$total_pages = ceil($total_products / $limit);

// ---------------------------
// 📂 Fetch paginated records
// ---------------------------
$sql = "SELECT * FROM products $search_sql LIMIT :limit OFFSET :offset";
$stmt = $pdo->prepare($sql);

// Bind search if needed
if (!empty($search)) {
    $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
}

// Bind pagination
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

$stmt->execute();
$result = $stmt->fetchAll();

// ---------------------------
// 📦 Fetch Active Items (for modal)
// ---------------------------
$items_stmt = $pdo->prepare("SELECT * FROM products WHERE status = 'Active'");
$items_stmt->execute();
$rows = $items_stmt->fetchAll();

$items = [];

foreach ($rows as $row) {
    $items[] = [
        'id'         => $row['product_id'],
        'sku'        => $row['sku'],
        'name'       => $row['name'],
        'description'=> $row['description'],
        'quantity'   => $row['quantity'],
        'cost_price' => $row['cost_price'],
        'total_cost' => $row['cost_price'] * $row['quantity'],
        'category'   => $row['category']
    ];
}
?>
