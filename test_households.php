<?php
include 'config.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Household Test</title>
</head>
<body>
    <h2>Household Testing</h2>
    
    <?php
    // Check households
    $households_result = $conn->query("SELECT * FROM households LIMIT 5");
    if ($households_result && $households_result->num_rows > 0) {
        echo "<h3>Available Households:</h3>";
        while ($household = $households_result->fetch_assoc()) {
            echo "<p>Household ID: {$household['id']} | ";
            echo "Address: " . htmlspecialchars($household['address'] ?? 'N/A') . " | ";
            echo "<a href='households/add_member.php?household_id={$household['id']}' target='_blank'>Add Member</a>";
            echo "</p>";
        }
    } else {
        echo "<p>No households found. Let's create a test household:</p>";
        
        // Create a test household
        $insert_household = $conn->query("INSERT INTO households (address, barangay, city, province) VALUES ('Test Address', 'Test Barangay', 'Test City', 'Test Province')");
        if ($insert_household) {
            $test_household_id = $conn->insert_id;
            echo "<p>✅ Created test household with ID: $test_household_id</p>";
            echo "<p><a href='households/add_member.php?household_id=$test_household_id' target='_blank'>Test Add Member</a></p>";
        } else {
            echo "<p>❌ Failed to create test household: " . $conn->error . "</p>";
        }
    }
    
    // Check residents
    echo "<h3>Available Residents:</h3>";
    $residents_result = $conn->query("SELECT id, first_name, last_name FROM residents LIMIT 5");
    if ($residents_result && $residents_result->num_rows > 0) {
        while ($resident = $residents_result->fetch_assoc()) {
            echo "<p>ID: {$resident['id']} | Name: {$resident['first_name']} {$resident['last_name']}</p>";
        }
    } else {
        echo "<p>No residents found.</p>";
    }
    
    // Check household_members
    echo "<h3>Current Household Members:</h3>";
    $members_result = $conn->query("SELECT * FROM household_members LIMIT 10");
    if ($members_result && $members_result->num_rows > 0) {
        while ($member = $members_result->fetch_assoc()) {
            echo "<p>Member ID: {$member['id']} | Household: {$member['household_id']} | Name: {$member['full_name']} | Relation: {$member['relation_to_head']}</p>";
        }
    } else {
        echo "<p>No household members found.</p>";
    }
    ?>
    
</body>
</html>