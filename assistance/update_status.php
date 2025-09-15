<?php
include '../config.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'], $_POST['action'])) {
    $id = intval($_POST['id']);
    $action = $_POST['action'];
    if (in_array($action, ['Approved', 'Declined'])) {
        $stmt = $conn->prepare("UPDATE assistance_requests SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $action, $id);
        $stmt->execute();

    // Email notification removed; now handled by Email.js in frontend
    }
    header('Location: index.php');
    exit();
}
?>
