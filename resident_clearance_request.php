<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'resident') {
    header("Location: login.php");
    exit();
}

include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $resident_id = $_SESSION['resident_id'];
    $purpose = trim($_POST['purpose']);
    $remarks = trim($_POST['remarks']);

    $stmt = $conn->prepare("INSERT INTO clearances (resident_id, purpose, remarks, official_in_charge, issued_date) VALUES (?, ?, ?, '', NOW())");
    $stmt->bind_param("iss", $resident_id, $purpose, $remarks);

    if ($stmt->execute()) {
        $success = true;
    } else {
        $error = "Failed to submit request. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Request Barangay Clearance</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Bootstrap & Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', sans-serif;
        }

        .form-container {
            background-color: white;
            border-radius: 10px;
            padding: 2rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }

        .section-title {
            font-weight: 600;
            margin-bottom: 1.5rem;
            color: #343a40;
        }
    </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container mt-5">
    <div class="form-container mx-auto" style="max-width: 600px;">
        <h4 class="section-title"><i class="bi bi-file-earmark-text me-2"></i>Barangay Clearance Request</h4>

        <?php if (isset($success)): ?>
            <div class="alert alert-success"><i class="bi bi-check-circle-fill me-2"></i>Your clearance request has been submitted successfully.</div>
        <?php elseif (isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST" novalidate>
            <div class="mb-3">
                <label class="form-label">Purpose <span class="text-danger">*</span></label>
                <input name="purpose" class="form-control" placeholder="e.g., Employment, ID, Business" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Remarks (Optional)</label>
                <textarea name="remarks" class="form-control" rows="3" placeholder="Any remarks or additional information..."></textarea>
            </div>
            <div class="d-flex justify-content-between">
                <a href="resident_dashboard.php" class="btn btn-secondary"><i class="bi bi-arrow-left-circle"></i> Back</a>
                <button class="btn btn-success"><i class="bi bi-send-check-fill"></i> Submit Request</button>
            </div>
        </form>
    </div>
</div>

</body>
</html>
