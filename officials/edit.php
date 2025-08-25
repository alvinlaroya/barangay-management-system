<?php
include '../config.php';
$id = $_GET['id'];
$res = $conn->query("SELECT * FROM barangay_officials WHERE id = $id");
$official = $res->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    extract($_POST);
    $stmt = $conn->prepare("UPDATE barangay_officials SET full_name=?, position=?, term_start=?, term_end=?, contact=?, status=? WHERE id=?");
    $stmt->bind_param("ssssssi", $full_name, $position, $term_start, $term_end, $contact, $status, $id);
    $stmt->execute();
    header("Location: index.php");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Official</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
            font-family: 'Segoe UI', sans-serif;
        }
        .card {
            background: #fff;
            border-radius: 16px;
            padding: 30px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            margin-top: 60px;
        }
        h3 {
            font-weight: 600;
            color: #333;
        }
        .form-label {
            font-weight: 500;
            color: #555;
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
            <div class="card">
                <h3 class="mb-4 text-center">Edit Barangay Official</h3>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input name="full_name" value="<?= $official['full_name'] ?>" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Position</label>
                        <input name="position" value="<?= $official['position'] ?>" class="form-control" required>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <label class="form-label">Term Start</label>
                            <input type="date" name="term_start" value="<?= $official['term_start'] ?>" class="form-control">
                        </div>
                        <div class="col">
                            <label class="form-label">Term End</label>
                            <input type="date" name="term_end" value="<?= $official['term_end'] ?>" class="form-control">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Contact Number</label>
                        <input name="contact" value="<?= $official['contact'] ?>" class="form-control">
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option <?= $official['status'] == 'Active' ? 'selected' : '' ?>>Active</option>
                            <option <?= $official['status'] == 'Inactive' ? 'selected' : '' ?>>Inactive</option>
                        </select>
                    </div>
                    <div class="d-flex justify-content-between">
                        <a href="index.php" class="btn btn-secondary">Back</a>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

</body>
</html>
