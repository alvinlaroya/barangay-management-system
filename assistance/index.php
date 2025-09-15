<?php
include '../config.php';

$requests = $conn->query("SELECT * FROM assistance_requests ORDER BY requested_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Assistance Requests - Barangay System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap 5 & Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .card { border-radius: 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
        .table th { background-color: #343a40; color: white; }
        .table td, .table th { vertical-align: middle; }
        .btn-sm { padding: 4px 10px; }
    </style>
</head>
<body>
<?php include '../navbar.php'; ?>
<div class="container mt-5">
    <div class="card p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0"><i class="bi bi-exclamation-triangle-fill text-danger"></i> Assistance Requests</h4>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Full Name</th>
                        <th>Type</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Requested At</th>
                        <th>Supporting Documents</th>
                        <th>Proof of Damage</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($r = $requests->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($r['fullname']) ?></td>
                        <td><?= htmlspecialchars($r['emergency_type']) ?></td>
                        <td><?= htmlspecialchars($r['emergency_description']) ?></td>
                        <td>
                            <span class="badge bg-<?php
                                if (($r['status'] ?? 'Pending') === 'Pending') echo 'warning';
                                else if (($r['status'] ?? '') === 'Approved') echo 'success';
                                else if (($r['status'] ?? '') === 'Declined') echo 'danger';
                                else echo 'secondary';
                            ?>">
                                <?= htmlspecialchars($r['status'] ?? 'Pending') ?>
                            </span>
                        </td>
                        <td><?= date('M d, Y h:i A', strtotime($r['requested_at'])) ?></td>
                        <td>
                            <?php if (!empty($r['supporting_documents'])): ?>
                                <a href="../download.php?type=document&id=<?= $r['id'] ?>" class="btn btn-sm btn-outline-primary">Download</a>
                            <?php else: ?>
                                <span class="text-muted">None</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (!empty($r['proof_of_damage'])): ?>
                                <a href="../download.php?type=image&id=<?= $r['id'] ?>" class="btn btn-sm btn-outline-primary">View</a>
                            <?php else: ?>
                                <span class="text-muted">None</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-success" onclick="updateStatus(<?= $r['id'] ?>, 'Approved', '<?= addslashes($r['email']) ?>', '<?= addslashes($r['fullname']) ?>')">
                                <i class="bi bi-check-circle"></i> Approve
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="updateStatus(<?= $r['id'] ?>, 'Declined', '<?= addslashes($r['email']) ?>', '<?= addslashes($r['fullname']) ?>')">
                                <i class="bi bi-x-circle"></i> Decline
                            </button>
                            <a href="view.php?id=<?= $r['id'] ?>" class="btn btn-sm btn-info">
                                <i class="bi bi-eye"></i> View Details
                            </a>
                            <a href="delete.php?id=<?= $r['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this request?')">
                                <i class="bi bi-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endwhile ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- Email.js SDK -->
<script src="https://cdn.emailjs.com/dist/email.min.js"></script>
<script>
    (function(){
        emailjs.init("5sfLZPD2Z7feLMjez"); // Replace with your Email.js public key
    })();

    function updateStatus(id, status, email, fullname) {
        if (!confirm(status + ' this request?')) return;
        fetch('update_status.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'id=' + encodeURIComponent(id) + '&action=' + encodeURIComponent(status)
        })
        .then(response => response.text())
        .then(data => {
            sendStatusEmail(email, fullname, status);
            alert('Status updated and email sent!');
            location.reload();
        })
        .catch(err => alert('Error updating status: ' + err));
    }

    function sendStatusEmail(toEmail, fullname, status) {
        emailjs.send("service_tnuy0yi", "template_c0m0xia", {
            to_email: toEmail,
            from: 'Barangay System',
            message: `Hi ${fullname}, your assistance request has been ${status}.`,
            status: status
            // Add other template parameters as needed
        })
        .then(function(response) {
            console.log("Email sent successfully!");
        }, function(error) {
            console.error("Failed to send email: ", error);
        });
    }
</script>
</body>
</html>
