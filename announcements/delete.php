<?php
include '../config.php';

// Set content type to JSON
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$id = $_POST['id'] ?? null;

if (!$id) {
    echo json_encode(['success' => false, 'message' => 'Announcement ID is required']);
    exit;
}

// Perform soft delete by setting deleted_at timestamp
$stmt = $conn->prepare("UPDATE announcements SET deleted_at = CURRENT_TIMESTAMP WHERE id = ? AND deleted_at IS NULL");
$stmt->bind_param("i", $id);

if ($stmt->execute() && $stmt->affected_rows > 0) {
    echo json_encode(['success' => true, 'message' => 'Announcement deleted successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to delete announcement or announcement not found']);
}

$stmt->close();
$conn->close();
?>