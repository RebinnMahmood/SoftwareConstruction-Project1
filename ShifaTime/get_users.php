<?php
header('Content-Type: application/json');
require 'db.php';
$result = $conn->query("SELECT id, name, email, role, specialization, degree, location, phone, image FROM users");
$users = [];
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}
echo json_encode($users);
$conn->close();
