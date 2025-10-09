<?php
session_start();
include 'config.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = $_POST['fullname'] ?? '';
    $address = $_POST['address'] ?? '';
    $contact = $_POST['contact'] ?? '';
    $email = $_POST['email'] ?? '';
    $birth_date = $_POST['birth_date'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $civil_status = $_POST['civil_status'] ?? '';
    $occupation = $_POST['occupation'] ?? '';
    $number_of_dependents = $_POST['number_of_dependents'] ?? 0;
    $emergency_type = $_POST['emergency_type'] ?? '';
    $emergency_description = $_POST['emergency_description'] ?? '';
    $requested_at = date('Y-m-d H:i:s');
    $user_id = $_SESSION['user_id'];

    // Handle file uploads
    $supporting_documents = null;
    $proof_of_damage = null;
    if (isset($_FILES['supporting_documents']) && $_FILES['supporting_documents']['error'] === UPLOAD_ERR_OK) {
        $supporting_documents = file_get_contents($_FILES['supporting_documents']['tmp_name']);
    }
    if (isset($_FILES['proof_of_damage']) && $_FILES['proof_of_damage']['error'] === UPLOAD_ERR_OK) {
        $proof_of_damage = file_get_contents($_FILES['proof_of_damage']['tmp_name']);
    }

    $stmt = $conn->prepare("INSERT INTO assistance_requests (user_id, fullname, address, contact, email, birth_date, gender, civil_status, occupation, number_of_dependents, emergency_type, emergency_description, requested_at, supporting_documents, proof_of_damage) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssssssisssbb", $user_id, $fullname, $address, $contact, $email, $birth_date, $gender, $civil_status, $occupation, $number_of_dependents, $emergency_type, $emergency_description, $requested_at, $supporting_documents, $proof_of_damage);
    if ($supporting_documents !== null) {
        $stmt->send_long_data(13, $supporting_documents);
    }
    if ($proof_of_damage !== null) {
        $stmt->send_long_data(14, $proof_of_damage);
    }
    if ($stmt->execute()) {
        $success = "Emergency assistance request submitted successfully.";
    } else {
        $error = "Failed to submit request.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Assistance Request</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }
        .container { max-width: 700px; margin-top: 40px; }
        .card { border-radius: 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
        .form-label { font-weight: 500; }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container">
    <div class="card p-4">
        <h4 class="mb-4 text-primary"><i class="bi bi-exclamation-triangle"></i> Assistance Request</h4>
        <?php if (isset($success)): ?>
            <div class="alert alert-success"> <?= htmlspecialchars($success) ?> </div>
        <?php elseif (isset($error)): ?>
            <div class="alert alert-danger"> <?= htmlspecialchars($error) ?> </div>
        <?php endif; ?>
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">Full Name</label>
                <input type="text" name="fullname" class="form-control" value="<?= htmlspecialchars($_SESSION['name'] ?? '') ?>" readonly required>
            </div>
            <div class="mb-3">
                <label class="form-label">Address <?php echo $_SESSION['address']; ?></label>
                <input type="text" name="address" class="form-control" value="<?= htmlspecialchars($_SESSION['address'] ?? '') ?>" readonly required>
            </div>
            <div class="mb-3">
                <label class="form-label">Contact</label>
                <input type="text" name="contact" class="form-control" value="<?= htmlspecialchars($_SESSION['contact'] ?? '') ?>" readonly required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($_SESSION['email'] ?? '') ?>" readonly required>
            </div>
            <div class="mb-3">
                <label class="form-label">Birth Date</label>
                <input type="date" name="birth_date" class="form-control" value="<?= htmlspecialchars($_SESSION['birthdate'] ?? '') ?>" readonly required>
            </div>
            <div class="mb-3">
                <label class="form-label">Gender</label>
                <select name="gender" class="form-select" disabled required>
                    <option value="">Select Gender</option>
                    <option value="Male" <?= (($_SESSION['gender'] ?? '') == 'Male') ? 'selected' : '' ?>>Male</option>
                    <option value="Female" <?= (($_SESSION['gender'] ?? '') == 'Female') ? 'selected' : '' ?>>Female</option>
                </select>
                <input type="hidden" name="gender" value="<?= htmlspecialchars($_SESSION['gender'] ?? '') ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Civil Status</label>
                <select name="civil_status" class="form-select" disabled required>
                    <option value="">Select Status</option>
                    <option value="Single" <?= (($_SESSION['civil_status'] ?? '') == 'Single') ? 'selected' : '' ?>>Single</option>
                    <option value="Married" <?= (($_SESSION['civil_status'] ?? '') == 'Married') ? 'selected' : '' ?>>Married</option>
                </select>
                <input type="hidden" name="civil_status" value="<?= htmlspecialchars($_SESSION['civil_status'] ?? '') ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Occupation</label>
                <input type="text" name="occupation" class="form-control" value="<?= htmlspecialchars($_SESSION['occupation'] ?? '') ?>" readonly required>
            </div>
            <div class="mb-3">
                <label class="form-label">Number of Dependents</label>
                <input type="number" name="number_of_dependents" class="form-control" min="0" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Emergency Type</label>
                <select name="emergency_type" class="form-select" required>
                    <option value="">Select Type</option>
                    <option value="Medical">Medical</option>
                    <option value="Fire">Fire</option>
                    <option value="Natural Disaster">Natural Disaster</option>
                    <option value="Financial Assistance">Financial Assistance</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Emergency Description</label>
                <textarea name="emergency_description" class="form-control" rows="4" required></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Supporting Documents (PDF, DOC, etc.)</label>
                <input type="file" name="supporting_documents" class="form-control" accept=".pdf,.doc,.docx,.txt,.xls,.xlsx">
            </div>
            <div class="mb-3">
                <label class="form-label">Proof of Damage (Image)</label>
                <input type="file" name="proof_of_damage" class="form-control" accept="image/*">
            </div>
            <button type="submit" class="btn btn-primary w-100">Submit Request</button>
        </form>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
