<?php
header('Content-Type: application/json');

$host = "localhost";
$user = "root";
$password = "";
$db = "inventory"; // Change to your database name

$conn = new mysqli($host, $user, $password, $db);
if ($conn->connect_error) {
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

// Get latest year
$latestYearResult = $conn->query("SELECT MAX(year) as max_year FROM earnings");
$latestYear = $latestYearResult->fetch_assoc()['max_year'];
$previousYear = $latestYear - 1;

// Fetch earnings for current and previous year
$data = ['labels' => [], 'current' => [], 'previous' => []];

$sql = "SELECT month, amount FROM earnings WHERE year = $latestYear ORDER BY id ASC";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $data['labels'][] = $row['month'];
    $data['current'][] = (float)$row['amount'];
}

$sql2 = "SELECT amount FROM earnings WHERE year = $previousYear ORDER BY id ASC";
$result2 = $conn->query($sql2);
while ($row = $result2->fetch_assoc()) {
    $data['previous'][] = (float)$row['amount'];
}

// Totals
$currentTotal = array_sum($data['current']);
$previousTotal = array_sum($data['previous']);
$growth = $previousTotal > 0 ? round((($currentTotal - $previousTotal) / $previousTotal) * 100, 2) : 0;

$data['summary'] = [
  'currentTotal' => number_format($currentTotal, 0),
  'previousTotal' => number_format($previousTotal, 0),
  'growth' => $growth
];

echo json_encode($data);
