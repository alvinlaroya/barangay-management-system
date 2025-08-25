<?php
include '../config.php';
$id = $_GET['id'];
$conn->query("DELETE FROM blotters WHERE id = $id");
header("Location: index.php");
