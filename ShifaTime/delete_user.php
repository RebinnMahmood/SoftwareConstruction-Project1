<?php
header('Content-Type: application/json');
require 'db.php';
$data = json_decode(file_get_contents('php://input'), true);
if (!$data || !isset($data['id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
    exit;
}
$id = $data['id'];
$stmt = $conn->prepare("DELETE FROM users WHERE id=?");
$stmt->bind_param('i', $id);
if ($stmt->execute()) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to delete user']);
}
$stmt->close();
$conn->close();
