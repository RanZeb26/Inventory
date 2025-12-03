<?php
$conn = new mysqli("localhost", "root", "", "calendar");
$data = json_decode(file_get_contents("php://input"), true);
$id = (int)$data['id'];
$sql = "DELETE FROM events WHERE id=$id";
echo json_encode(['success' => $conn->query($sql)]);
?>
