<?php
include '../config.php';
if (!isset($_GET['id'])) {
    echo "<h3>No request selected.</h3>";
    exit();
}
$id = intval($_GET['id']);
$stmt = $conn->prepare("SELECT * FROM assistance_requests WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
if (!$result || $result->num_rows === 0) {
    echo "<h3>Request not found.</h3>";
    exit();
}
$r = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Assistance Request Details</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .card { border-radius: 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
        .label { font-weight: bold; color: #343a40; }
    </style>
</head>
<body>
<?php include '../navbar.php'; ?>
<div class="container mt-5" style="max-width: 800px;">
    <div class="card p-4">
        <h4 class="mb-3"><i class="bi bi-file-earmark-text"></i> Assistance Request Details</h4>
        <dl class="row">
            <?php foreach ($r as $col => $val): ?>
                <?php if ($col === 'id' || $col === 'user_id') continue; ?>
                <dt class="col-sm-3 label"><?= ucwords(str_replace(['_', 'id'], [' ', 'ID'], $col)) ?></dt>
                <dd class="col-sm-9">
                    <?php
                    if ($col === 'status') {
                        echo '<span class="badge bg-'.
                            (($val ?? 'Pending') === 'Pending' ? 'warning' :
                            (($val ?? '') === 'Approved' ? 'success' :
                            (($val ?? '') === 'Declined' ? 'danger' : 'secondary')))
                        .'">'.htmlspecialchars($val ?? 'Pending').'</span>';
                    } elseif ($col === 'requested_at' && !empty($val)) {
                        echo date('M d, Y h:i A', strtotime($val));
                    } elseif ($col === 'supporting_documents') {
                        if (!empty($val)) {
                            echo '<a href="../download.php?type=document&id='.$r['id'].'" class="btn btn-sm btn-outline-primary">Download</a>';
                        } else {
                            echo '<span class="text-muted">None</span>';
                        }
                    } elseif ($col === 'proof_of_damage') {
                        if (!empty($val)) {
                            echo '<a href="../download.php?type=image&id='.$r['id'].'" class="btn btn-sm btn-outline-primary">View</a>';
                        } else {
                            echo '<span class="text-muted">None</span>';
                        }
                    } else {
                        echo htmlspecialchars($val);
                    }
                    ?>
                </dd>
            <?php endforeach; ?>
        </dl>
        <a href="index.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Back to List</a>
    </div>
</div>
</body>
</html>
