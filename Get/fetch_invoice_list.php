<?php
include "config/db.php";


// Get search input
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$search_sql = "";
$params = [];

if (!empty($search)) {
    $search_sql = "WHERE invoice_no LIKE :search";
    $params[':search'] = "%$search%";
}

// Pagination setup
$limit = 8;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// Count total records
$total_query = $pdo->prepare("SELECT COUNT(*) AS total FROM invoice_header $search_sql");
$total_query->execute($params);
$total_row = $total_query->fetch(PDO::FETCH_ASSOC);
$total_products = $total_row ? $total_row['total'] : 0;
$total_pages = ceil($total_products / $limit);

// Fetch records
$sql = "SELECT ih.invoice_id,
            ih.invoice_no,
            ih.invoice_date,
            ih.total_amount,
            ih.status,
            ih.due_date,
            ih.invoice_date,
            c.customer_name,
            c.phone,c.email,
            c.customer_id,
            c.company_name
FROM invoice_header ih
        LEFT JOIN customers c ON c.customer_id = ih.customer_id
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

$query = "SELECT ih.invoice_id, c.customer_name,c.customer_id

FROM invoice_header ih
        LEFT JOIN customers c ON c.customer_id = ih.customer_id";
$results = $pdo->query($query);
if ($results->rowCount() > 0) {
    while ($row = $results->fetch(PDO::FETCH_ASSOC)) {
        $items[] = [
            'id'         => $row['customer_id'],
            'name'       => $row['customer_name'],
        ];
    }
}
