<?php
include "config/db.php"; // Database connection

// Get search input
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$search_sql = "";
$params = [];

if (!empty($search)) {
    $search_sql = "WHERE name LIKE :search";
    $params[':search'] = "%$search%";
}

// Pagination setup
$limit = 8;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// Count total records
$total_query = $pdo->prepare("SELECT COUNT(*) AS total FROM products i INNER JOIN products_adjustments a ON a.product_id = i.product_id $search_sql");
$total_query->execute($params);
$total_row = $total_query->fetch(PDO::FETCH_ASSOC);
$total_products = $total_row ? $total_row['total'] : 0;
$total_pages = ceil($total_products / $limit);

// Fetch records
$sql = "SELECT i.name,i.quantity,i.status,a.adj_id,a.adjustment_qty,a.reason,a.created_at
FROM products i
INNER JOIN products_adjustments a ON a.product_id = i.product_id
$search_sql LIMIT :limit OFFSET :offset";
$stmt = $pdo->prepare($sql);

if (!empty($search)) {
    $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
}
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();

$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch items before rendering modal
$items = [];

$query = "SELECT i.product_id,
    i.name,
    i.quantity,
    i.status,
    a.adj_id,
    a.adjustment_qty,
    a.reason,
    a.created_at FROM products i
INNER JOIN products_adjustments a 
    ON a.product_id = i.product_id
    INNER JOIN (
        SELECT product_id, MAX(created_at) AS last_adjusted
        FROM products_adjustments
        GROUP BY product_id
    ) x ON x.product_id = a.product_id AND x.last_adjusted = a.created_at";
$results = $pdo->query($query);
if ($results->rowCount() > 0) {
    while ($row = $results->fetch(PDO::FETCH_ASSOC)) {
        $items[] = [
            'id'         => $row['adj_id'],
            'name'       => $row['name'],
            'quantity'   => $row['quantity'],
            'adj_qty'    => $row['adjustment_qty'],
            'reason'    => $row['reason'],
            'status'   => $row['status'],
        ];
    }
}

?>
