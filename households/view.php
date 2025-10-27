<?php
include '../config.php';

$household_id = intval($_GET['id']);

// Fetch household details using prepared statement
$household_stmt = $conn->prepare("SELECT * FROM households WHERE id = ?");
$household_stmt->bind_param("i", $household_id);
$household_stmt->execute();
$household_result = $household_stmt->get_result();
$household = $household_result->fetch_assoc();

if (!$household) {
    die("Household not found.");
}

// Fetch household members with resident details using prepared statement
$members_stmt = $conn->prepare("
    SELECT hm.id, 
           hm.household_id,
           hm.resident_id,
           hm.full_name, 
           hm.birthdate, 
           hm.gender, 
           hm.relation_to_head, 
           hm.occupation,
           r.first_name as resident_first_name, 
           r.middle_name as resident_middle_name, 
           r.last_name as resident_last_name,
           r.contact as resident_contact,
           r.email as resident_email
    FROM household_members hm 
    LEFT JOIN residents r ON hm.resident_id = r.id 
    WHERE hm.household_id = ? 
    ORDER BY 
        CASE hm.relation_to_head 
            WHEN 'Head' THEN 1 
            WHEN 'Spouse' THEN 2 
            ELSE 3 
        END, 
        hm.full_name
");
$members_stmt->bind_param("i", $household_id);
$members_stmt->execute();
$members_result = $members_stmt->get_result();

// Convert to array for easier handling
$members_array = [];
while ($row = $members_result->fetch_assoc()) {
    $members_array[] = $row;
}

// Debug information
$debug_info = [
    'household_id' => $household_id,
    'total_members' => count($members_array),
    'query_error' => $conn->error
];
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

            <h6 class="mb-3 text-muted"><i class="bi bi-people-fill me-1"></i>Household Members 
                <span class="badge bg-primary"><?= count($members_array) ?></span>
            </h6>
            
            <!-- Debug Information (remove in production) -->
            <?php if (isset($_GET['debug'])): ?>
                <div class="alert alert-info">
                    <strong>Debug Info:</strong><br>
                    Household ID: <?= $debug_info['household_id'] ?><br>
                    Total Members Found: <?= $debug_info['total_members'] ?><br>
                    MySQL Error: <?= $debug_info['query_error'] ?: 'None' ?><br>
                    <details>
                        <summary>Raw Member Data</summary>
                        <pre><?= htmlspecialchars(print_r($members_array, true)) ?></pre>
                    </details>
                </div>
            <?php endif; ?>
            
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Full Name</th>
                            <th>Contact Info</th>
                            <th>Birthdate</th>
                            <th>Gender</th>
                            <th>Relation</th>
                            <th>Occupation</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($members_array) > 0): ?>
                            <?php foreach ($members_array as $member): ?>
                                <?php
                                $is_resident = !empty($member['resident_id']);
                                $display_name = $is_resident && !empty($member['resident_first_name']) ? 
                                    trim($member['resident_first_name'] . ' ' . ($member['resident_middle_name'] ? $member['resident_middle_name'] . ' ' : '') . $member['resident_last_name']) :
                                    $member['full_name'];
                                ?>
                                <tr>
                                    <td>
                                        <div>
                                            <strong><?= htmlspecialchars($display_name) ?></strong>
                                            <?php if ($is_resident): ?>
                                                <br><small class="text-muted">
                                                    <i class="bi bi-person-check text-success"></i> Registered Resident
                                                </small>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if ($is_resident && ($member['resident_email'] || $member['resident_contact'])): ?>
                                            <div class="small">
                                                <?php if ($member['resident_email']): ?>
                                                    <i class="bi bi-envelope"></i> <?= htmlspecialchars($member['resident_email']) ?><br>
                                                <?php endif; ?>
                                                <?php if ($member['resident_contact']): ?>
                                                    <i class="bi bi-telephone"></i> <?= htmlspecialchars($member['resident_contact']) ?>
                                                <?php endif; ?>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($member['birthdate']) ?></td>
                                    <td><span class="badge bg-<?= $member['gender'] === 'Male' ? 'primary' : ($member['gender'] === 'Female' ? 'warning' : 'secondary') ?>"><?= $member['gender'] ?></span></td>
                                    <td><?= htmlspecialchars($member['relation_to_head']) ?></td>
                                    <td><?= htmlspecialchars($member['occupation']) ?></td>
                                    <td>
                                        <?php if ($is_resident): ?>
                                            <span class="badge bg-success">Linked</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Manual Entry</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <?php if ($is_resident): ?>
                                                <a href="../residents/index.php?highlight=<?= $member['resident_id'] ?>" 
                                                   class="btn btn-sm btn-outline-info"
                                                   title="View Resident Profile">
                                                   <i class="bi bi-eye"></i>
                                                </a>
                                            <?php endif; ?>
                                            <a href="delete_member.php?id=<?= $member['id'] ?>&household_id=<?= $household_id ?>" 
                                               class="btn btn-sm btn-outline-danger"
                                               onclick="return confirm('Are you sure you want to remove this member from the household?')"
                                               title="Remove from Household">
                                               <i class="bi bi-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted">
                                    No members found in this household.
                                    <br><small class="text-muted">
                                        <a href="add_member.php?household_id=<?= $household_id ?>" class="btn btn-sm btn-primary mt-2">
                                            <i class="bi bi-person-plus"></i> Add First Member
                                        </a>
                                    </small>
                                </td>
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
