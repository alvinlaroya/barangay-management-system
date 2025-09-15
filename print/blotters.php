<?php
include '../config.php';

// Handle hearing schedule update submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['schedule_datetime'], $_POST['blotter_id'])) {
    $schedule_datetime = $_POST['schedule_datetime'];
    $blotter_id = $_POST['blotter_id'];

    $stmt = $conn->prepare("UPDATE blotters SET schedule_datetime = ? WHERE id = ?");
    $stmt->bind_param("si", $schedule_datetime, $blotter_id);
    $stmt->execute();

    header("Location: index.php");
    exit();
}

$blotters = $conn->query("SELECT * FROM blotters ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Blotter Records - Barangay System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 & Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background-color: #f8f9fa;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        }
        .table th {
            background-color: #343a40;
            color: white;
        }
        .table td, .table th {
            vertical-align: middle;
        }
        .btn-sm {
            padding: 4px 10px;
        }
        input[type="datetime-local"] {
            min-width: 200px;
        }
        @media print {
            body * {
                visibility: hidden;
            }
            .print-area, .print-area * {
                visibility: visible;
            }
            .print-area {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                padding: 0 !important;
                margin: 0 !important;
                overflow: visible !important;
            }
            .print-area .table-responsive {
                overflow: visible !important;
                box-shadow: none !important;
            }
            .print-area table {
                margin: 0 !important;
            }
            .print-area th, .print-area td {
                padding: 0 !important;
                white-space: nowrap !important;
            }
            @page {
                size: landscape;
            }
        }
    </style>
</head>
<body>

<?php include '../navbar.php'; ?>

<div class="container mt-5">
    <div class="card p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0"><i class="bi bi-exclamation-triangle-fill text-danger"></i> Barangay Blotter Records</h4>
            <button class="btn btn-success btn-sm" onclick="printTable()" type="button">
                <i class="bi bi-printer"></i> PRINT
            </button>
        </div>

        <div class="table-responsive print-area">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Complainant</th>
                        <th>Respondent</th>
                        <th>Type</th>
                        <th>Incident Date</th>
                        <th>Status</th>
                        <th>Hearing Schedule</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($b = $blotters->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($b['complainant']) ?></td>
                        <td><?= htmlspecialchars($b['respondent']) ?></td>
                        <td><?= htmlspecialchars($b['incident_type']) ?></td>
                        <td><?= !empty($b['incident_datetime']) ? date('M d, Y h:i A', strtotime($b['incident_datetime'])) : '<span class="text-muted">N/A</span>' ?></td>
                        <td><?= htmlspecialchars($b['status']) ?></td>
                        <td><?= htmlspecialchars(!empty($b['schedule_datetime']) ? date('Y-m-d\TH:i', strtotime($b['schedule_datetime'])) : '') ?></td>
                    </tr>
                    <?php endwhile ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function printTable() {
    window.print();
}
</script>
</body>
</html>
