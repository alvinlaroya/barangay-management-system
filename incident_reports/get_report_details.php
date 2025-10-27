<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'resident') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Access denied']);
    exit();
}

include '../config.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid report ID']);
    exit();
}

$report_id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

// Get report details - only allow residents to view their own reports
$stmt = $conn->prepare("SELECT * FROM incident_reports WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $report_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Report not found']);
    exit();
}

$report = $result->fetch_assoc();

header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'report' => $report
]);
?>