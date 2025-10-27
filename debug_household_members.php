<?php
include 'config.php';

echo "<h2>Household Members Debug Script</h2>";

// Check database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "<p><strong>✅ Database connection successful</strong></p>";

// Test household_members table
echo "<h3>1. Test household_members table structure</h3>";
$structure = $conn->query("DESCRIBE household_members");
if ($structure) {
    echo "<table border='1'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while ($row = $structure->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['Field']}</td>";
        echo "<td>{$row['Type']}</td>";
        echo "<td>{$row['Null']}</td>";
        echo "<td>{$row['Key']}</td>";
        echo "<td>{$row['Default']}</td>";
        echo "<td>{$row['Extra']}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>❌ Error describing household_members table: " . $conn->error . "</p>";
}

// Count total household members
echo "<h3>2. Count all household members</h3>";
$count_result = $conn->query("SELECT COUNT(*) as total FROM household_members");
if ($count_result) {
    $count = $count_result->fetch_assoc()['total'];
    echo "<p>Total household members in database: <strong>$count</strong></p>";
} else {
    echo "<p>❌ Error counting members: " . $conn->error . "</p>";
}

// List all household members with their details
echo "<h3>3. All household members data</h3>";
$all_members = $conn->query("
    SELECT hm.id, hm.household_id, hm.resident_id, hm.full_name, hm.relation_to_head, 
           r.first_name as resident_first_name, r.last_name as resident_last_name
    FROM household_members hm 
    LEFT JOIN residents r ON hm.resident_id = r.id 
    ORDER BY hm.household_id, hm.id
");

if ($all_members && $all_members->num_rows > 0) {
    echo "<table border='1'>";
    echo "<tr><th>Member ID</th><th>Household ID</th><th>Resident ID</th><th>Full Name</th><th>Resident Name</th><th>Relation</th></tr>";
    while ($member = $all_members->fetch_assoc()) {
        $resident_name = $member['resident_first_name'] ? $member['resident_first_name'] . ' ' . $member['resident_last_name'] : 'N/A';
        echo "<tr>";
        echo "<td>{$member['id']}</td>";
        echo "<td>{$member['household_id']}</td>";
        echo "<td>{$member['resident_id']}</td>";
        echo "<td>{$member['full_name']}</td>";
        echo "<td>$resident_name</td>";
        echo "<td>{$member['relation_to_head']}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>❌ No household members found or error: " . $conn->error . "</p>";
}

// Test specific household if provided
if (isset($_GET['household_id'])) {
    $household_id = intval($_GET['household_id']);
    echo "<h3>4. Members for Household ID: $household_id</h3>";
    
    $household_members = $conn->prepare("
        SELECT hm.*, 
               r.first_name as resident_first_name, 
               r.middle_name as resident_middle_name, 
               r.last_name as resident_last_name
        FROM household_members hm 
        LEFT JOIN residents r ON hm.resident_id = r.id 
        WHERE hm.household_id = ?
    ");
    $household_members->bind_param("i", $household_id);
    $household_members->execute();
    $result = $household_members->get_result();
    
    if ($result->num_rows > 0) {
        echo "<p>Found <strong>{$result->num_rows}</strong> members for household $household_id</p>";
        echo "<table border='1'>";
        echo "<tr><th>ID</th><th>Full Name</th><th>Resident Name</th><th>Relation</th><th>Gender</th><th>Occupation</th></tr>";
        while ($member = $result->fetch_assoc()) {
            $resident_name = $member['resident_first_name'] ? 
                trim($member['resident_first_name'] . ' ' . ($member['resident_middle_name'] ? $member['resident_middle_name'] . ' ' : '') . $member['resident_last_name']) : 
                'Manual Entry';
            echo "<tr>";
            echo "<td>{$member['id']}</td>";
            echo "<td>{$member['full_name']}</td>";
            echo "<td>$resident_name</td>";
            echo "<td>{$member['relation_to_head']}</td>";
            echo "<td>{$member['gender']}</td>";
            echo "<td>{$member['occupation']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>❌ No members found for household $household_id</p>";
    }
}

// List all households
echo "<h3>5. All Households</h3>";
$households = $conn->query("SELECT id, household_no, head_of_family FROM households ORDER BY id");
if ($households && $households->num_rows > 0) {
    echo "<p>Available households to test:</p><ul>";
    while ($household = $households->fetch_assoc()) {
        echo "<li><a href='?household_id={$household['id']}'>Household #{$household['household_no']} - {$household['head_of_family']}</a></li>";
    }
    echo "</ul>";
} else {
    echo "<p>❌ No households found</p>";
}
?>