<?php
include '../config.php';
$id = $_GET['id'];
$data = $conn->query("SELECT * FROM blotters WHERE id = $id")->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    extract($_POST);
    $stmt = $conn->prepare("UPDATE blotters SET complainant=?, respondent=?, incident_type=?, incident_location=?, incident_datetime=?, description=?, status=? WHERE id=?");
    $stmt->bind_param("sssssssi", $complainant, $respondent, $incident_type, $incident_location, $incident_datetime, $description, $status, $id);
    $stmt->execute();
    header("Location: index.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Blotter Record</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8f9fc;
            font-family: 'Segoe UI', sans-serif;
        }
        .card {
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        }
        .form-label {
            font-weight: 500;
            color: #2c3e50;
        }
        .page-title {
            font-weight: 600;
            color: #0d6efd;
        }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card p-4">
                <h3 class="page-title mb-4">
                    <i class="bi bi-pencil-square me-2"></i>Edit Blotter Record
                </h3>

                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Complainant</label>
                        <input name="complainant" value="<?= htmlspecialchars($data['complainant']) ?>" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Respondent</label>
                        <input name="respondent" value="<?= htmlspecialchars($data['respondent']) ?>" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Incident Type</label>
                        <input name="incident_type" value="<?= htmlspecialchars($data['incident_type']) ?>" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Incident Location</label>
                        <input name="incident_location" value="<?= htmlspecialchars($data['incident_location']) ?>" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Incident Date & Time</label>
                        <input type="datetime-local" name="incident_datetime" value="<?= date('Y-m-d\TH:i', strtotime($data['incident_datetime'])) ?>" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($data['description']) ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option <?= $data['status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
                            <option <?= $data['status'] == 'Settled' ? 'selected' : '' ?>>Settled</option>
                            <option <?= $data['status'] == 'Dismissed' ? 'selected' : '' ?>>Dismissed</option>
                        </select>
                    </div>

                    <div class="d-flex justify-content-end">
                        <a href="index.php" class="btn btn-secondary me-2">
                            <i class="bi bi-arrow-left-circle me-1"></i> Cancel
                        </a>
                        <button class="btn btn-success">
                            <i class="bi bi-save me-1"></i> Update
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

</body>
</html>
