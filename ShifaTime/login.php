<?php
header("Content-Type: application/json");
require 'db.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(["status" => "error", "message" => "Invalid data"]);
    exit;
}

// ✅ FIX 1: Input Validation added
$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["status" => "error", "message" => "Invalid email format"]);
    exit;
}

if (empty($password)) {
    echo json_encode(["status" => "error", "message" => "Password is required"]);
    exit;
}

// ✅ FIX 2: Fetch user then verify hashed password
$stmt = $conn->prepare("SELECT id, name, email, password, role, specialization, degree, location, phone, image FROM users WHERE email=?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user && password_verify($password, $user['password'])) {
    // Remove password from response
    unset($user['password']);
    echo json_encode(["status" => "success", "user" => $user]);
} else {
    echo json_encode(["status" => "error", "message" => "Invalid credentials"]);
}
$stmt->close();
$conn->close();
?>
