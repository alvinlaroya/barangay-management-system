<?php
include '../config.php';
$id = $_GET['id'];
$conn->query("DELETE FROM households WHERE id = $id");
header("Location: index.php");
