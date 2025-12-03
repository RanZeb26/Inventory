<?php
$conn = new mysqli("localhost", "root", "", "calendar");
$data = json_decode(file_get_contents("php://input"), true);
$title = $conn->real_escape_string($data['title']);
$start = $conn->real_escape_string($data['start']);
$end = $conn->real_escape_string($data['end']);
$sql = "INSERT INTO events (title, start, end) VALUES ('$title', '$start', '$end')";
echo json_encode(['success' => $conn->query($sql)]);
?>
