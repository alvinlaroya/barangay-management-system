<?php
include 'config.php';
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form inputs
    $first_name   = trim($_POST['first_name']);
    $middle_name  = trim($_POST['middle_name']);
    $last_name    = trim($_POST['last_name']);
    $suffix       = trim($_POST['suffix']);
    $gender       = $_POST['gender'];
    $birthdate    = $_POST['birthdate'];
    $age          = $_POST['age'];
    $civil_status = trim($_POST['civil_status']);
    $citizenship  = trim($_POST['citizenship']);
    $religion     = trim($_POST['religion']);
    $occupation   = trim($_POST['occupation']);
    $purok        = trim($_POST['purok']);
    $voter_status = $_POST['voter_status'];
    $is_4ps       = $_POST['is_4ps'];
    $contact      = trim($_POST['contact']);
    $email        = trim($_POST['email']);
    $address      = trim($_POST['address']);

    // For login account
    $username  = trim($_POST['username']);
    $password  = sha1($_POST['password']); // NOTE: replace with password_hash() in production
    $full_name = $first_name . ' ' . $last_name;
    $role      = 'resident';

    // Check if username already exists
    $check = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $check->bind_param("s", $username);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $error = "Username already taken.";
    } else {
        // Insert into residents table
        $res_stmt = $conn->prepare("INSERT INTO residents 
            (first_name, middle_name, last_name, suffix, gender, birthdate, age, civil_status, citizenship, religion, occupation, purok, voter_status, is_4ps, contact, email, address) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $res_stmt->bind_param("ssssssissssssssss", 
            $first_name, $middle_name, $last_name, $suffix, $gender, $birthdate, $age, $civil_status,
            $citizenship, $religion, $occupation, $purok, $voter_status, $is_4ps, $contact, $email, $address);

        if ($res_stmt->execute()) {
            $resident_id = $conn->insert_id;

            // Insert into users table
            $user_stmt = $conn->prepare("INSERT INTO users (name, username, password, role, resident_id, contact, created_at) 
                                         VALUES (?, ?, ?, ?, ?, ?, NOW())");
            $user_stmt->bind_param("ssssis", $full_name, $username, $password, $role, $resident_id, $contact);

            if ($user_stmt->execute()) {
                $success = "Account created successfully! You may now login.";
            } else {
                $error = "Failed to create user account. (" . $user_stmt->error . ")";
            }
        } else {
            $error = "Failed to save resident information. (" . $res_stmt->error . ")";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Resident Registration - Barangay System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="bg-light p-4">
<div class="container">
    <div class="card shadow p-4 mx-auto" style="max-width: 850px;">
        <div class="text-center mb-3">
            <!-- Change 'logo.png' to your actual logo file -->
            <img src="assets/logo.png" alt="Barangay Logo" style="width: 80px; height: 80px; object-fit: contain;">
            <h4 class="mt-2">Resident Registration</h4>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php elseif ($success): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="row mb-2">
                <div class="col"><input type="text" name="first_name" class="form-control" placeholder="First Name" required></div>
                <div class="col"><input type="text" name="middle_name" class="form-control" placeholder="Middle Name"></div>
                <div class="col"><input type="text" name="last_name" class="form-control" placeholder="Last Name" required></div>
                <div class="col-2"><input type="text" name="suffix" class="form-control" placeholder="Suffix"></div>
            </div>
            <div class="row mb-2">
                <div class="col-md-3">
                    <select name="gender" class="form-control" required>
                        <option value="">Gender</option>
                        <option>Male</option>
                        <option>Female</option>
                    </select>
                </div>
                <div class="col-md-3"><input type="date" name="birthdate" class="form-control" required></div>
                <div class="col-md-2"><input type="number" name="age" class="form-control" placeholder="Age" required></div>
                <div class="col-md-4"><input type="text" name="civil_status" class="form-control" placeholder="Civil Status"></div>
            </div>
            <div class="row mb-2">
                <div class="col-md-4"><input type="text" name="citizenship" class="form-control" placeholder="Citizenship"></div>
                <div class="col-md-4"><input type="text" name="religion" class="form-control" placeholder="Religion"></div>
                <div class="col-md-4"><input type="text" name="occupation" class="form-control" placeholder="Occupation"></div>
            </div>
            <div class="row mb-2">
                <div class="col-md-4"><input type="text" name="purok" class="form-control" placeholder="Purok"></div>
                <div class="col-md-4">
                    <select name="voter_status" class="form-control">
                        <option value="">Voter?</option>
                        <option>Yes</option>
                        <option>No</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <select name="is_4ps" class="form-control">
                        <option value="">4Ps Member?</option>
                        <option>Yes</option>
                        <option>No</option>
                    </select>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-6"><input type="text" name="contact" class="form-control" placeholder="Contact Number"></div>
                <div class="col-md-6"><input type="email" name="email" class="form-control" placeholder="Email Address"></div>
            </div>
            <div class="mb-3">
                <textarea name="address" class="form-control" placeholder="Full Address" required></textarea>
            </div>

            <h5 class="mt-4">Login Information</h5>
            <div class="row mb-3">
                <div class="col"><input type="text" name="username" class="form-control" placeholder="Username" required></div>
                <div class="col"><input type="password" name="password" class="form-control" placeholder="Password" required></div>
            </div>

            <button class="btn btn-primary w-100">Register</button>
            <a href="login.php" class="btn btn-link d-block mt-2 text-center">Already have an account? Login</a>
        </form>
    </div>
</div>
</body>
</html>
