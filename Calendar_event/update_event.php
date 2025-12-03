<?php
$conn = new mysqli("localhost", "root", "", "calendar");
$data = json_decode(file_get_contents("php://input"), true);
$id = (int)$data['id'];
$title = $conn->real_escape_string($data['title']);
$start = $conn->real_escape_string($data['start']);
$end = $conn->real_escape_string($data['end']);
$sql = "UPDATE events SET title='$title', start='$start', end='$end' WHERE id=$id";
echo json_encode(['success' => $conn->query($sql)]);
?>
