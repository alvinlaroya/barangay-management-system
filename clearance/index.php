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
    </style>
</head>
<body>

<?php include '../navbar.php'; ?>

<div class="container mt-5">
    <div class="card p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0"><i class="bi bi-file-earmark-text-fill text-primary"></i> Barangay Clearances</h4>
            <a href="add.php" class="btn btn-success btn-sm">
                <i class="bi bi-plus-circle"></i> New Clearance
            </a>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Resident</th>
                        <th>Purpose</th>
                        <th>Issued Date</th>
                        <th>Official</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $clearances->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['full_name']) ?></td>
                        <td><?= htmlspecialchars($row['purpose']) ?></td>
                        <td><?= date('M d, Y', strtotime($row['issued_date'])) ?></td>
                        <td><?= htmlspecialchars($row['official_in_charge']) ?: '<span class="text-muted">N/A</span>' ?></td>
                        <td>
                            <form method="POST" action="update_status.php" class="d-flex">
                                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                <select name="status" class="form-select form-select-sm me-2">
                                    <option <?= $row['status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                                    <option <?= $row['status'] === 'Ready for Pickup' ? 'selected' : '' ?>>Ready for Pickup</option>
                                    <option <?= $row['status'] === 'Claimed' ? 'selected' : '' ?>>Claimed</option>
                                </select>
                                <button class="btn btn-sm btn-primary" type="submit"><i class="bi bi-arrow-repeat"></i></button>
                            </form>
                        </td>
                        <td>
                            <a href="print.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-success" target="_blank">
                                <i class="bi bi-printer"></i> Print
                            </a>
                            <a href="delete.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this record?')">
                                <i class="bi bi-trash"></i> Delete
                            </a>
                        </td>
                    </tr>
                    <?php endwhile ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
