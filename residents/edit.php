<?php
include '../config.php';
$id = $_GET['id'];
$result = $conn->query("SELECT * FROM residents WHERE id = $id");
$resident = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    extract($_POST);
    $stmt = $conn->prepare("UPDATE residents SET first_name=?, middle_name=?, last_name=?, suffix=?, gender=?, birthdate=?, age=?, civil_status=?, citizenship=?, religion=?, occupation=?, purok=?, voter_status=?, is_4ps=?, contact=?, email=?, address=? WHERE id=?");
    
    // Fixed: Added type definition string as first parameter
    // 's' = string, 'i' = integer
    $stmt->bind_param("ssssssissssssssssi", $first_name, $middle_name, $last_name, $suffix, $gender, $birthdate, $age, $civil_status, $citizenship, $religion, $occupation, $purok, $voter_status, $is_4ps, $contact, $email, $address, $id);
    
    $stmt->execute();
    header("Location: index.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Resident</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Bootstrap 5 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background-color: #f8f9fa;
        }
        .card {
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.06);
        }
        .form-label {
            font-weight: 500;
        }
        h4 {
            font-weight: 600;
        }
    </style>
</head>
<body>

<?php include '../navbar.php'; ?>

<div class="container mt-5">
    <div class="card p-4">
        <h4 class="mb-4"><i class="bi bi-pencil-square text-primary"></i> Edit Resident</h4>

        <form method="POST">
            <!-- Full Name -->
            <div class="row mb-3">
                <div class="col-md-3">
                    <label class="form-label">First Name</label>
                    <input name="first_name" value="<?= $resident['first_name'] ?>" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Middle Name</label>
                    <input name="middle_name" value="<?= $resident['middle_name'] ?>" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Last Name</label>
                    <input name="last_name" value="<?= $resident['last_name'] ?>" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Suffix</label>
                    <input name="suffix" value="<?= $resident['suffix'] ?>" class="form-control">
                </div>
            </div>

            <!-- Demographics -->
            <div class="row mb-3">
                <div class="col-md-3">
                    <label class="form-label">Gender</label>
                    <select name="gender" class="form-select">
                        <option <?= $resident['gender'] == 'Male' ? 'selected' : '' ?>>Male</option>
                        <option <?= $resident['gender'] == 'Female' ? 'selected' : '' ?>>Female</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Birthdate</label>
                    <input type="date" name="birthdate" value="<?= $resident['birthdate'] ?>" class="form-control" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Age</label>
                    <input type="number" name="age" value="<?= $resident['age'] ?>" class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Civil Status</label>
                    <input name="civil_status" value="<?= $resident['civil_status'] ?>" class="form-control">
                </div>
            </div>

            <!-- Background -->
            <div class="row mb-3">
                <div class="col-md-3">
                    <label class="form-label">Citizenship</label>
                    <input name="citizenship" value="<?= $resident['citizenship'] ?>" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Religion</label>
                    <input name="religion" value="<?= $resident['religion'] ?>" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Occupation</label>
                    <input name="occupation" value="<?= $resident['occupation'] ?>" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Purok</label>
                    <input name="purok" value="<?= $resident['purok'] ?>" class="form-control">
                </div>
            </div>

            <!-- Additional -->
            <div class="row mb-3">
                <div class="col-md-3">
                    <label class="form-label">Voter Status</label>
                    <select name="voter_status" class="form-select">
                        <option <?= $resident['voter_status'] == 'Yes' ? 'selected' : '' ?>>Yes</option>
                        <option <?= $resident['voter_status'] == 'No' ? 'selected' : '' ?>>No</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">4Ps Member?</label>
                    <select name="is_4ps" class="form-select">
                        <option <?= $resident['is_4ps'] == 'Yes' ? 'selected' : '' ?>>Yes</option>
                        <option <?= $resident['is_4ps'] == 'No' ? 'selected' : '' ?>>No</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Contact</label>
                    <input name="contact" value="<?= $resident['contact'] ?>" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Email</label>
                    <input name="email" type="email" value="<?= $resident['email'] ?>" class="form-control">
                </div>
            </div>

            <!-- Address -->
            <div class="mb-3">
                <label class="form-label">Address</label>
                <textarea name="address" class="form-control" rows="2"><?= $resident['address'] ?></textarea>
            </div>

            <!-- Buttons -->
            <div class="d-flex justify-content-end">
                <button class="btn btn-primary me-2"><i class="bi bi-save"></i> Update</button>
                <a href="index.php" class="btn btn-secondary"><i class="bi bi-x-circle"></i> Back</a>
            </div>
        </form>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>