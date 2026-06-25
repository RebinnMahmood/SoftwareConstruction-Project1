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
$role = 'doctor';
$specialization = $data['specialization'] ?? '';
$degree = $data['degree'] ?? '';
$location = $data['location'] ?? '';
$phone = $data['phone'] ?? '';
$image = $data['image'] ?? '';

// Check if doctor exists
$stmt = $conn->prepare("SELECT id FROM users WHERE email=? AND role='doctor'");
$stmt->bind_param('s', $email);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows === 0) {
    // Insert doctor
    $stmt->close();
    $stmt = $conn->prepare("INSERT INTO users (name, email, role, specialization, degree, location, phone, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('ssssssss', $name, $email, $role, $specialization, $degree, $location, $phone, $image);
    if ($stmt->execute()) {
        echo json_encode(['status' => 'created']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to create doctor']);
    }
    $stmt->close();
} else {
    echo json_encode(['status' => 'exists']);
    $stmt->close();
}
$conn->close();
