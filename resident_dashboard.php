
<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'resident') {
    header("Location: login.php");
    exit();
}
include 'config.php';
$residentName = $_SESSION['name'];

// Use resident id from session (should be set at login)
$resident_id = $_SESSION['id'] ?? null;
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
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        .action-card:hover {
            transform: translateY(-8px);
            background: rgba(255, 255, 255, 0.2);
        }
        .action-btn {
            margin-top: auto;
            border-radius: 8px;
            font-weight: 500;
            padding: 10px 15px;
        }
        .id-picture-container {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            text-align: center;
            min-height: 200px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        .id-picture {
            width: 100%;
            max-width: 300px;
            height: auto;
            border-radius: 10px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
            object-fit: contain;
        }
        .no-id-text {
            color: #e0e0e0;
            font-style: italic;
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

        <!-- ID Picture Display Section -->
        <div class="id-picture-container">
            <h5 class="mb-3">Your ID Document</h5>
            <?php if ($resident_id): ?>
                <div class="w-100 d-flex flex-column align-items-center">
                    <img src="get_id_picture.php?id=<?= urlencode($resident_id) ?>" 
                    alt="ID Picture" 
                    class="id-picture"
                    onclick="openImageModal()">
                    <div class="mt-3">
                        <small class="text-light">Click image to view full size</small>
                    </div>
                </div>
            <?php else: ?>
                <div class="no-id-text">
                    <i class="fas fa-image fa-3x mb-3" style="opacity: 0.5;"></i>
                    <p>No ID Document uploaded</p>
                    <small>Please contact the barangay office to upload your ID document</small>
                </div>
            <?php endif; ?>
        </div>

        <div class="row g-4">
            <div class="col-md-3 d-flex">
                <div class="action-card">
                    <h5>Request Clearance</h5>
                    <p>Submit your barangay clearance request online.</p>
                    <a href="resident_clearance_request.php" class="btn btn-success w-100 action-btn">Request Now</a>
                </div>
            </div>
            <div class="col-md-3 d-flex">
                <div class="action-card">
                    <h5>File Blotter Complaint</h5>
                    <p>Report incidents or disputes within your community.</p>
                    <a href="resident_blotter_request.php" class="btn btn-danger w-100 action-btn">File Complaint</a>
                </div>
            </div>
            <div class="col-md-3 d-flex">
                <div class="action-card">
                    <h5>Assistance Request</h5>
                    <p>Request help due to medical, fire, disaster, or other emergencies, you can quickly notify barangay officials for immediate response.</p>
                    <a href="resident_assistance_request.php" class="btn btn-warning w-100 action-btn">Request Assistance</a>
                </div>
            </div>
            <div class="col-md-3 d-flex">
                <div class="action-card">
                    <h5>My Requests</h5>
                    <p>View the history and status of your past requests.</p>
                    <a href="resident_requests.php" class="btn btn-primary w-100 action-btn">View Requests</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for full-size image view -->
    <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content bg-transparent border-0">
                <div class="modal-header border-0">
                    <h5 class="modal-title text-white" id="imageModalLabel">ID Document</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <?php if ($resident_id): ?>
                        <img src="get_id_picture.php?id=<?= urlencode($resident_id) ?>" 
                             alt="ID Picture" 
                             class="img-fluid rounded">
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Add click event to image for modal display
        document.addEventListener('DOMContentLoaded', function() {
            const idPicture = document.querySelector('.id-picture');
            if (idPicture) {
                idPicture.style.cursor = 'pointer';
                idPicture.addEventListener('click', function() {
                    const modal = new bootstrap.Modal(document.getElementById('imageModal'));
                    modal.show();
                });
            }
        });
    </script>
</body>
</html>