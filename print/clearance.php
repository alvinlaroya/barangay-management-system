<?php
include '../config.php';

// Fetch clearances with full name and origin info
$clearances = $conn->query("
    SELECT c.*, 
           CONCAT(r.first_name, ' ', r.last_name) AS full_name,
           CASE 
               WHEN c.official_in_charge = '' OR c.official_in_charge IS NULL THEN 'Resident'
               ELSE 'Admin'
           END AS requested_by
    FROM clearances c
    JOIN residents r ON r.id = c.resident_id
    ORDER BY issued_date DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Barangay Clearances</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap & Icons -->
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
            <h4 class="mb-0"><i class="bi bi-file-earmark-text-fill text-primary"></i> Barangay Clearances</h4>
            <button class="btn btn-success btn-sm" onclick="printTable()" type="button">
                <i class="bi bi-printer"></i> PRINT
            </button>
        </div>

        <div class="table-responsive print-area">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Resident</th>
                        <th>Purpose</th>
                        <th>Issued Date</th>
                        <th>Official</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $clearances->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['full_name']) ?></td>
                        <td><?= htmlspecialchars($row['purpose']) ?></td>
                        <td><?= date('M d, Y', strtotime($row['issued_date'])) ?></td>
                        <td><?= htmlspecialchars($row['official_in_charge']) ?: '<span class="text-muted">N/A</span>' ?></td>
                        <td><?= htmlspecialchars($row['status']) ?></td>
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
