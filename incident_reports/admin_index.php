<?php
session_start();
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'staff'])) {
    header("Location: ../login.php");
    exit();
}

include '../config.php';

// Handle status updates
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $report_id = $_POST['report_id'];
    $new_status = $_POST['status'];
    
    $stmt = $conn->prepare("UPDATE incident_reports SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $new_status, $report_id);
    
    if ($stmt->execute()) {
        $success_message = "Report status updated successfully!";
    } else {
        $error_message = "Error updating report status.";
    }
}

// Handle delete
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_report'])) {
    $report_id = $_POST['report_id'];
    
    $stmt = $conn->prepare("DELETE FROM incident_reports WHERE id = ?");
    $stmt->bind_param("i", $report_id);
    
    if ($stmt->execute()) {
        $success_message = "Report deleted successfully!";
    } else {
        $error_message = "Error deleting report.";
    }
}

// Fetch all incident reports with user information and resident details
$query = "SELECT ir.*, 
                 u.name as user_name, 
                 u.username as username,
                 u.contact as user_contact,
                 r.first_name as resident_first_name,
                 r.middle_name as resident_middle_name,
                 r.last_name as resident_last_name,
                 r.email as resident_email,
                 r.address as resident_address,
                 r.purok as resident_purok,
                 r.contact as resident_contact
          FROM incident_reports ir 
          LEFT JOIN users u ON ir.user_id = u.id 
          LEFT JOIN residents r ON u.resident_id = r.id
          ORDER BY ir.created_at DESC";
$result = $conn->query($query);

