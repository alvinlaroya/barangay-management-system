<?php 
include '../config.php'; 

// Get announcement ID from URL
$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: index.php');
    exit;
}

// Fetch announcement data
$stmt = $conn->prepare("SELECT * FROM announcements WHERE id = ? AND deleted_at IS NULL");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$announcement = $result->fetch_assoc();

if (!$announcement) {
    header('Location: index.php');
    exit;
}

// Handle form submission
if ($_POST) {
    $title = $_POST['title'] ?? '';
    $content = $_POST['content'] ?? '';
    
    if (!empty($title) && !empty($content)) {
        $stmt = $conn->prepare("UPDATE announcements SET title = ?, content = ? WHERE id = ?");
        $stmt->bind_param("ssi", $title, $content, $id);
        
        if ($stmt->execute()) {
            header('Location: index.php?success=updated');
            exit;
        } else {
            $error = "Failed to update announcement.";
        }
    } else {
        $error = "Please fill in all required fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Announcement - Barangay System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Quill.js for rich text editor -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', sans-serif;
        }
        .container {
            max-width: 800px;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        }
        h4 {
            font-weight: 600;
        }
        .form-label {
            font-weight: 500;
        }
        #editor {
            height: 300px;
        }
    </style>
</head>
<body>

<?php include '../navbar.php'; ?>

<div class="container mt-5">
    <div class="card p-4 mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="mb-0"><i class="bi bi-pencil text-primary"></i> Edit Announcement</h4>
            <a href="index.php" class="btn btn-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Back to Announcements
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-4">
            <?php if (isset($error)): ?>
                <div class="alert alert-danger" role="alert">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" id="announcementForm">
                <div class="mb-3">
                    <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="title" name="title" 
                           value="<?= htmlspecialchars($announcement['title']) ?>" required>
                </div>

                <div class="mb-4">
                    <label for="content" class="form-label">Content <span class="text-danger">*</span></label>
                    <div id="editor"><?= $announcement['content'] ?></div>
                    <textarea name="content" id="content" style="display: none;" required></textarea>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle"></i> Update Announcement
                    </button>
                    <a href="index.php" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- Quill.js -->
<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>

<script>
// Initialize Quill editor
var quill = new Quill('#editor', {
    theme: 'snow',
    modules: {
        toolbar: [
            [{ 'header': [1, 2, 3, false] }],
            ['bold', 'italic', 'underline'],
            [{ 'list': 'ordered'}, { 'list': 'bullet' }],
            ['link'],
            ['clean']
        ]
    }
});

// Set initial content in Quill and textarea
var initialContent = document.getElementById('editor').innerHTML;
quill.clipboard.dangerouslyPasteHTML(initialContent);
document.getElementById('content').value = initialContent;

// Handle form submission
document.getElementById('announcementForm').onsubmit = function() {
    // Get HTML content from Quill
    var content = quill.root.innerHTML;
    document.getElementById('content').value = content;
    return true;
};
</script>

</body>
</html>