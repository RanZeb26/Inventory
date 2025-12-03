<?php
$conn = new mysqli("localhost", "root", "", "calendar");
$result = $conn->query("SELECT id, title, start, end FROM events");
$events = [];
while ($row = $result->fetch_assoc()) {
    $events[] = $row;
}
header('Content-Type: application/json');
echo json_encode($events);
?>
