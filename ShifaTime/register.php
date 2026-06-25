<?php
header("Content-Type: application/json");
require 'db.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(["status" => "error", "message" => "Invalid data"]);
    exit;
}

// ✅ FIX 1: Input Validation added
$name = trim($data['name'] ?? '');
$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';
$role = $data['role'] ?? '';
$specialization = $data['specialization'] ?? null;
$degree = $data['degree'] ?? null;
$location = $data['location'] ?? null;
$phone = $data['phone'] ?? null;
$image = $data['image'] ?? null;

// Validate name
if (empty($name)) {
    echo json_encode(["status" => "error", "message" => "Name is required"]);
    exit;
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["status" => "error", "message" => "Invalid email format"]);
    exit;
}

// Validate password length
if (strlen($password) < 6) {
    echo json_encode(["status" => "error", "message" => "Password must be at least 6 characters"]);
    exit;
}

// Validate role
if (!in_array($role, ['patient', 'doctor'])) {
    echo json_encode(["status" => "error", "message" => "Invalid role"]);
    exit;
}

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

// ✅ FIX 2: Hash password before storing
$hashedPassword = password_hash($password, PASSWORD_BCRYPT);

$stmt = $conn->prepare("INSERT INTO users (name, email, password, role, specialization, degree, location, phone, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssssssss", $name, $email, $hashedPassword, $role, $specialization, $degree, $location, $phone, $image);

if ($stmt->execute()) {
    echo json_encode(["status" => "success"]);
} else {
    echo json_encode(["status" => "error", "message" => "Registration failed: " . $stmt->error]);
}
$stmt->close();
$conn->close();
?>
