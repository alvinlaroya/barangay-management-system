<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'resident') {
    header("Location: login.php");
    exit();
}
include 'config.php';

$resident_id = $_SESSION['resident_id'];
$full_name = $_SESSION['name'];

$clearances = $conn->query("SELECT * FROM clearances WHERE resident_id = $resident_id ORDER BY issued_date DESC");
$blotters = $conn->query("SELECT * FROM blotters WHERE complainant = '$full_name' ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Requests</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 + Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <style>
        body {
            background: linear-gradient(135deg, #e3f2fd, #ffffff);
            font-family: 'Segoe UI', sans-serif;
            min-height: 100vh;
        }

        .container {
            max-width: 1100px;
        }

        h3 {
            font-weight: 700;
            color: #0d6efd;
        }

        .card {
            border-radius: 16px;
            border: none;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
            transition: transform 0.2s;
        }

        .card:hover {
            transform: translateY(-4px);
        }

        .section-title {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 1rem;
            border-left: 5px solid #0d6efd;
            padding-left: 12px;
            font-size: 1.2rem;
        }

        .table {
            border-radius: 12px;
            overflow: hidden;
        }

        .table thead {
            background: #f1f5fb;
        }

        .table th {
            font-weight: 600;
            color: #34495e;
        }

        .badge {
            font-size: 0.85em;
            padding: 6px 10px;
            border-radius: 8px;
        }

        .text-muted {
            font-style: italic;
        }
    </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container py-4">
    <h3 class="mb-4"><i class="bi bi-clock-history me-2"></i>My Request History</h3>

    <!-- Barangay Clearance Section -->
    <div class="card p-4 mb-4">
        <h5 class="section-title"><i class="bi bi-file-earmark-text-fill me-2"></i>Barangay Clearance Requests</h5>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Purpose</th>
                        <th>Remarks</th>
                        <th>Status</th>
                        <th>Issued Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($clearances->num_rows > 0): ?>
                        <?php while ($c = $clearances->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($c['purpose']) ?></td>
                            <td><?= htmlspecialchars($c['remarks']) ?></td>
                            <td>
                                <span class="badge bg-<?= 
                                    $c['status'] === 'Pending' ? 'warning' :
                                    ($c['status'] === 'Ready for Pickup' ? 'info' : 'success') ?>">
                                    <?= $c['status'] ?>
                                </span>
                            </td>
                            <td><?= date('M d, Y', strtotime($c['issued_date'])) ?></td>
                        </tr>
                        <?php endwhile ?>
                    <?php else: ?>
                        <tr><td colspan="4" class="text-center text-muted">No clearance requests found.</td></tr>
                    <?php endif ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Blotter Complaints Section -->
    <div class="card p-4">
        <h5 class="section-title"><i class="bi bi-exclamation-triangle-fill me-2"></i>Blotter Complaints</h5>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Respondent</th>
                        <th>Type</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Hearing</th>
                        <th>Filed On</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($blotters->num_rows > 0): ?>
                        <?php while ($b = $blotters->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($b['respondent']) ?></td>
                            <td><?= htmlspecialchars($b['incident_type']) ?></td>
                            <td><?= htmlspecialchars($b['description']) ?></td>
                            <td>
                                <span class="badge bg-<?= 
                                    $b['status'] === 'Pending' ? 'warning' :
                                    ($b['status'] === 'Settled' ? 'success' :
                                    ($b['status'] === 'Dismissed' ? 'danger' : 'secondary')) ?>">
                                    <?= $b['status'] ?>
                                </span>
                            </td>
                            <td>
                                <?= !empty($b['schedule_datetime']) 
                                    ? date('M d, Y h:i A', strtotime($b['schedule_datetime'])) 
                                    : '<span class="text-muted">Not Scheduled</span>' ?>
                            </td>
                            <td><?= date('M d, Y', strtotime($b['created_at'])) ?></td>
                        </tr>
                        <?php endwhile ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="text-center text-muted">No blotter complaints filed.</td></tr>
                    <?php endif ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>