// Debug: Check if query executed successfully
if (!$result) {
    $error_message = "Error fetching incident reports: " . $conn->error;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Incident Reports Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }
        .status-badge {
            font-size: 0.8rem;
        }
        .content-preview {
            max-width: 300px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
    </style>
</head>
<body>

<?php include '../navbar.php'; ?>

<div class="container mt-4">
    <!-- Success/Error Messages -->
    <?php if (isset($success_message)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            <?= $success_message ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <?= $error_message ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <?php
        // Get statistics
        $statsQuery = "SELECT 
                        COUNT(*) as total,
                        SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) as pending,
                        SUM(CASE WHEN status = 'Under Review' THEN 1 ELSE 0 END) as under_review,
                        SUM(CASE WHEN status = 'Approved' THEN 1 ELSE 0 END) as approved,
                        SUM(CASE WHEN status = 'Rejected' THEN 1 ELSE 0 END) as rejected
                       FROM incident_reports";
        $statsResult = $conn->query($statsQuery);
        $stats = $statsResult ? $statsResult->fetch_assoc() : ['total' => 0, 'pending' => 0, 'under_review' => 0, 'approved' => 0, 'rejected' => 0];
        ?>
        
        <div class="col-md-2">
            <div class="card text-center bg-primary text-white">
                <div class="card-body py-3">
                    <h5 class="card-title mb-1"><?= $stats['total'] ?></h5>
                    <small>Total Reports</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center bg-warning text-dark">
                <div class="card-body py-3">
                    <h5 class="card-title mb-1"><?= $stats['pending'] ?></h5>
                    <small>Pending</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center bg-info text-white">
                <div class="card-body py-3">
                    <h5 class="card-title mb-1"><?= $stats['under_review'] ?></h5>
                    <small>Under Review</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center bg-success text-white">
                <div class="card-body py-3">
                    <h5 class="card-title mb-1"><?= $stats['approved'] ?></h5>
                    <small>Approved</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center bg-danger text-white">
                <div class="card-body py-3">
                    <h5 class="card-title mb-1"><?= $stats['rejected'] ?></h5>
                    <small>Rejected</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center bg-secondary text-white">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-center gap-2">
                        <a href="export_reports.php" class="text-white text-decoration-none" title="Export to CSV">
                            <i class="bi bi-download"></i>
                        </a>
                        <a href="print_reports.php" target="_blank" class="text-white text-decoration-none" title="Print Reports">
                            <i class="bi bi-printer"></i>
                        </a>
                    </div>
                    <small>Export/Print</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Card -->
    <div class="card">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-0">
                    <i class="bi bi-file-earmark-text me-2"></i>
                    Incident Reports Management
                </h4>
            </div>
            <div>
                <select class="form-select form-select-sm" id="statusFilter" onchange="filterByStatus()">
                    <option value="">All Status</option>
                    <option value="Pending">Pending</option>
                    <option value="Under Review">Under Review</option>
                    <option value="Approved">Approved</option>
                    <option value="Rejected">Rejected</option>
                </select>
            </div>
        </div>
        <div class="card-body">
            <?php if ($result->num_rows > 0): ?>
                <div class="table-responsive">
                    <table class="table table-striped" id="reportsTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Reporter</th>
                                <th>Secretary</th>
                                <th>Content Preview</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($report = $result->fetch_assoc()): ?>
                                <tr>
                                    <td>#<?= $report['id'] ?></td>
                                    <td>
                                        <div>
                                            <?php if ($report['resident_first_name']): ?>
                                                <strong><?= htmlspecialchars($report['resident_first_name'] . ' ' . ($report['resident_middle_name'] ? $report['resident_middle_name'] . ' ' : '') . $report['resident_last_name']) ?></strong>
                                                <br>
                                                <small class="text-muted"><?= htmlspecialchars($report['resident_email']) ?></small>
                                                <?php if ($report['resident_purok']): ?>
                                                    <br><small class="text-muted">Purok: <?= htmlspecialchars($report['resident_purok']) ?></small>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <strong><?= htmlspecialchars($report['user_name']) ?></strong>
                                                <br>
                                                <small class="text-muted">Username: <?= htmlspecialchars($report['username']) ?></small>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td><?= htmlspecialchars($report['secretary_name']) ?></td>
                                    <td>
                                        <div class="content-preview" title="<?= htmlspecialchars(strip_tags($report['content'])) ?>">
                                            <?= htmlspecialchars(substr(strip_tags($report['content']), 0, 50)) ?>...
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?= $report['status'] == 'Pending' ? 'warning' : 
                                            ($report['status'] == 'Approved' ? 'success' : 
                                            ($report['status'] == 'Rejected' ? 'danger' : 'secondary')) ?> status-badge">
                                            <?= $report['status'] ?>
                                        </span>
                                    </td>
                                    <td>
                                        <small>
                                            <?= date('M j, Y', strtotime($report['created_at'])) ?>
                                            <br>
                                            <?= date('g:i A', strtotime($report['created_at'])) ?>
                                        </small>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button class="btn btn-sm btn-outline-primary" 
                                                    onclick="viewReport(<?= $report['id'] ?>)">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-warning" 
                                                    onclick="updateStatus(<?= $report['id'] ?>, '<?= $report['status'] ?>')">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger" 
                                                    onclick="deleteReport(<?= $report['id'] ?>)">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="bi bi-inbox display-1 text-muted"></i>
                    <h5 class="text-muted mt-3">No incident reports found</h5>
                    <p class="text-muted">Incident reports submitted by residents will appear here.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- View Report Modal -->
<div class="modal fade" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewModalLabel">Incident Report Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="viewModalContent">
                <!-- Content loaded via JavaScript -->
            </div>
        </div>
    </div>
</div>

<!-- Update Status Modal -->
<div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="statusModalLabel">Update Report Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="report_id" id="statusReportId">
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" name="status" id="statusSelect" required>
                            <option value="Pending">Pending</option>
                            <option value="Under Review">Under Review</option>
                            <option value="Approved">Approved</option>
                            <option value="Rejected">Rejected</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="update_status" class="btn btn-primary">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this incident report? This action cannot be undone.
            </div>
            <form method="POST">
                <div class="modal-footer">
                    <input type="hidden" name="report_id" id="deleteReportId">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="delete_report" class="btn btn-danger">Delete Report</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
let dataTable;

$(document).ready(function() {
    dataTable = $('#reportsTable').DataTable({
        pageLength: 10,
        responsive: true,
        order: [[5, 'desc']], // Order by date (newest first)
        columnDefs: [
            { orderable: false, targets: [6] } // Disable sorting for Actions column
        ],
        language: {
            search: "Search reports:",
            lengthMenu: "Show _MENU_ reports per page",
            info: "Showing _START_ to _END_ of _TOTAL_ reports",
            emptyTable: "No incident reports found"
        }
    });
});

function filterByStatus() {
    const selectedStatus = document.getElementById('statusFilter').value;
    dataTable.column(4).search(selectedStatus).draw(); // Status is column 4 (0-indexed)
}

function viewReport(reportId) {
    fetch(`view_report.php?id=${reportId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const report = data.report;
                const reporterName = report.resident_first_name ? 
                    `${report.resident_first_name} ${report.resident_middle_name ? report.resident_middle_name + ' ' : ''}${report.resident_last_name}` :
                    report.user_name;
                
                const reporterEmail = report.resident_email || 'N/A';
                const reporterContact = report.resident_contact || report.user_contact || 'N/A';
                
                document.getElementById('viewModalContent').innerHTML = `
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-primary">Report Information</h6>
                            <p><strong>Report ID:</strong> #${report.id}</p>
                            <p><strong>Secretary:</strong> ${report.secretary_name}</p>
                            <p><strong>Status:</strong> 
                                <span class="badge bg-${report.status == 'Pending' ? 'warning' : 
                                    (report.status == 'Under Review' ? 'info' :
                                    (report.status == 'Approved' ? 'success' : 'danger'))}">${report.status}</span>
                            </p>
                            <p><strong>Submitted:</strong> ${new Date(report.created_at).toLocaleString()}</p>
                            ${report.updated_at !== report.created_at ? `<p><strong>Last Updated:</strong> ${new Date(report.updated_at).toLocaleString()}</p>` : ''}
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-primary">Reporter Information</h6>
                            <p><strong>Name:</strong> ${reporterName}</p>
                            <p><strong>Email:</strong> ${reporterEmail}</p>
                            <p><strong>Contact:</strong> ${reporterContact}</p>
                            ${report.username ? `<p><strong>Username:</strong> ${report.username}</p>` : ''}
                            ${report.resident_purok ? `<p><strong>Purok:</strong> ${report.resident_purok}</p>` : ''}
                            ${report.resident_address ? `<p><strong>Address:</strong> ${report.resident_address}</p>` : ''}
                        </div>
                    </div>
                    <hr>
                    <div class="mb-3">
                        <h6 class="text-primary">Report Content</h6>
                        <div class="border p-3 mt-2" style="max-height: 400px; overflow-y: auto; background-color: #f8f9fa;">
                            ${report.content}
                        </div>
                    </div>
                `;
                
                var modal = new bootstrap.Modal(document.getElementById('viewModal'));
                modal.show();
            } else {
                alert('Error loading report details.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading report details.');
        });
}

function updateStatus(reportId, currentStatus) {
    document.getElementById('statusReportId').value = reportId;
    document.getElementById('statusSelect').value = currentStatus;
    
    var modal = new bootstrap.Modal(document.getElementById('statusModal'));
    modal.show();
}

function deleteReport(reportId) {
    document.getElementById('deleteReportId').value = reportId;
    
    var modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}
</script>

</body>
</html>