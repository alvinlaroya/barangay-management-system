<?php
session_start();
include '../config.php';
if (!isset($_SESSION['user_id'])) header("Location: ../login.php");

// Get filter values
$filter_senior = isset($_GET['senior']) ? true : false;
$filter_purok = isset($_GET['purok']) ? $_GET['purok'] : '';

// Build query
$query = "SELECT * FROM residents WHERE 1";
if ($filter_purok !== '') {
    $query .= " AND purok = '" . $conn->real_escape_string($filter_purok) . "'";
}
$result = $conn->query($query . " ORDER BY last_name ASC");
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
        @media print {
            body * {
                visibility: hidden;
            }
            .print-area, .print-area * {
                visibility: visible;
            }
            .print-area {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                padding: 0 !important;
                margin-top: -40px !important;
                margin-left: -240px !important;
                overflow: visible !important;
            }
            .print-area .table-responsive {
                overflow: visible !important;
                box-shadow: none !important;
            }
            .print-area table {
                margin: 0 !important;
            }
            .print-area th, .print-area td {
                padding: 0 !important;
                white-space: nowrap !important;
            }
            @page {
                size: landscape;
            }
        }
    </style>
</head>
<body>
    
<?php
include '../navbar.php';
?>

<div class="container mt-5">
    <div class="card p-4">
        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-4">
                <label for="purok" class="form-label">Filter by Purok</label>
                <select name="purok" id="purok" class="form-select">
                    <option value="">All Puroks</option>
                    <?php
                    $purok_result = $conn->query("SELECT DISTINCT purok FROM residents ORDER BY purok ASC");
                    while ($purok_row = $purok_result->fetch_assoc()):
                    ?>
                        <option value="<?= htmlspecialchars($purok_row['purok']) ?>" <?= ($filter_purok == $purok_row['purok']) ? 'selected' : '' ?>><?= htmlspecialchars($purok_row['purok']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="senior" id="senior" value="1" <?= $filter_senior ? 'checked' : '' ?>>
                    <label class="form-check-label" for="senior">Show only Seniors</label>
                </div>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="residents.php" class="btn btn-secondary ms-2">Reset</a>
            </div>
        </form>
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0"><i class="bi bi-people-fill text-primary"></i> Resident List</h4>
        </div>
        <div class="d-flex justify-content-end mb-3">
            <button class="btn btn-success btn-sm" onclick="printTable()">
                <i class="bi bi-printer"></i> Print Table
            </button>
        </div>

        <div class="table-responsive print-area">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Full Name</th>
                        <th>Gender</th>
                        <th>Birthdate</th>
                        <th>Age</th>
                        <th>Civil Status</th>
                        <th>Citizenship</th>
                        <th>Religion</th>
                        <th>Occupation</th>
                        <th>Voter Status</th>
                        <th>4Ps Member</th>
                        <th>Contact</th>
                        <th>Email</th>
                        <th>Address</th>
                        <th>Purok</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $result->fetch_assoc()): ?>
                    <?php
                        // Calculate age from birthdate
                        $birthdate = $row['birthdate'];
                        $age = '';
                        if ($birthdate) {
                            $dob = new DateTime($birthdate);
                            $now = new DateTime();
                            $age = $now->diff($dob)->y;
                        }
                        // If filtering by senior, skip if age < 60
                        if ($filter_senior && $age < 60) continue;
                    ?>
                    <tr>
                        <td><?= htmlspecialchars("{$row['last_name']}, {$row['first_name']} {$row['middle_name']} {$row['suffix']}") ?></td>
                        <td><?= $row['gender'] ?></td>
                        <td><?= htmlspecialchars($row['birthdate']) ?></td>
                        <td><?= htmlspecialchars($age) ?></td>
                        <td><?= htmlspecialchars($row['civil_status'] ?? '') ?></td>
                        <td><?= htmlspecialchars($row['citizenship'] ?? '') ?></td>
                        <td><?= htmlspecialchars($row['religion'] ?? '') ?></td>
                        <td><?= htmlspecialchars($row['occupation'] ?? '') ?></td>
                        <td><?= htmlspecialchars($row['voter_status'] ?? '') ?></td>
                        <td><?= isset($row['is_4ps']) ? ($row['is_4ps'] ? 'Yes' : 'No') : '' ?></td>
                        <td><?= htmlspecialchars($row['contact'] ?? '') ?></td>
                        <td><?= htmlspecialchars($row['email'] ?? '') ?></td>
                        <td><?= htmlspecialchars($row['address'] ?? '') ?></td>
                        <td><?= htmlspecialchars($row['purok'] ?? '') ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function printTable() {
    window.print();
}
</script>
</body>
</html>
