<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'resident') {
    header("Location: ../login.php");
    exit();
}

include '../config.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $secretary_name = $_POST['secretary_name'];
    $content = $_POST['content'];
    $user_id = $_SESSION['user_id'];
    
    $stmt = $conn->prepare("INSERT INTO incident_reports (secretary_name, content, user_id) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $secretary_name, $content, $user_id);
    
    if ($stmt->execute()) {
        $success_message = "Incident report submitted successfully!";
    } else {
        $error_message = "Error submitting incident report. Please try again.";
    }
}

// Fetch resident's incident reports
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM incident_reports WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$reports = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Incident Reports</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Quill Rich Text Editor -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }
        .form-label {
            font-weight: 500;
            color: #333;
        }
        .quill-editor {
            min-height: 200px;
        }
        .report-card {
            transition: transform 0.2s;
        }
        .report-card:hover {
            transform: translateY(-2px);
        }
        .status-badge {
            font-size: 0.8rem;
        }
    </style>
</head>
<body>

<?php include '../navbar.php'; ?>

<div class="container mt-4">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            
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

            <!-- Create New Incident Report -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-file-earmark-text me-2"></i>Create New Incident Report</h5>
                </div>
                <div class="card-body">
                    <form method="POST" id="incidentForm">
                        <div class="mb-3">
                            <label for="secretary_name" class="form-label">Secretary Name</label>
                            <input type="text" class="form-control" id="secretary_name" name="secretary_name" required 
                                   placeholder="Enter the name of the secretary handling this report">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Report Content</label>
                            <div id="editor" class="quill-editor"></div>
                            <input type="hidden" name="content" id="content">
                        </div>
                        
                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn btn-secondary me-2" onclick="resetForm()">
                                <i class="bi bi-arrow-clockwise me-1"></i>Reset
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-send-fill me-1"></i>Submit Report
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- My Incident Reports -->
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="bi bi-list-ul me-2"></i>My Incident Reports</h5>
                </div>
                <div class="card-body">
                    <?php if ($reports->num_rows > 0): ?>
                        <div class="row">
                            <?php while ($report = $reports->fetch_assoc()): ?>
                                <div class="col-12 mb-3">
                                    <div class="card report-card">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start mb-3">
                                                <div>
                                                    <h6 class="card-title mb-1">
                                                        Report #<?= $report['id'] ?>
                                                    </h6>
                                                    <small class="text-muted">
                                                        <i class="bi bi-person-fill me-1"></i>
                                                        Secretary: <?= htmlspecialchars($report['secretary_name']) ?>
                                                    </small>
                                                </div>
                                                <span class="badge bg-<?= $report['status'] == 'Pending' ? 'warning' : 
                                                    ($report['status'] == 'Approved' ? 'success' : 'danger') ?> status-badge">
                                                    <?= $report['status'] ?>
                                                </span>
                                            </div>
                                            
                                            <div class="report-content mb-3">
                                                <?= $report['content'] ?>
                                            </div>
                                            
                                            <div class="d-flex justify-content-between align-items-center">
                                                <small class="text-muted">
                                                    <i class="bi bi-calendar-event me-1"></i>
                                                    Submitted: <?= date('M j, Y g:i A', strtotime($report['created_at'])) ?>
                                                </small>
                                                <button class="btn btn-sm btn-outline-primary" 
                                                        onclick="viewReport(<?= $report['id'] ?>)">
                                                    <i class="bi bi-eye me-1"></i>View Details
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="bi bi-inbox display-1 text-muted"></i>
                            <h5 class="text-muted mt-3">No incident reports yet</h5>
                            <p class="text-muted">Submit your first incident report using the form above.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Report Details Modal -->
<div class="modal fade" id="reportModal" tabindex="-1" aria-labelledby="reportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reportModalLabel">Incident Report Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modalContent">
                <!-- Content loaded via JavaScript -->
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>

<script>
// Initialize Quill editor
var quill = new Quill('#editor', {
    theme: 'snow',
    placeholder: 'Describe the incident in detail...',
    modules: {
        toolbar: [
            ['bold', 'italic', 'underline', 'strike'],
            ['blockquote', 'code-block'],
            [{ 'header': 1 }, { 'header': 2 }],
            [{ 'list': 'ordered'}, { 'list': 'bullet' }],
            [{ 'script': 'sub'}, { 'script': 'super' }],
            [{ 'indent': '-1'}, { 'indent': '+1' }],
            [{ 'direction': 'rtl' }],
            [{ 'size': ['small', false, 'large', 'huge'] }],
            [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
            [{ 'color': [] }, { 'background': [] }],
            [{ 'font': [] }],
            [{ 'align': [] }],
            ['clean']
        ]
    }
});

// Form submission handler
document.getElementById('incidentForm').addEventListener('submit', function(e) {
    // Get HTML content from Quill editor
    var content = quill.root.innerHTML;
    
    // Set the hidden input value
    document.getElementById('content').value = content;
    
    // Check if content is empty
    if (quill.getText().trim() === '') {
        e.preventDefault();
        alert('Please enter the incident report content.');
        return false;
    }
});

// Reset form function
function resetForm() {
    document.getElementById('secretary_name').value = '';
    quill.setContents([]);
}

// View report details function
function viewReport(reportId) {
    // Here you can implement AJAX to fetch and display report details
    // For now, we'll use a simple implementation
    fetch(`get_report_details.php?id=${reportId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('modalContent').innerHTML = `
                    <div class="mb-3">
                        <strong>Report ID:</strong> #${data.report.id}
                    </div>
                    <div class="mb-3">
                        <strong>Secretary:</strong> ${data.report.secretary_name}
                    </div>
                    <div class="mb-3">
                        <strong>Status:</strong> 
                        <span class="badge bg-${data.report.status == 'Pending' ? 'warning' : 
                            (data.report.status == 'Approved' ? 'success' : 'danger')}">${data.report.status}</span>
                    </div>
                    <div class="mb-3">
                        <strong>Submitted:</strong> ${new Date(data.report.created_at).toLocaleString()}
                    </div>
                    <div class="mb-3">
                        <strong>Content:</strong>
                        <div class="border p-3 mt-2">${data.report.content}</div>
                    </div>
                `;
                
                var modal = new bootstrap.Modal(document.getElementById('reportModal'));
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
</script>

</body>
</html>