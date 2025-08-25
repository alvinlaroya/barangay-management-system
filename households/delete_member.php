<?php
include '../config.php';
$id = $_GET['id'];
$household_id = $_GET['household_id'];
$conn->query("DELETE FROM household_members WHERE id = $id");
header("Location: view.php?id=$household_id");
