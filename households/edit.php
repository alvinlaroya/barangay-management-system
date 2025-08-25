<?php
include '../config.php';
$id = $_GET['id'];
$data = $conn->query("SELECT * FROM households WHERE id = $id")->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    extract($_POST);
    $stmt = $conn->prepare("UPDATE households SET household_no=?, purok=?, address=?, head_of_family=?, contact_number=? WHERE id=?");
    $stmt->bind_param("sssssi", $household_no, $purok, $address, $head_of_family, $contact_number, $id);
    $stmt->execute();
    header("Location: index.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Household</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 + Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background-color: #f7f9fc;
            font-family: 'Segoe UI', sans-serif;
        }
        .card {
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
            border: none;
        }
        .card-title {
            font-weight: 600;
            color: #34495e;
        }
        .form-label {
            font-weight: 500;
            color: #555;
        }
        .btn-success {
            background-color: #28a745;
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
                    <h4 class="card-title mb-0">
                        <i class="bi bi-house-gear-fill me-2 text-primary"></i>
                        Edit Household #<?= htmlspecialchars($data['household_no']) ?>
                    </h4>
                    <a href="index.php" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Back
                    </a>
                </div>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Household Number</label>
                        <input type="text" name="household_no" value="<?= htmlspecialchars($data['household_no']) ?>" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Purok</label>
                        <input type="text" name="purok" value="<?= htmlspecialchars($data['purok']) ?>" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <textarea name="address" class="form-control" rows="2" required><?= htmlspecialchars($data['address']) ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Head of Family</label>
                        <input type="text" name="head_of_family" value="<?= htmlspecialchars($data['head_of_family']) ?>" class="form-control" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Contact Number</label>
                        <input type="text" name="contact_number" value="<?= htmlspecialchars($data['contact_number']) ?>" class="form-control">
                    </div>
                    <div class="d-flex justify-content-end gap-2">
                        <button type="submit" class="btn btn-success px-4">
                            <i class="bi bi-save me-1"></i> Update
                        </button>
                        <a href="index.php" class="btn btn-secondary px-4">
                            Cancel
                        </a>
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
