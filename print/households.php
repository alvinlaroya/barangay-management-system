<?php include '../config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Households - Barangay System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', sans-serif;
        }
        .container {
            max-width: 1200px;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        }
        h4 {
            font-weight: 600;
        }
        .table th {
            background-color: #343a40;
            color: white;
        }
        .table td, .table th {
            vertical-align: middle;
        }
        .btn-sm {
            padding: 5px 10px;
            font-size: 14px;
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
            <h4 class="mb-0"><i class="bi bi-house-door-fill text-primary"></i> Household Records</h4>
            <button class="btn btn-success btn-sm" onclick="printTable()" type="button">
                <i class="bi bi-printer"></i> PRINT
            </button>
        </div>

        <div class="table-responsive print-area">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Household No</th>
                        <th>Head of Family</th>
                        <th>Purok</th>
                        <th>Address</th>
                        <th>Contact</th>
                        <th>Registered</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $result = $conn->query("SELECT * FROM households ORDER BY id DESC");
                    if ($result->num_rows > 0):
                        while ($row = $result->fetch_assoc()):
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($row['household_no']) ?></td>
                        <td><?= htmlspecialchars($row['head_of_family']) ?></td>
                        <td><?= htmlspecialchars($row['purok']) ?></td>
                        <td><?= htmlspecialchars($row['address']) ?></td>
                        <td><?= htmlspecialchars($row['contact_number']) ?></td>
                        <td><?= date('M d, Y', strtotime($row['date_registered'])) ?></td>
                    </tr>
                    <?php endwhile; else: ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted">No household records found.</td>
                    </tr>
                    <?php endif; ?>
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
