<?php
session_start();
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'staff'])) {
    header("Location: ../login.php");
    exit();
}

include '../config.php';

// Fetch all incident reports with user information
$query = "SELECT ir.*, 
                 u.name as user_name, 
                 u.username as username,
                 u.contact as user_contact,
                 r.first_name as resident_first_name,
                 r.middle_name as resident_middle_name,
                 r.last_name as resident_last_name,
                 r.email as resident_email,
                 r.address as resident_address,
                 r.purok as resident_purok,
                 r.contact as resident_contact
          FROM incident_reports ir 
          LEFT JOIN users u ON ir.user_id = u.id 
          LEFT JOIN residents r ON u.resident_id = r.id
          ORDER BY ir.created_at DESC";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Incident Reports - Print View</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .report-item { margin-bottom: 20px; padding: 10px; border: 1px solid #ddd; page-break-inside: avoid; }
        .report-header { background-color: #f8f9fa; padding: 5px; margin-bottom: 10px; }
        .content { margin: 10px 0; }
        @media print {
            .no-print { display: none; }
            .report-item { page-break-inside: avoid; }
        }
    </style>
</head>
<body>

<div class="no-print" style="margin-bottom: 20px;">
    <button onclick="window.print()" class="btn">Print Reports</button>
    <button onclick="window.close()">Close</button>
</div>

<div class="header">
    <h2>Barangay Incident Reports</h2>
    <p>Generated on: <?= date('F j, Y g:i A') ?></p>
    <p>Total Reports: <?= $result ? $result->num_rows : 0 ?></p>
</div>

<?php if ($result && $result->num_rows > 0): ?>
    <?php while ($report = $result->fetch_assoc()): ?>
        <?php
        $reporterName = $report['resident_first_name'] ? 
            trim($report['resident_first_name'] . ' ' . ($report['resident_middle_name'] ?? '') . ' ' . $report['resident_last_name']) :
            $report['user_name'];
        
        $reporterEmail = $report['resident_email'] ?: 'N/A';
        $reporterContact = $report['resident_contact'] ?: ($report['user_contact'] ?: 'N/A');
        ?>
        
        <div class="report-item">
            <div class="report-header">
                <strong>Report #<?= $report['id'] ?></strong> - 
                Status: <strong><?= $report['status'] ?></strong> - 
                Date: <?= date('M j, Y g:i A', strtotime($report['created_at'])) ?>
            </div>
            
            <div style="margin-bottom: 10px;">
                <strong>Reporter:</strong> <?= htmlspecialchars($reporterName) ?><br>
                <strong>Email:</strong> <?= htmlspecialchars($reporterEmail) ?><br>
                <strong>Contact:</strong> <?= htmlspecialchars($reporterContact) ?><br>
                <?php if ($report['resident_purok']): ?>
                    <strong>Purok:</strong> <?= htmlspecialchars($report['resident_purok']) ?><br>
                <?php endif; ?>
                <strong>Secretary:</strong> <?= htmlspecialchars($report['secretary_name']) ?>
            </div>
            
            <div class="content">
                <strong>Report Content:</strong><br>
                <div style="border: 1px solid #ddd; padding: 10px; background-color: #f9f9f9;">
                    <?= $report['content'] ?>
                </div>
            </div>
        </div>
    <?php endwhile; ?>
<?php else: ?>
    <p style="text-align: center; color: #666;">No incident reports found.</p>
<?php endif; ?>

</body>
</html>