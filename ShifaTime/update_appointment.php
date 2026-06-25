<?php
header('Content-Type: application/json');
require 'db.php';
$data = json_decode(file_get_contents('php://input'), true);
if (!$data || !isset($data['id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
    exit;
}
$id = $data['id'];

// Build dynamic update fields
$fields = [];
$params = [];
$types = '';

if (isset($data['slot'])) {
    $fields[] = 'slot=?';
    $params[] = $data['slot'];
    $types .= 's';
}
if (isset($data['details'])) {
    $fields[] = 'details=?';
    $params[] = $data['details'];
    $types .= 's';
}
if (isset($data['status'])) {
    $fields[] = 'status=?';
    $params[] = $data['status'];
    $types .= 's';
}
if (empty($fields)) {
    echo json_encode(['status' => 'error', 'message' => 'No fields to update']);
    exit;
}
$params[] = $id;
$types .= 'i';
$sql = "UPDATE appointments SET ".implode(',', $fields)." WHERE id=?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(['status' => 'error', 'message' => 'Prepare failed: '.$conn->error]);
    exit;
}
$stmt->bind_param($types, ...$params);
if ($stmt->execute()) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => $stmt->error]);
}
$stmt->close();
$conn->close();
?>
