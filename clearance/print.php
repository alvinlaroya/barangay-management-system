<?php
include '../config.php';
$id = $_GET['id'];

$stmt = $conn->prepare("
    SELECT c.*, CONCAT(r.first_name, ' ', r.middle_name, ' ', r.last_name) AS full_name, r.address
    FROM clearances c
    JOIN residents r ON r.id = c.resident_id
    WHERE c.id = ?
");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Barangay Clearance</title>
    <style>
        body { font-family: Arial; margin: 40px; }
        .clearance-box { border: 2px solid black; padding: 20px; }
        h2, h4 { text-align: center; margin: 0; }
    </style>
</head>
<body onload="window.print()">
<div class="clearance-box">
    <h2>Republic of the Philippines</h2>
    <h4>Barangay Information Management System</h4>
    <h4>Barangay Clearance</h4>
    <hr>

    <p>This is to certify that <strong><?= $result['full_name'] ?></strong>, a resident of <strong><?= $result['address'] ?></strong>, is known to this office and has no derogatory record filed as of this date.</p>

    <p>This clearance is issued upon request of the above-named individual for the purpose of <strong><?= $result['purpose'] ?></strong>.</p>

    <p>Issued on <strong><?= date('F d, Y', strtotime($result['issued_date'])) ?></strong>.</p>

    <br><br>
    <p style="text-align:right;"><strong><?= $result['official_in_charge'] ?></strong><br>Barangay Official</p>
</div>
</body>
</html>
