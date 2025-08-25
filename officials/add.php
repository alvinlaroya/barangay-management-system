<?php 
include '../config.php';

$error = ""; // feedback messages

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $_POST['full_name'];
    $position = $_POST['position'];
    $term_start = $_POST['term_start'];
    $term_end = $_POST['term_end'];
    $contact = $_POST['contact'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $status = $_POST['status'];

    // ✅ All barangay officials will have admin role
    $role = "admin";

    // Check if username already exists
    $check = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $check->bind_param("s", $username);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $error = "⚠️ Username already taken. Please choose another.";
    } else {
        // Insert into barangay_officials table
        $stmt1 = $conn->prepare("INSERT INTO barangay_officials (full_name, position, term_start, term_end, contact, status) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt1->bind_param("ssssss", $full_name, $position, $term_start, $term_end, $contact, $status);
        $stmt1->execute();
        $official_id = $stmt1->insert_id;

        // Insert into users table
        $stmt2 = $conn->prepare("INSERT INTO users (name, username, password, role, contact, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt2->bind_param("sssss", $full_name, $username, $password, $role, $contact);
        $stmt2->execute();

        header("Location: index.php");
        exit();
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Barangay Official</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .card {
            background: #ffffff;
            border-radius: 16px;
            padding: 30px;
            margin-top: 40px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        .form-label {
            font-weight: 500;
        }
        .btn-primary {
            background-color: #2575fc;
            border-radius: 10px;
            border: none;
        }
        .btn-primary:hover {
            background-color: #1a5fd6;
        }
        .btn-secondary {
            border-radius: 10px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card">
                <h4 class="mb-4 text-center">Add Barangay Official</h4>

                <!-- Show error if username already exists -->
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input name="full_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Position</label>
                        <input name="position" class="form-control" required>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <label class="form-label">Term Start</label>
                            <input type="date" name="term_start" class="form-control" required>
                        </div>
                        <div class="col">
                            <label class="form-label">Term End</label>
                            <input type="date" name="term_end" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Contact</label>
                        <input name="contact" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input name="username" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select" required>
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>
                    <div class="d-flex justify-content-between">
                        <a href="index.php" class="btn btn-secondary">Back</a>
                        <button type="submit" class="btn btn-primary">Save Official</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

</body>
</html>
