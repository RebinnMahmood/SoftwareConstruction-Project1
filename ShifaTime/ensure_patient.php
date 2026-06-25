<?php
header('Content-Type: application/json');
require 'db.php';

$data = json_decode(file_get_contents('php://input'), true);
if (!$data || !isset($data['email']) || !isset($data['name'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
    exit;
}
$email = $data['email'];
$name = $data['name'];
$role = 'patient';

// Check if patient exists
$stmt = $conn->prepare("SELECT id FROM users WHERE email=? AND role='patient'");
$stmt->bind_param('s', $email);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows === 0) {
    // Insert patient
    $stmt->close();
    $stmt = $conn->prepare("INSERT INTO users (name, email, role) VALUES (?, ?, ?)");
    $stmt->bind_param('sss', $name, $email, $role);
    if ($stmt->execute()) {
        echo json_encode(['status' => 'created']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to create patient']);
    }
    $stmt->close();
} else {
    echo json_encode(['status' => 'exists']);
    $stmt->close();
}
$conn->close();
