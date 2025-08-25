<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'resident') {
    header("Location: login.php");
    exit();
}
include 'config.php';
$residentName = $_SESSION['name'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Resident Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            color: #fff;
            font-family: 'Segoe UI', sans-serif;
        }
        .dashboard-container {
            padding: 40px 20px;
        }
        .welcome-card {
            background: rgba(255, 255, 255, 0.15);
            border: none;
            border-radius: 15px;
            padding: 25px;
            backdrop-filter: blur(10px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
        }
        .dashboard-title {
            font-size: 1.8rem;
            font-weight: 600;
            margin-bottom: 10px;
        }
        .dashboard-subtitle {
            font-size: 1rem;
            color: #e0e0e0;
        }
        .action-card {
            background: rgba(255, 255, 255, 0.1);
            border: none;
            border-radius: 15px;
            padding: 25px;
            text-align: center;
            transition: all 0.3s ease-in-out;
            color: #fff;
        }
        .action-card:hover {
            transform: translateY(-8px);
            background: rgba(255, 255, 255, 0.2);
        }
        .action-btn {
            margin-top: 15px;
            border-radius: 8px;
            font-weight: 500;
            padding: 10px 15px;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container dashboard-container">
        <div class="welcome-card mb-4">
            <h3 class="dashboard-title">Welcome, <?= htmlspecialchars($residentName) ?> 👋</h3>
            <p class="dashboard-subtitle">You can request a clearance, file a blotter complaint, and view your request history here.</p>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="action-card">
                    <h5>Request Clearance</h5>
                    <p>Submit your barangay clearance request online.</p>
                    <a href="resident_clearance_request.php" class="btn btn-success w-100 action-btn">Request Now</a>
                </div>
            </div>
            <div class="col-md-4">
                <div class="action-card">
                    <h5>File Blotter Complaint</h5>
                    <p>Report incidents or disputes within your community.</p>
                    <a href="resident_blotter_request.php" class="btn btn-danger w-100 action-btn">File Complaint</a>
                </div>
            </div>
            <div class="col-md-4">
                <div class="action-card">
                    <h5>My Requests</h5>
                    <p>View the history and status of your past requests.</p>
                    <a href="resident_requests.php" class="btn btn-primary w-100 action-btn">View Requests</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
