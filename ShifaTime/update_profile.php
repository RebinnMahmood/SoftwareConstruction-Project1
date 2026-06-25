<?php
header('Content-Type: application/json');
require 'db.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['email'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
    exit;
}

$email = $data['email'];
$name = $data['name'] ?? '';
$role = $data['role'] ?? '';
$specialization = $data['specialization'] ?? '';
$degree = $data['degree'] ?? '';
$location = $data['location'] ?? '';
$phone = $data['phone'] ?? '';
$image = $data['image'] ?? '';

if ($role === 'doctor') {
    $stmt = $conn->prepare("UPDATE users SET name=?, specialization=?, degree=?, location=?, phone=?, image=? WHERE email=? AND role='doctor'");
    $stmt->bind_param('sssssss', $name, $specialization, $degree, $location, $phone, $image, $email);
} else {
    $stmt = $conn->prepare("UPDATE users SET name=? WHERE email=? AND role='patient'");
    $stmt->bind_param('ss', $name, $email);
}

if ($stmt->execute()) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to update profile']);
}
$stmt->close();
$conn->close();
