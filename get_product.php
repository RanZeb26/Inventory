<?php
$conn = new mysqli("localhost", "root", "", "inventory");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Search logic
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$search_sql = $search ? "WHERE name LIKE '%$search%' OR sku LIKE '%$search%'" : "";

// Pagination
$limit = 8;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Count total
$total_query = $conn->query("SELECT COUNT(*) AS total FROM products $search_sql");
$total_row = $total_query->fetch_assoc();
$total_products = $total_row['total'];
$total_pages = ceil($total_products / $limit);

// Fetch records
$sql = "SELECT * FROM products $search_sql LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);

// Fetch items before rendering modal
$items = [];
$query = "SELECT * FROM products WHERE status = 'Active'";
$results = $conn->query($query);
if ($results->num_rows > 0) {
    while ($row = $results->fetch_assoc()) {
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
}
?>