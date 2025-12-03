<?php
include "config/db.php"; // Database connection

// Get search input
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$search_sql = "";
$params = [];

if (!empty($search)) {
    $search_sql = "WHERE product_name LIKE :search";
    $params[':search'] = "%$search%";
}

// Pagination setup
$limit = 8;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// Count total records
$total_query = $pdo->prepare("SELECT COUNT(*) AS total FROM adjustments $search_sql");
$total_query->execute($params);
$total_row = $total_query->fetch(PDO::FETCH_ASSOC);
$total_products = $total_row ? $total_row['total'] : 0;
$total_pages = ceil($total_products / $limit);

// Fetch records
$sql = "SELECT * FROM adjustments $search_sql LIMIT :limit OFFSET :offset";
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
$query = "SELECT * FROM adjustments WHERE status = 'Active'";
$results = $pdo->query($query);
if ($results->rowCount() > 0) {
    while ($row = $results->fetch(PDO::FETCH_ASSOC)) {
        $items[] = [
            'id'         => $row['reference_id'],
            'name'       => $row['product_name'],
            'reason'    => $row['reasons'],
            'status'   => $row['status'],
        ];
    }
}

?>
