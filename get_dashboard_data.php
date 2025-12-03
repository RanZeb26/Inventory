<?php
header('Content-Type: application/json');

// DB credentials
$host = 'localhost';
$db   = 'inventory';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
  die(json_encode(['error' => 'Database connection failed']));
}

// Get Events counts 
$sql = "SELECT type, COUNT(*) as total FROM events GROUP BY type";
$result = $conn->query($sql);

$eventStats = ['critical' => 0, 'error' => 0, 'warning' => 0];
while ($row = $result->fetch_assoc()) {
  $eventStats[$row['type']] = (int)$row['total'];
}

// Get progress Sessions by Channel (example: % of closed events)
$sql2 = "SELECT 
            (SELECT COUNT(*) FROM events WHERE status='closed') / COUNT(*) 
         AS progress FROM events";
$result2 = $conn->query($sql2);
$row2 = $result2->fetch_assoc();
$circleProgress = round($row2['progress'], 2); // e.g. 0.75 for 75%

$conn->close();

// Output as JSON
echo json_encode([
  'circleProgress' => $circleProgress,
  'eventStats' => $eventStats
]);
?>
