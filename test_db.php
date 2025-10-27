<?php
include 'config.php';

echo "<h3>Database Connection Test</h3>";

// Test connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "✅ Database connected successfully<br>";

// Test household_members table
$table_test = $conn->query("SHOW TABLES LIKE 'household_members'");
if ($table_test && $table_test->num_rows > 0) {
    echo "✅ household_members table exists<br>";
    
    // Show table structure
    echo "<h4>Table Structure:</h4>";
    $structure = $conn->query("DESCRIBE household_members");
    echo "<table border='1'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    while ($col = $structure->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$col['Field']}</td>";
        echo "<td>{$col['Type']}</td>";
        echo "<td>{$col['Null']}</td>";
        echo "<td>{$col['Key']}</td>";
        echo "<td>{$col['Default']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Show existing records count
    $count_result = $conn->query("SELECT COUNT(*) as total FROM household_members");
    $count = $count_result->fetch_assoc()['total'];
    echo "<br>Current records in household_members: $count<br>";
    
} else {
    echo "❌ household_members table does not exist<br>";
}

// Test residents table
$residents_test = $conn->query("SHOW TABLES LIKE 'residents'");
if ($residents_test && $residents_test->num_rows > 0) {
    echo "✅ residents table exists<br>";
    
    $residents_count = $conn->query("SELECT COUNT(*) as total FROM residents");
    $residents_total = $residents_count->fetch_assoc()['total'];
    echo "Total residents: $residents_total<br>";
    
    if ($residents_total > 0) {
        echo "<h4>Sample Residents:</h4>";
        $sample_residents = $conn->query("SELECT id, first_name, last_name FROM residents LIMIT 5");
        while ($resident = $sample_residents->fetch_assoc()) {
            echo "ID: {$resident['id']}, Name: {$resident['first_name']} {$resident['last_name']}<br>";
        }
    }
    
} else {
    echo "❌ residents table does not exist<br>";
}

// Test households table
$households_test = $conn->query("SHOW TABLES LIKE 'households'");
if ($households_test && $households_test->num_rows > 0) {
    echo "✅ households table exists<br>";
    
    $households_count = $conn->query("SELECT COUNT(*) as total FROM households");
    $households_total = $households_count->fetch_assoc()['total'];
    echo "Total households: $households_total<br>";
    
} else {
    echo "❌ households table does not exist<br>";
}

$conn->close();
?>