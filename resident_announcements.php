<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Households - Barangay System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', sans-serif;
        }
        .container {
            max-width: 1200px;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        }
        h4 {
            font-weight: 600;
        }
        .table th {
            background-color: #343a40;
            color: white;
        }
        .table td, .table th {
            vertical-align: middle;
        }
        .btn-sm {
            padding: 5px 10px;
            font-size: 14px;
        }
        .announcement-content {
            margin-top: 15px;
            line-height: 1.6;
        }
        .announcement-content p {
            margin-bottom: 10px;
        }
        .announcement-content h1, 
        .announcement-content h2, 
        .announcement-content h3 {
            color: #343a40;
            margin-top: 20px;
            margin-bottom: 10px;
        }
        .announcement-content ul, 
        .announcement-content ol {
            margin-bottom: 15px;
        }
        .announcement-content blockquote {
            border-left: 4px solid #0d6efd;
            padding-left: 15px;
            margin: 15px 0;
            color: #6c757d;
            font-style: italic;
        }
    </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container mt-5">
    <div class="card p-4 mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="mb-0"><i class="bi bi-house-door-fill text-primary"></i> Announcements</h4>
             <!-- <a href="add.php" class="btn btn-success btn-sm">
                <i class="bi bi-plus-circle"></i> Create Announcement
            </a> -->
        </div>
    </div>

    <?php
    $result = $conn->query("SELECT * FROM announcements WHERE deleted_at IS NULL ORDER BY id DESC");
    if ($result->num_rows > 0):
        while ($row = $result->fetch_assoc()):
    ?>
        <div class="p-4 mb-3 bg-white border" style="border-radius: 5px">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-primary"><?= htmlspecialchars($row['title']) ?></h5>
                <small class="text-muted">
                    <?= isset($row['created_at']) ? date('M j, Y', strtotime($row['created_at'])) : '' ?>
                </small>
            </div>
            
            <!-- Display HTML content here -->
            <div class="announcement-content">
                <?= $row['content'] ?>
            </div>
        </div>
    <?php endwhile; else: ?>
        <div class="text-center p-5">
            <i class="bi bi-megaphone text-muted" style="font-size: 3rem;"></i>
            <p class="text-muted mt-3">No announcement records found.</p>
        </div>
    <?php endif; ?>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>