<?php include '../config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Barangay Officials</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 & Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <style>
        body {
            background-color: #f7f9fc;
            font-family: 'Segoe UI', sans-serif;
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
        .badge-status {
            padding: 6px 12px;
            border-radius: 10px;
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
            <h4 class="mb-0"><i class="bi bi-person-badge-fill text-primary"></i> Barangay Officials</h4>
            <button class="btn btn-success btn-sm" onclick="printTable()" type="button">
                <i class="bi bi-printer"></i> PRINT
            </button>
        </div>

        <div class="table-responsive print-area">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Full Name</th>
                        <th>Position</th>
                        <th>Term</th>
                        <th>Contact</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $result = $conn->query("SELECT * FROM barangay_officials ORDER BY term_end DESC");
                while ($row = $result->fetch_assoc()):
                    $statusColor = $row['status'] === 'Active' ? 'success' : 'secondary';
                ?>
                <tr>
                    <td><?= htmlspecialchars($row['full_name']) ?></td>
                    <td><?= htmlspecialchars($row['position']) ?></td>
                    <td>
                        <?= date('M Y', strtotime($row['term_start'])) ?> 
                        – 
                        <?= date('M Y', strtotime($row['term_end'])) ?>
                    </td>
                    <td><?= htmlspecialchars($row['contact']) ?></td>
                    <td><?= htmlspecialchars($row['status']) ?></td>

                </tr>
                <?php endwhile; ?>
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
