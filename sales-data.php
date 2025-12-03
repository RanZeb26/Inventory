<?php
header('Content-Type: application/json');

$host = "localhost";
$user = "root";
$password = "";
$database = "inventory"; // change this

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
  echo json_encode(['error' => 'Database connection failed']);
  exit;
}

$sql = "SELECT * FROM sales ORDER BY id ASC";
$result = $conn->query($sql);

$months = [];
$online = [];
$offline = [];
$marketing = [];
$totals = ['online' => 0, 'offline' => 0, 'marketing' => 0];

while ($row = $result->fetch_assoc()) {
  $months[] = $row['month'];
  $online[] = (int)$row['online'];
  $offline[] = (int)$row['offline'];
  $marketing[] = (int)$row['marketing'];

  $totals['online'] += (int)$row['online'];
  $totals['offline'] += (int)$row['offline'];
  $totals['marketing'] += (int)$row['marketing'];
}

$conn->close();

echo json_encode([
  'months' => $months,
  'online' => $online,
  'offline' => $offline,
  'marketing' => $marketing,
  'totals' => $totals
]);
