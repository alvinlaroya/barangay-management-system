<?php
session_start();
include '../config.php';
if (!isset($_SESSION['user_id'])) header("Location: ../login.php");
$result = $conn->query("SELECT * FROM residents ORDER BY last_name ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Residents</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Bootstrap 5 CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        body {
            background-color: #f8f9fa;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        }
        .table th {
            background-color: #343a40;
            color: white;
        }
        .table td, .table th {
            vertical-align: middle;
        }
        .btn-sm {
            padding: 4px 10px;
        }
    </style>
</head>
<body>
    
<?php
include '../navbar.php';
?>

<div class="container mt-5">
    <div class="card p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0"><i class="bi bi-people-fill text-primary"></i> Resident List</h4>
            <a href="add.php" class="btn btn-success btn-sm">
                <i class="bi bi-plus-circle"></i> Add Resident
            </a>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Full Name</th>
                        <th>Gender</th>
                        <th>Birthdate</th>
                        <th>Purok</th>
                        <th>Contact</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <?= htmlspecialchars("{$row['last_name']}, {$row['first_name']} {$row['middle_name']} {$row['suffix']}") ?>
                        </td>
                        <td><span class="badge bg-<?= $row['gender'] === 'Male' ? 'primary' : 'warning' ?>"><?= $row['gender'] ?></span></td>
                        <td><?= htmlspecialchars($row['birthdate']) ?></td>
                        <td><?= htmlspecialchars($row['purok']) ?></td>
                        <td><?= htmlspecialchars($row['contact']) ?></td>
                        <td>
                            <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-pencil-square"></i> Edit
                            </a>
                            <a href="delete.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this resident?')">
                                <i class="bi bi-trash"></i> Delete
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
