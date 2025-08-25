<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'config.php';

// Use the logged-in user's full name
$userName = isset($_SESSION['name']) ? $_SESSION['name'] : 'User';

// Fetch statistics
$totalResidents = $conn->query("SELECT COUNT(*) as total FROM residents")->fetch_assoc()['total'];
$totalClearances = $conn->query("SELECT COUNT(*) as total FROM clearances")->fetch_assoc()['total'];
$totalHouseholds = $conn->query("SELECT COUNT(*) as total FROM households")->fetch_assoc()['total'];
$totalBlotters = $conn->query("SELECT COUNT(*) as total FROM blotters")->fetch_assoc()['total'];
$totalOfficials = $conn->query("SELECT COUNT(*) as total FROM barangay_officials")->fetch_assoc()['total'];

// Gender stats
$genderResult = $conn->query("SELECT gender, COUNT(*) AS count FROM residents GROUP BY gender");
$genderData = ['Male' => 0, 'Female' => 0, 'Others' => 0];
while ($row = $genderResult->fetch_assoc()) {
    $genderData[$row['gender']] = $row['count'];
}

// Age group stats
$ageGroups = ['0-17' => 0, '18-35' => 0, '36-60' => 0, '61+' => 0];
$residents = $conn->query("SELECT birthdate FROM residents");
$currentYear = date('Y');
while ($r = $residents->fetch_assoc()) {
    $birthYear = date('Y', strtotime($r['birthdate']));
    $age = $currentYear - $birthYear;
    if ($age <= 17) $ageGroups['0-17']++;
    else if ($age <= 35) $ageGroups['18-35']++;
    else if ($age <= 60) $ageGroups['36-60']++;
    else $ageGroups['61+']++;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Dashboard - Barangay System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            background-color: #f5f7fa;
            font-family: 'Segoe UI', sans-serif;
        }

        .card {
            border: none;
            border-radius: 12px;
            transition: all 0.3s ease-in-out;
        }

        .card:hover {
            box-shadow: 0 0 18px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        .card .card-title {
            font-size: 1rem;
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        .card .card-text {
            font-size: 2rem;
            font-weight: bold;
        }

        .shadow-sm {
            border-radius: 12px;
            background-color: white;
        }

        .container h3 {
            font-size: 1.75rem;
            color: #333;
            font-weight: 600;
        }

        footer {
            background-color: #f1f1f1;
        }
    </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container mt-4 mb-5">
    <div class="alert alert-primary rounded shadow-sm">
        Welcome back, <strong><?= htmlspecialchars($userName) ?></strong>!
    </div>

    <h3 class="mb-4">Dashboard Overview</h3>

    <div class="row g-4">
        <div class="col-sm-6 col-md-4 col-lg-3">
            <div class="card text-white bg-primary h-100">
                <div class="card-body">
                    <h5 class="card-title">Total Residents</h5>
                    <p class="card-text"><?= $totalResidents ?></p>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-4 col-lg-3">
            <div class="card text-white bg-success h-100">
                <div class="card-body">
                    <h5 class="card-title">Total Households</h5>
                    <p class="card-text"><?= $totalHouseholds ?></p>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-4 col-lg-3">
            <div class="card text-white bg-warning h-100">
                <div class="card-body">
                    <h5 class="card-title">Total Clearances</h5>
                    <p class="card-text"><?= $totalClearances ?></p>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-4 col-lg-3">
            <div class="card text-white bg-danger h-100">
                <div class="card-body">
                    <h5 class="card-title">Total Blotters</h5>
                    <p class="card-text"><?= $totalBlotters ?></p>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-4 col-lg-3">
            <div class="card text-white bg-info h-100">
                <div class="card-body">
                    <h5 class="card-title">Total Officials</h5>
                    <p class="card-text"><?= $totalOfficials ?></p>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-5 g-4">
        <div class="col-lg-6">
            <div class="card p-3 shadow-sm">
                <h5 class="card-title text-center">Gender Distribution</h5>
                <canvas id="genderChart" height="300"></canvas>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card p-3 shadow-sm">
                <h5 class="card-title text-center">Age Group Distribution</h5>
                <canvas id="ageChart" height="300"></canvas>
            </div>
        </div>
    </div>
</div>

<footer class="text-center text-muted py-3 border-top">
    &copy; <?= date('Y') ?> Barangay System. All rights reserved.
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Gender Chart
const genderCtx = document.getElementById('genderChart').getContext('2d');
new Chart(genderCtx, {
    type: 'pie',
    data: {
        labels: ['Male', 'Female', 'Others'],
        datasets: [{
            label: 'Gender Distribution',
            data: [
                <?= $genderData['Male'] ?? 0 ?>,
                <?= $genderData['Female'] ?? 0 ?>,
                <?= $genderData['Others'] ?? 0 ?>
            ],
            backgroundColor: ['#0d6efd', '#dc3545', '#6c757d']
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'bottom' }
        }
    }
});

// Age Group Chart
const ageCtx = document.getElementById('ageChart').getContext('2d');
new Chart(ageCtx, {
    type: 'bar',
    data: {
        labels: ['0-17', '18-35', '36-60', '61+'],
        datasets: [{
            label: 'Residents by Age Group',
            data: [
                <?= $ageGroups['0-17'] ?>,
                <?= $ageGroups['18-35'] ?>,
                <?= $ageGroups['36-60'] ?>,
                <?= $ageGroups['61+'] ?>
            ],
            backgroundColor: '#198754'
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true,
                ticks: { stepSize: 1 }
            }
        },
        plugins: {
            legend: { display: false }
        }
    }
});
</script>

</body>
</html>
