<?php
include '../config.php';

$residents = $conn->query("SELECT * FROM residents");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    extract($_POST);
    $stmt = $conn->prepare("INSERT INTO clearances (resident_id, purpose, official_in_charge, remarks) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $resident_id, $purpose, $official_in_charge, $remarks);
    $stmt->execute();
    header("Location: index.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>New Barangay Clearance</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Bootstrap 5 + Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background-color: #f8f9fc;
            font-family: 'Segoe UI', sans-serif;
        }

        .card {
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.08);
        }

        .form-label {
            font-weight: 500;
        }

        .btn-success {
            border-radius: 8px;
        }

        .btn-secondary {
            border-radius: 8px;
        }
    </style>
</head>
<body>

<?php include '../navbar.php'; ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="mb-0 text-primary"><i class="bi bi-file-earmark-text me-2"></i>Issue New Barangay Clearance</h4>
                    <a href="index.php" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Back</a>
                </div>

                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Resident</label>
                        <select name="resident_id" class="form-select" required>
                            <option disabled selected>Select Resident</option>
                            <?php while ($r = $residents->fetch_assoc()): ?>
                                <option value="<?= $r['id'] ?>">
                                    <?= htmlspecialchars($r['last_name'] . ', ' . $r['first_name'] . ' ' . $r['middle_name']) ?>
                                </option>
                            <?php endwhile ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Purpose</label>
                        <input name="purpose" class="form-control" placeholder="e.g., Employment, School Requirements" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Barangay Official In-Charge</label>
                        <input name="official_in_charge" class="form-control" placeholder="Enter official's name" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Remarks (optional)</label>
                        <textarea name="remarks" class="form-control" rows="2" placeholder="Any additional notes..."></textarea>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <button type="submit" class="btn btn-success px-4">
                            <i class="bi bi-printer me-1"></i> Generate Clearance
                        </button>
                        <a href="index.php" class="btn btn-secondary px-4">Cancel</a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
