<?php
// Debug script to check residents in the database
include 'config.php';

echo "<h3>Residents Debug Information</h3>";

// Check if residents table exists
$tableCheck = $conn->query("SHOW TABLES LIKE 'residents'");
if ($tableCheck->num_rows > 0) {
    echo "<p>✅ Table 'residents' exists</p>";
    
    // Check total residents count
    $countResult = $conn->query("SELECT COUNT(*) as total FROM residents");
    $count = $countResult->fetch_assoc()['total'];
    echo "<p><strong>Total residents: $count</strong></p>";
    
    if ($count > 0) {
        echo "<h4>Sample Residents:</h4>";
        $sampleData = $conn->query("SELECT id, first_name, middle_name, last_name, gender, occupation FROM residents LIMIT 5");
        echo "<table border='1'>";
        echo "<tr><th>ID</th><th>Name</th><th>Gender</th><th>Occupation</th></tr>";
        while ($row = $sampleData->fetch_assoc()) {
            $full_name = trim($row['first_name'] . ' ' . ($row['middle_name'] ? $row['middle_name'] . ' ' : '') . $row['last_name']);
            echo "<tr>";
            echo "<td>" . $row['id'] . "</td>";
            echo "<td>" . htmlspecialchars($full_name) . "</td>";
            echo "<td>" . $row['gender'] . "</td>";
            echo "<td>" . htmlspecialchars($row['occupation']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>❌ No residents found in the database</p>";
        echo "<p><a href='residents/add.php'>Add a resident first</a></p>";
    }
} else {
    echo "<p>❌ Table 'residents' does NOT exist</p>";
}

// Check household_members table
echo "<h4>Household Members Information:</h4>";
$householdCheck = $conn->query("SHOW TABLES LIKE 'household_members'");
if ($householdCheck->num_rows > 0) {
    echo "<p>✅ Table 'household_members' exists</p>";
    
    // Check if resident_id column exists
    $columnCheck = $conn->query("SHOW COLUMNS FROM household_members LIKE 'resident_id'");
    if ($columnCheck->num_rows > 0) {
        echo "<p>✅ Column 'resident_id' exists in household_members</p>";
    } else {
        echo "<p>❌ Column 'resident_id' does NOT exist in household_members</p>";
        echo "<p><a href='setup_household_migration.php'>Run migration to add resident_id column</a></p>";
    }
    
    $memberCount = $conn->query("SELECT COUNT(*) as total FROM household_members")->fetch_assoc()['total'];
    echo "<p>Total household members: $memberCount</p>";
} else {
    echo "<p>❌ Table 'household_members' does NOT exist</p>";
}

$conn->close();
?>

<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    table { border-collapse: collapse; margin: 10px 0; }
    th, td { padding: 8px; text-align: left; }
    th { background-color: #f2f2f2; }
</style>