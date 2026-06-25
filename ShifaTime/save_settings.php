<?php
// save_settings.php
header('Content-Type: application/json');
require_once 'db.php';
$data = json_decode(file_get_contents('php://input'), true);
if (!isset($data['siteTitle']) || !isset($data['siteEmail'])) {
    echo json_encode(['status' => 'error', 'message' => 'Missing fields']);
    exit;
}
$title = $data['siteTitle'];
$email = $data['siteEmail'];
// Use a settings table with unique row (id=1)
$sql = "INSERT INTO settings (id, site_title, site_email) VALUES (1, ?, ?) ON DUPLICATE KEY UPDATE site_title=VALUES(site_title), site_email=VALUES(site_email)";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ss', $title, $email);
if ($stmt->execute()) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => $stmt->error]);
}
$stmt->close();
$conn->close();
?>
