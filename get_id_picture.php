<?php
// get_id_picture.php
include 'config.php';

if (!isset($_GET['id'])) {
    http_response_code(400);
    exit('Missing resident ID.');
}

$id = intval($_GET['id']);
$stmt = $conn->prepare('SELECT id_picture, id_picture_type FROM residents WHERE id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    http_response_code(404);
    exit('Image not found.');
}

$stmt->bind_result($id_picture, $id_picture_type);
$stmt->fetch();

if ($id_picture === null) {
    http_response_code(404);
    exit('No image uploaded.');
}

if ($id_picture_type) {
    header('Content-Type: ' . $id_picture_type);
} else {
    header('Content-Type: image/jpeg');
}
echo $id_picture;
