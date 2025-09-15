<?php
include 'config.php';

$type = $_GET['type'] ?? '';
$id = $_GET['id'] ?? '';

if (!$id || !in_array($type, ['document', 'image'])) {
    http_response_code(400);
    exit('Invalid request');
}

$stmt = $conn->prepare("SELECT supporting_documents, proof_of_damage FROM assistance_requests WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows !== 1) {
    http_response_code(404);
    exit('File not found');
}
$row = $result->fetch_assoc();

if ($type === 'document' && !empty($row['supporting_documents'])) {
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="supporting_document_' . $id . '.pdf"');
    echo $row['supporting_documents'];
    exit();
} elseif ($type === 'image' && !empty($row['proof_of_damage'])) {
    header('Content-Type: image/jpeg');
    header('Content-Disposition: inline; filename="proof_of_damage_' . $id . '.jpg"');
    echo $row['proof_of_damage'];
    exit();
} else {
    http_response_code(404);
    exit('File not found');
}
