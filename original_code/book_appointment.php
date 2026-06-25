<?php
header("Content-Type: application/json");
require 'db.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(["status" => "error", "message" => "Invalid data"]);
    exit;
}

// Find doctor and patient IDs
$doctor_email = $data['doctor_email'] ?? '';
$patient_email = $data['email'] ?? '';

$doctor_id = null;
$patient_id = null;

if ($doctor_email) {
    $stmt = $conn->prepare("SELECT id FROM users WHERE email=? AND role='doctor'");
    $stmt->bind_param("s", $doctor_email);
    $stmt->execute();
    $stmt->bind_result($doctor_id);
    $stmt->fetch();
    $stmt->close();
}
if ($patient_email) {
    $stmt = $conn->prepare("SELECT id FROM users WHERE email=? AND role='patient'");
    $stmt->bind_param("s", $patient_email);
    $stmt->execute();
    $stmt->bind_result($patient_id);
    $stmt->fetch();
    $stmt->close();
}

if (!$doctor_id || !$patient_id) {
    echo json_encode(["status" => "error", "message" => "Doctor or patient not found"]);
    exit;
}

$stmt = $conn->prepare("INSERT INTO appointments (doctor_id, patient_id, doctor_name, patient_name, age, email, phone, details, slot, fee) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param(
    "iississssd",
    $doctor_id,
    $patient_id,
    $data['doctor_name'],
    $data['patient_name'],
    $data['age'],
    $data['email'],
    $data['phone'],
    $data['details'],
    $data['slot'],
    $data['fee']
);

if ($stmt->execute()) {
    echo json_encode(["status" => "success"]);
} else {
    echo json_encode(["status" => "error", "message" => "Failed to insert appointment"]);
}
$stmt->close();
$conn->close();
?>
