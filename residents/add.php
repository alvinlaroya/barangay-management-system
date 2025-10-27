<?php
include '../config.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    extract($_POST);
    $stmt = $conn->prepare("INSERT INTO residents (first_name, middle_name, last_name, suffix, gender, birthdate, age, civil_status, citizenship, religion, occupation, purok, voter_status, is_4ps, contact, email, address) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssissssssssss", $first_name, $middle_name, $last_name, $suffix, $gender, $birthdate, $age, $civil_status, $citizenship, $religion, $occupation, $purok, $voter_status, $is_4ps, $contact, $email, $address);
    $stmt->execute();
    header("Location: index.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Resident</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }
        h4 {
            font-weight: 600;
            color: #333;
        }
        .form-label {
            font-weight: 500;
        }
    </style>
</head>
<body>

<?php include '../navbar.php'; ?>

<div class="container mt-5">
    <div class="card p-4">
        <h4 class="mb-3"><i class="bi bi-person-plus-fill text-primary"></i> Add New Resident</h4>

        <form method="POST">
            <!-- Name -->
            <div class="row mb-3">
                <div class="col-md-3">
                    <label class="form-label">First Name</label>
                    <input name="first_name" class="form-control" pattern="[a-zA-Z ]{2,25}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Middle Name</label>
                    <input name="middle_name" class="form-control" pattern="[a-zA-Z ]{2,25}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Last Name</label>
                    <input name="last_name" class="form-control" pattern="[a-zA-Z ]{2,25}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Suffix</label>
                    <input name="suffix" class="form-control">
                </div>
            </div>

            <!-- Personal Info -->
            <div class="row mb-3">
                <div class="col-md-3">
                    <label class="form-label">Gender</label>
                    <select name="gender" class="form-select">
                        <option>Male</option>
                        <option>Female</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Birthdate</label>
                    <input type="date" name="birthdate" class="form-control" max="<?php echo date('Y-m-d', strtotime('-1 day')); ?>" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Age</label>
                    <input type="number" name="age" class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Civil Status</label>
                    <!-- <input name="civil_status" class="form-control"> -->
                    <select name="civil_status" class="form-select id="">
                        <option value="Single">Single</option>
                        <option value="Married">Married</option>
                    </select>
                </div>
            </div>

            <!-- Background Info -->
            <div class="row mb-3">
                <div class="col-md-3">
                    <label class="form-label">Citizenship</label>
                    <input name="citizenship" class="form-control" pattern="[a-zA-Z ]{2,25}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Religion</label>
                    <!-- <input name="religion" class="form-control"> -->
                     <select name="religion" class="form-select id="">
                        <option value="Roman Catholic">Roman Catholic</option>
                        <option value="Protestant">Protestant</option>
                        <option value="Iglesia ni Cristo">Iglesia ni Cristo</option>
                        <option value="Philippine Independent Church">Philippine Independent Church (Aglipayan)</option>
                        <option value="Seventh-day Adventist">Seventh-day Adventist</option>
                        <option value="Baptist">Baptist</option>
                        <option value="Methodist">Methodist</option>
                        <option value="Pentecostal">Pentecostal</option>
                        <option value="Jehovah's Witnesses">Jehovah's Witnesses</option>
                        <option value="United Church of Christ">United Church of Christ in the Philippines</option>
                        <option value="Jesus is Lord Church">Jesus is Lord Church</option>
                        <option value="Victory Christian Fellowship">Victory Christian Fellowship</option>
                        <option value="Christ's Commission Fellowship">Christ's Commission Fellowship</option>
                        <option value="Jesus Miracle Crusade">Jesus Miracle Crusade</option>
                        <option value="Kingdom of Jesus Christ">Kingdom of Jesus Christ</option>
                        <option value="Members Church of God International">Members Church of God International (Ang Dating Daan)</option>
                        <option value="Other Christian">Other Christian</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Occupation</label>
                    <input name="occupation" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Purok</label>
                    <input name="purok" class="form-control">
                </div>
            </div>

            <!-- Other Details -->
            <div class="row mb-3">
                <div class="col-md-3">
                    <label class="form-label">Voter Status</label>
                    <select name="voter_status" class="form-select">
                        <option>Yes</option>
                        <option>No</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">4Ps Member?</label>
                    <select name="is_4ps" class="form-select">
                        <option>Yes</option>
                        <option>No</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Contact Number</label>
                    <input name="contact" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control">
                </div>
            </div>

            <!-- Address -->
            <div class="mb-3">
                <label class="form-label">Full Address</label>
                <textarea name="address" class="form-control" rows="2"></textarea>
            </div>

            <!-- Buttons -->
            <div class="d-flex justify-content-end">
                <button class="btn btn-success me-2"><i class="bi bi-save"></i> Save</button>
                <a href="index.php" class="btn btn-secondary"><i class="bi bi-x-circle"></i> Cancel</a>
            </div>
        </form>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
