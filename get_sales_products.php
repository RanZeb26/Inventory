<?php
header('Content-Type: application/json');

$host = "localhost";
$user = "root";
$password = "";
$database = "inventory"; // Change this

$conn = new mysqli($host, $user, $password, $database);

// Check connection
if ($conn->connect_error) {
  echo json_encode(['error' => 'Database connection failed']);
  exit;
}

$sql = "SELECT category, total_sales FROM product_sales";
$result = $conn->query($sql);

$data = [];

if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $data[] = [
      'label' => $row['category'],
      'value' => (int)$row['total_sales']
    ];
  }
} else {
  $data[] = ['label' => 'No Data', 'value' => 0];
}

$conn->close();

// Output JSON
echo json_encode($data);
