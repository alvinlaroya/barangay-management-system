<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'resident') {
    header("Location: login.php");
    exit();
}

include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $complainant = $_SESSION['name']; // Resident's name from session
    $respondent = trim($_POST['respondent']);
    $incident_type = trim($_POST['incident_type']);
    $incident_location = trim($_POST['incident_location']);
    $incident_datetime = $_POST['incident_datetime'];
    $description = trim($_POST['description']);

    $stmt = $conn->prepare("INSERT INTO blotters (complainant, respondent, incident_type, incident_location, incident_datetime, description, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("ssssss", $complainant, $respondent, $incident_type, $incident_location, $incident_datetime, $description);

    if ($stmt->execute()) {
        $success = true;
    } else {
        $error = "Something went wrong. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>File Blotter Complaint</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Bootstrap & Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <style>
        body {
            background-color: #f8f9fa;
        }
        .form-box {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.06);
        }
    </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container mt-5">
    <div class="form-box mx-auto" style="max-width: 650px;">
        <h4 class="mb-4"><i class="bi bi-exclamation-diamond-fill text-danger me-2"></i>File Blotter Complaint</h4>

        <?php if (isset($success)): ?>
            <div class="alert alert-success"><i class="bi bi-check-circle-fill me-2"></i>Complaint submitted successfully.</div>
        <?php elseif (isset($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" novalidate>
            <div class="mb-3">
                <label class="form-label">Respondent <span class="text-danger">*</span></label>
                <input type="text" name="respondent" class="form-control" required placeholder="Name of person you're reporting">
            </div>

            <div class="mb-3">
                <label class="form-label">Type of Incident <span class="text-danger">*</span></label>
                <input type="text" name="incident_type" class="form-control" required placeholder="e.g. Physical Assault, Theft">
            </div>

            <div class="mb-3">
                <label class="form-label">Location of Incident <span class="text-danger">*</span></label>
                <input type="text" name="incident_location" class="form-control" required placeholder="Exact location of the incident">
            </div>

            <div class="mb-3">
                <label class="form-label">Date and Time of Incident <span class="text-danger">*</span></label>
                <input type="datetime-local" name="incident_datetime" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Description <span class="text-danger">*</span></label>
                <textarea name="description" class="form-control" rows="4" required placeholder="Detailed description of the incident..."></textarea>
            </div>

            <div class="d-flex justify-content-between">
                <a href="resident_dashboard.php" class="btn btn-secondary"><i class="bi bi-arrow-left-circle"></i> Back</a>
                <button class="btn btn-danger"><i class="bi bi-flag-fill"></i> Submit Complaint</button>
            </div>
        </form>
    </div>
</div>

</body>
</html>
