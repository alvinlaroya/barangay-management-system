<?php
include '../config.php';

echo "<h2>Debug: Add Member Process</h2>";

// Debug GET parameters
echo "<h3>GET Parameters:</h3>";
echo "<pre>" . print_r($_GET, true) . "</pre>";

// Debug POST parameters  
echo "<h3>POST Parameters:</h3>";
echo "<pre>" . print_r($_POST, true) . "</pre>";

// Check if household_id is provided
if (!isset($_GET['household_id'])) {
    echo "<p style='color: red;'>ERROR: No household_id provided in URL</p>";
    echo "<p>Please access this page like: add_member_debug.php?household_id=1</p>";
    exit;
}

$household_id = intval($_GET['household_id']);
echo "<p>Household ID: $household_id</p>";

// Check if household exists
$household_check = $conn->prepare("SELECT * FROM households WHERE id = ?");
$household_check->bind_param("i", $household_id);
$household_check->execute();
$household_result = $household_check->get_result();

if ($household_result->num_rows == 0) {
    echo "<p style='color: red;'>ERROR: Household with ID $household_id does not exist</p>";
    exit;
} else {
    $household = $household_result->fetch_assoc();
    echo "<p style='color: green;'>✅ Household found: " . print_r($household, true) . "</p>";
}

// Check residents
$residents_result = $conn->query("SELECT id, first_name, last_name FROM residents");
echo "<h3>Available Residents: " . $residents_result->num_rows . "</h3>";
while ($resident = $residents_result->fetch_assoc()) {
    echo "<p>ID: {$resident['id']} - {$resident['first_name']} {$resident['last_name']}</p>";
}

// Process form if submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    echo "<h3>Processing Form Submission...</h3>";
    
    $resident_id = intval($_POST['resident_id']);
    $relation = trim($_POST['relation']);
    
    echo "<p>Resident ID: $resident_id</p>";
    echo "<p>Relation: $relation</p>";
    
    // Check if resident exists
    $resident_check = $conn->prepare("SELECT * FROM residents WHERE id = ?");
    $resident_check->bind_param("i", $resident_id);
    $resident_check->execute();
    $resident_result = $resident_check->get_result();
    
    if ($resident_result->num_rows == 0) {
        echo "<p style='color: red;'>ERROR: Resident with ID $resident_id does not exist</p>";
    } else {
        $resident = $resident_result->fetch_assoc();
        echo "<p style='color: green;'>✅ Resident found: {$resident['first_name']} {$resident['last_name']}</p>";
        
        // Try to insert
        $full_name = trim($resident['first_name'] . ' ' . ($resident['middle_name'] ? $resident['middle_name'] . ' ' : '') . $resident['last_name']);
        
        $insert_query = "INSERT INTO household_members (household_id, resident_id, full_name, birthdate, gender, relation_to_head, occupation) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_query);
        
        if (!$stmt) {
            echo "<p style='color: red;'>ERROR preparing statement: " . $conn->error . "</p>";
        } else {
            $stmt->bind_param("iisssss", $household_id, $resident_id, $full_name, $resident['birthdate'], $resident['gender'], $relation, $resident['occupation']);
            
            if ($stmt->execute()) {
                $new_id = $conn->insert_id;
                echo "<p style='color: green;'>✅ SUCCESS! Member added with ID: $new_id</p>";
            } else {
                echo "<p style='color: red;'>ERROR executing statement: " . $stmt->error . "</p>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Debug Add Member</title>
</head>
<body>
    <h3>Test Form</h3>
    <form method="POST">
        <p>
            <label>Select Resident:</label>
            <select name="resident_id" required>
                <option value="">Choose...</option>
                <?php
                // Reload residents for dropdown
                $residents_result = $conn->query("SELECT id, first_name, last_name FROM residents");
                while ($resident = $residents_result->fetch_assoc()) {
                    echo "<option value='{$resident['id']}'>{$resident['first_name']} {$resident['last_name']}</option>";
                }
                ?>
            </select>
        </p>
        
        <p>
            <label>Relation:</label>
            <select name="relation" required>
                <option value="">Choose...</option>
                <option value="Head">Head</option>
                <option value="Spouse">Spouse</option>
                <option value="Son">Son</option>
                <option value="Daughter">Daughter</option>
            </select>
        </p>
        
        <p>
            <button type="submit">Add Member</button>
        </p>
    </form>
    
    <h3>Current Members in this Household:</h3>
    <?php
    $members_result = $conn->query("SELECT * FROM household_members WHERE household_id = $household_id");
    if ($members_result->num_rows > 0) {
        while ($member = $members_result->fetch_assoc()) {
            echo "<p>ID: {$member['id']} | {$member['full_name']} | {$member['relation_to_head']}</p>";
        }
    } else {
        echo "<p>No members in this household yet.</p>";
    }
    ?>
</body>
</html>