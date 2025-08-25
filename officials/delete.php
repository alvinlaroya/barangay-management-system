<?php
include '../config.php';
$id = $_GET['id'];
$conn->query("DELETE FROM barangay_officials WHERE id = $id");
header("Location: index.php");
?>
