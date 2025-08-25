<?php
include '../config.php';

$household_id = $_GET['id'];
$household = $conn->query("SELECT * FROM households WHERE id = $household_id")->fetch_assoc();
$members = $conn->query("SELECT * FROM household_members WHERE household_id = $household_id");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Household Details</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 + Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', sans-serif;
        }

        .card {
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.06);
        }

        .card-header {
            background: linear-gradient(to right, #4b6cb7, #182848);
            color: white;
            border-radius: 12px 12px 0 0;
        }

        .card-header h5 {
            font-weight: 600;
        }

        .btn-sm {
            padding: 5px 10px;
            font-size: 14px;
        }

        .table th {
            background-color: #f1f3f5;
        }

        .table th, .table td {
            vertical-align: middle;
        }
    </style>
</head>
<body>

<?php include '../navbar.php'; ?>

<div class="container mt-5">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-house-door-fill me-2"></i>Household #<?= htmlspecialchars($household['household_no']) ?></h5>
            <div>
                <a href="add_member.php?household_id=<?= $household_id ?>" class="btn btn-light btn-sm me-2">
                    <i class="bi bi-person-plus-fill"></i> Add Member
                </a>
                <a href="index.php" class="btn btn-outline-light btn-sm">
                    <i class="bi bi-arrow-left-circle"></i> Back
                </a>
            </div>
        </div>
        <div class="card-body">
            <p class="mb-1"><strong>Head of Family:</strong> <?= htmlspecialchars($household['head_of_family']) ?></p>
            <p class="mb-3"><strong>Address:</strong> <?= htmlspecialchars($household['address']) ?> 
                | <strong>Purok:</strong> <?= htmlspecialchars($household['purok']) ?></p>

            <h6 class="mb-3 text-muted"><i class="bi bi-people-fill me-1"></i>Household Members</h6>
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Full Name</th>
                            <th>Birthdate</th>
                            <th>Gender</th>
                            <th>Relation</th>
                            <th>Occupation</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($members->num_rows > 0): ?>
                            <?php while ($member = $members->fetch_assoc()): ?>
                                <tr>
                                    <td><?= htmlspecialchars($member['full_name']) ?></td>
                                    <td><?= htmlspecialchars($member['birthdate']) ?></td>
                                    <td><span class="badge bg-<?= $member['gender'] === 'Male' ? 'primary' : 'warning' ?>"><?= $member['gender'] ?></span></td>
                                    <td><?= htmlspecialchars($member['relation_to_head']) ?></td>
                                    <td><?= htmlspecialchars($member['occupation']) ?></td>
                                    <td>
                                        <a href="delete_member.php?id=<?= $member['id'] ?>&household_id=<?= $household_id ?>" 
                                           class="btn btn-sm btn-outline-danger"
                                           onclick="return confirm('Are you sure you want to delete this member?')">
                                           <i class="bi bi-trash"></i> Delete
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted">No members found in this household.</td>
                            </tr>
                        <?php endif ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
