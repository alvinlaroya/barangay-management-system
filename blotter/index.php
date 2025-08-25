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
    </style>
</head>
<body>

<?php include '../navbar.php'; ?>

<div class="container mt-5">
    <div class="card p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0"><i class="bi bi-exclamation-triangle-fill text-danger"></i> Barangay Blotter Records</h4>
            <a href="add.php" class="btn btn-success btn-sm">
                <i class="bi bi-plus-circle"></i> Add Blotter
            </a>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Complainant</th>
                        <th>Respondent</th>
                        <th>Type</th>
                        <th>Incident Date</th>
                        <th>Status</th>
                        <th>Hearing Schedule</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($b = $blotters->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($b['complainant']) ?></td>
                        <td><?= htmlspecialchars($b['respondent']) ?></td>
                        <td><?= htmlspecialchars($b['incident_type']) ?></td>
                        <td><?= !empty($b['incident_datetime']) ? date('M d, Y h:i A', strtotime($b['incident_datetime'])) : '<span class="text-muted">N/A</span>' ?></td>
                        <td>
                            <span class="badge bg-<?= 
                                $b['status'] === 'Pending' ? 'warning' : 
                                ($b['status'] === 'Settled' ? 'success' : 
                                ($b['status'] === 'Dismissed' ? 'danger' : 'secondary')) ?>">
                                <?= $b['status'] ?>
                            </span>
                        </td>
                        <td>
                            <form method="POST" class="d-flex align-items-center gap-2">
                                <input type="hidden" name="blotter_id" value="<?= $b['id'] ?>">
                                <input type="datetime-local" name="schedule_datetime" class="form-control form-control-sm"
                                    value="<?= !empty($b['schedule_datetime']) ? date('Y-m-d\TH:i', strtotime($b['schedule_datetime'])) : '' ?>" required>
                                <button type="submit" class="btn btn-sm btn-primary">
                                    <i class="bi bi-calendar-check"></i> Set
                                </button>
                            </form>
                        </td>
                        <td>
                            <a href="edit.php?id=<?= $b['id'] ?>" class="btn btn-sm btn-outline-warning">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                            <a href="delete.php?id=<?= $b['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this blotter?')">
                                <i class="bi bi-trash"></i>
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
