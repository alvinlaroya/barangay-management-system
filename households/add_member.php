<?php
include '../config.php';
$household_id = $_GET['household_id'];

// Check if resident is already a household member
function isAlreadyMember($conn, $resident_id) {
    $check_query = "SELECT COUNT(*) as count FROM household_members WHERE resident_id = ?";
    $stmt = $conn->prepare($check_query);
    $stmt->bind_param("i", $resident_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc()['count'] > 0;
}

// Fetch all residents for the dropdown
$residents_query = "SELECT id, first_name, middle_name, last_name, birthdate, gender, occupation FROM residents ORDER BY last_name, first_name";
$residents_result = $conn->query($residents_query);

// Debug: Check if we have residents and query was successful
if ($residents_result === false) {
    $error_message = "Error fetching residents: " . $conn->error;
    $total_residents = 0;
    $residents_array = [];
} else {
    $total_residents = $residents_result->num_rows;
    // Fetch all residents into an array so we can use them in the HTML
    $residents_array = [];
    while ($row = $residents_result->fetch_assoc()) {
        $residents_array[] = $row;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate input data
    if (empty($_POST['resident_id']) || empty($_POST['relation'])) {
        $error_message = "Please fill in all required fields.";
    } else {
        $resident_id = intval($_POST['resident_id']);
        $relation = trim($_POST['relation']);
        
        // Start transaction for data integrity
        $conn->begin_transaction();
        
        try {            
            // Get resident details
            $resident_query = "SELECT first_name, middle_name, last_name, birthdate, gender, occupation FROM residents WHERE id = ?";
            $stmt = $conn->prepare($resident_query);
            $stmt->bind_param("i", $resident_id);
            $stmt->execute();
            $resident = $stmt->get_result()->fetch_assoc();
            
            if (!$resident) {
                throw new Exception("Selected resident not found.");
            }
            
            // Build full name
            $full_name = trim($resident['first_name'] . ' ' . ($resident['middle_name'] ? $resident['middle_name'] . ' ' : '') . $resident['last_name']);
            
            // Insert household member with resident reference
            $insert_stmt = $conn->prepare("INSERT INTO household_members (household_id, full_name, birthdate, gender, relation_to_head, occupation) VALUES (?, ?, ?, ?, ?, ?)");
            $insert_stmt->bind_param("isssss", $household_id, $full_name, $resident['birthdate'], $resident['gender'], $relation, $resident['occupation']);
            
            if (!$insert_stmt->execute()) {
                throw new Exception("Database error: " . $insert_stmt->error);
            }
            
            // Get the inserted member ID
            $new_member_id = $conn->insert_id;
            
            // Log the successful operation
            error_log("Household Member Added: Member ID=$new_member_id, Household ID=$household_id, Resident ID=$resident_id, Name=$full_name, Relation=$relation");
            
            // Commit the transaction
            $conn->commit();
            
            // Verify the data was saved correctly
            $verify_stmt = $conn->prepare("SELECT id, full_name, relation_to_head FROM household_members WHERE id = ?");
            $verify_stmt->bind_param("i", $new_member_id);
            $verify_stmt->execute();
            $verification = $verify_stmt->get_result()->fetch_assoc();
            
            if ($verification) {
                $success_message = "Resident successfully added to household! (Member ID: #$new_member_id)<br>";
                $success_message .= "<small class='text-muted'>Verified: {$verification['full_name']} as {$verification['relation_to_head']}</small>";
            } else {
                $success_message = "Member added but verification failed. Please check the household members list.";
            }
            
            // Optional: Auto-redirect after success (uncomment if desired)
            // header("refresh:2;url=view.php?id=$household_id&success=1");
            
        } catch (Exception $e) {
            // Rollback transaction on error
            $conn->rollback();
            $error_message = $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Household Member</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
            font-family: 'Segoe UI', sans-serif;
        }
        .form-card {
            background: #ffffff;
            border-radius: 16px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            margin-top: 50px;
        }
        .form-title {
            font-weight: 600;
            margin-bottom: 25px;
            text-align: center;
            color: #333;
        }
        .btn-primary {
            background-color: #2575fc;
            border: none;
            border-radius: 10px;
        }
        .btn-primary:hover {
            background-color: #1a5fd6;
        }
        .btn-secondary {
            border-radius: 10px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="form-card">
                <h4 class="form-title">Add Member to Household #<?= htmlspecialchars($household_id) ?></h4>
                
                <?php if (isset($error_message)): ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i><?= $error_message ?>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($success_message)): ?>
                    <div class="alert alert-success" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i><?= $success_message ?>
                        <div class="mt-2">
                            <a href="view.php?id=<?= $household_id ?>" class="btn btn-sm btn-success me-2">View Household</a>
                            <button onclick="resetForm()" class="btn btn-sm btn-outline-success">Add Another Member</button>
                        </div>
                    </div>
                <?php endif; ?>
                
                <?php if ($total_residents == 0): ?>
                    <div class="alert alert-warning" role="alert">
                        <strong>No residents found!</strong> You need to add residents to the system before you can add them to households.
                        <br><a href="../residents/add.php" class="btn btn-sm btn-primary mt-2">Add New Resident</a>
                    </div>
                <?php elseif ($total_residents > 0 && count($residents_array) > 0): ?>
                    <!-- Debug info - can be removed later -->
                    <div class="alert alert-info" role="alert">
                        <small><strong>Debug:</strong> Found <?= $total_residents ?> residents. First few: 
                        <?php 
                        $first_few = array_slice($residents_array, 0, 3);
                        foreach ($first_few as $r) {
                            echo htmlspecialchars($r['first_name'] . ' ' . $r['last_name']) . ', ';
                        }
                        ?>
                        </small>
                    </div>
                <?php endif; ?>
                
                <form method="POST" id="memberForm">
                    <div class="mb-3">
                        <label class="form-label">Select Resident</label>
                        <select name="resident_id" class="form-select" id="residentSelect" required onchange="updateResidentInfo()">
                            <option value="">Choose a resident...</option>
                            <?php if ($total_residents > 0): ?>
                                <?php foreach ($residents_array as $resident): ?>
                                    <?php 
                                    $resident_name = trim($resident['first_name'] . ' ' . ($resident['middle_name'] ? $resident['middle_name'] . ' ' : '') . $resident['last_name']);
                                    ?>
                                    <option value="<?= $resident['id'] ?>" 
                                            data-name="<?= htmlspecialchars($resident_name) ?>"
                                            data-birthdate="<?= $resident['birthdate'] ?>"
                                            data-gender="<?= $resident['gender'] ?>"
                                            data-occupation="<?= htmlspecialchars($resident['occupation']) ?>"
                                            data-already-member="<?= $already_member ? 'true' : 'false' ?>">
                                        <?= htmlspecialchars($resident_name) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <option value="" disabled>No residents found. Please add residents first.</option>
                            <?php endif; ?>
                        </select>
                        <small class="form-text text-muted">
                            Total residents available: <?= $total_residents ?>
                            <?php if ($total_residents == 0): ?>
                                | <a href="../residents/add.php" target="_blank">Add a resident first</a>
                                | <a href="../debug_residents.php" target="_blank">Debug residents table</a>
                            <?php else: ?>
                                | Residents loaded: <?= count($residents_array) ?>
                            <?php endif; ?>
                        </small>
                    </div>
                    
                    <!-- Resident Information Display -->
                    <div id="residentInfo" class="card mb-3" style="display: none;">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Selected Resident Information</h6>
                        </div>
                        <div class="card-body">
                            <div id="alreadyMemberWarning" class="alert alert-warning" style="display: none;">
                                <strong>Notice:</strong> This resident is already a member of another household. Adding them here will create a duplicate entry.
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Name:</strong> <span id="displayName">-</span></p>
                                    <p class="mb-1"><strong>Birthdate:</strong> <span id="displayBirthdate">-</span></p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Gender:</strong> <span id="displayGender">-</span></p>
                                    <p class="mb-1"><strong>Occupation:</strong> <span id="displayOccupation">-</span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label">Relation to Household Head</label>
                        <select name="relation" class="form-select" required>
                            <option value="">Select Relationship</option>
                            <option value="Head">Head</option>
                            <option value="Spouse">Spouse</option>
                            <option value="Son">Son</option>
                            <option value="Daughter">Daughter</option>
                            <option value="Father">Father</option>
                            <option value="Mother">Mother</option>
                            <option value="Brother">Brother</option>
                            <option value="Sister">Sister</option>
                            <option value="Grandfather">Grandfather</option>
                            <option value="Grandmother">Grandmother</option>
                            <option value="Grandson">Grandson</option>
                            <option value="Granddaughter">Granddaughter</option>
                            <option value="Uncle">Uncle</option>
                            <option value="Aunt">Aunt</option>
                            <option value="Nephew">Nephew</option>
                            <option value="Niece">Niece</option>
                            <option value="Cousin">Cousin</option>
                            <option value="Son-in-law">Son-in-law</option>
                            <option value="Daughter-in-law">Daughter-in-law</option>
                            <option value="Other Relative">Other Relative</option>
                            <option value="Non-Relative">Non-Relative</option>
                        </select>
                    </div>
                    <div class="d-flex justify-content-between">
                        <a href="view.php?id=<?= $household_id ?>" class="btn btn-secondary">Back</a>
                        <button type="submit" class="btn btn-primary" id="submitBtn" disabled>Add Member</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function updateResidentInfo() {
    const select = document.getElementById('residentSelect');
    const selectedOption = select.options[select.selectedIndex];
    const infoDiv = document.getElementById('residentInfo');
    const warningDiv = document.getElementById('alreadyMemberWarning');
    const submitBtn = document.getElementById('submitBtn');
    
    if (selectedOption.value) {
        // Show resident information
        document.getElementById('displayName').textContent = selectedOption.dataset.name || '-';
        document.getElementById('displayBirthdate').textContent = selectedOption.dataset.birthdate || '-';
        document.getElementById('displayGender').textContent = selectedOption.dataset.gender || '-';
        document.getElementById('displayOccupation').textContent = selectedOption.dataset.occupation || '-';
        
      
        infoDiv.style.display = 'block';
        submitBtn.disabled = false;
    } else {
        // Hide resident information
        infoDiv.style.display = 'none';
        warningDiv.style.display = 'none';
        submitBtn.disabled = true;
    }
}

// Reset form function
function resetForm() {
    document.getElementById('memberForm').reset();
    document.getElementById('residentInfo').style.display = 'none';
    document.getElementById('alreadyMemberWarning').style.display = 'none';
    document.getElementById('submitBtn').disabled = true;
}

// Form validation
document.getElementById('memberForm').addEventListener('submit', function(e) {
    const residentSelect = document.getElementById('residentSelect');
    const relationSelect = document.querySelector('select[name="relation"]');
    
    if (!residentSelect.value) {
        e.preventDefault();
        alert('Please select a resident.');
        return false;
    }
    
    if (!relationSelect.value) {
        e.preventDefault();
        alert('Please select the relationship to household head.');
        return false;
    }
});
</script>

</body>
</html>
