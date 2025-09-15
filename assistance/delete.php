<?php
include '../config.php';
$id = $_GET['id'];
$conn->query("DELETE FROM assistance_requests WHERE id = $id");
header("Location: index.php");
