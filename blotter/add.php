<?php
include '../config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    extract($_POST);
    $stmt = $conn->prepare("INSERT INTO blotters (complainant, respondent, incident_type, incident_location, incident_datetime, description, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $complainant, $respondent, $incident_type, $incident_location, $incident_datetime, $description, $status);
    $stmt->execute();
    header("Location: index.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Blotter Report</title>
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

        .btn-success, .btn-secondary {
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
                    <h4 class="mb-0 text-primary"><i class="bi bi-journal-plus me-2"></i>Add Blotter Record</h4>
                    <a href="index.php" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Back</a>
                </div>

                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Complainant</label>
                        <input name="complainant" class="form-control" placeholder="Enter complainant name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Respondent</label>
                        <input name="respondent" class="form-control" placeholder="Enter respondent name">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Type of Incident</label>
                        <input name="incident_type" class="form-control" placeholder="e.g., Physical Assault, Theft" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Location of Incident</label>
                        <input name="incident_location" class="form-control" placeholder="Where the incident occurred" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Date & Time of Incident</label>
                        <input type="datetime-local" name="incident_datetime" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Incident Description</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Brief details of what happened..." required></textarea>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select" required>
                            <option>Pending</option>
                            <option>Settled</option>
                            <option>Dismissed</option>
                        </select>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <button type="submit" class="btn btn-success px-4">
                            <i class="bi bi-save me-1"></i> Save
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
