<?php
session_start();
include "../config/db.php";
header('Content-Type: application/json');

$conn->query("DELETE FROM invoices WHERE id=" . $_POST['id']);
echo "deleted";