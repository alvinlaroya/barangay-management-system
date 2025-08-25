<?php
include '../config.php'; // ✅ Correct path based on your folder structure

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE clearances SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $id);
    $stmt->execute();

    header("Location: index.php"); // back to clearance list
    exit();
}
?>
