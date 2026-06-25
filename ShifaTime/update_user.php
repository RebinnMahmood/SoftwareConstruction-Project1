<?php
header('Content-Type: application/json');
require 'db.php';
$data = json_decode(file_get_contents('php://input'), true);
if (!$data || !isset($data['id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
    exit;
}
$id = $data['id'];
$name = $data['name'] ?? '';
$role = $data['role'] ?? '';
$stmt = $conn->prepare("UPDATE users SET name=?, role=? WHERE id=?");
$stmt->bind_param('ssi', $name, $role, $id);
if ($stmt->execute()) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to update user']);
}
$stmt->close();
$conn->close();
