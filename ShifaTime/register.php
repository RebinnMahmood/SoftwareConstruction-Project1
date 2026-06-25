<?php
header("Content-Type: application/json");
require 'db.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(["status" => "error", "message" => "Invalid data"]);
    exit;
}

$name = $data['name'];
$email = $data['email'];
$password = $data['password'];
$role = $data['role'];
$specialization = $data['specialization'] ?? null;
$degree = $data['degree'] ?? null;
$location = $data['location'] ?? null;
$phone = $data['phone'] ?? null;
$image = $data['image'] ?? null;

// Prevent duplicate email
$stmt = $conn->prepare("SELECT id FROM users WHERE email=?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    echo json_encode(["status" => "error", "message" => "Email already registered"]);
    exit;
}
$stmt->close();

$stmt = $conn->prepare("INSERT INTO users (name, email, password, role, specialization, degree, location, phone, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssssssss", $name, $email, $password, $role, $specialization, $degree, $location, $phone, $image);

if ($stmt->execute()) {
    echo json_encode(["status" => "success"]);
} else {
    echo json_encode(["status" => "error", "message" => "Registration failed: " . $stmt->error]);
}
$stmt->close();
$conn->close();
?>
