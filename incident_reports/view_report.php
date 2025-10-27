<?php
session_start();
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'staff'])) {
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

// Get report details with user information and resident details
$stmt = $conn->prepare("
    SELECT ir.*, 
           u.name as user_name, 
           u.username as username,
           u.contact as user_contact,
           r.first_name as resident_first_name,
           r.middle_name as resident_middle_name,
           r.last_name as resident_last_name,
           r.email as resident_email,
           r.address as resident_address,
           r.purok as resident_purok,
           r.contact as resident_contact
    FROM incident_reports ir 
    LEFT JOIN users u ON ir.user_id = u.id 
    LEFT JOIN residents r ON u.resident_id = r.id
    WHERE ir.id = ?
");
$stmt->bind_param("i", $report_id);
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