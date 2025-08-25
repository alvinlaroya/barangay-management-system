<?php
include '../config.php';
$household_id = $_GET['household_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    extract($_POST);
    $stmt = $conn->prepare("INSERT INTO household_members (household_id, full_name, birthdate, gender, relation_to_head, occupation) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssss", $household_id, $full_name, $birthdate, $gender, $relation, $occupation);
    $stmt->execute();
    header("Location: view.php?id=$household_id");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Household Member</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
            font-family: 'Segoe UI', sans-serif;
        }
        .form-card {
            background: #ffffff;
            border-radius: 16px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            margin-top: 50px;
        }
        .form-title {
            font-weight: 600;
            margin-bottom: 25px;
            text-align: center;
            color: #333;
        }
        .btn-primary {
            background-color: #2575fc;
            border: none;
            border-radius: 10px;
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
            <div class="form-card">
                <h4 class="form-title">Add Member to Household #<?= htmlspecialchars($household_id) ?></h4>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input name="full_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Birthdate</label>
                        <input type="date" name="birthdate" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Gender</label>
                        <select name="gender" class="form-control" required>
                            <option value="">Select Gender</option>
                            <option>Male</option>
                            <option>Female</option>
                            <option>Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Relation to Head</label>
                        <input name="relation" class="form-control" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Occupation</label>
                        <input name="occupation" class="form-control">
                    </div>
                    <div class="d-flex justify-content-between">
                        <a href="view.php?id=<?= $household_id ?>" class="btn btn-secondary">Back</a>
                        <button type="submit" class="btn btn-primary">Add Member</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

</body>
</html>
