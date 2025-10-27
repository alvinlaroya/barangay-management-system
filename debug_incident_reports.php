<?php
// Debug script to check incident_reports table
include 'config.php';

echo "<h3>Incident Reports Debug Information</h3>";

// Check if table exists
$tableCheck = $conn->query("SHOW TABLES LIKE 'incident_reports'");
if ($tableCheck->num_rows > 0) {
    echo "<p>✅ Table 'incident_reports' exists</p>";
    
    // Check table structure
    echo "<h4>Table Structure:</h4>";
    $structure = $conn->query("DESCRIBE incident_reports");
    echo "<table border='1'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while ($row = $structure->fetch_assoc()) {
        echo "<tr>";
        foreach ($row as $value) {
            echo "<td>" . ($value ?? 'NULL') . "</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
    
    // Check data count
    $countResult = $conn->query("SELECT COUNT(*) as total FROM incident_reports");
    $count = $countResult->fetch_assoc()['total'];
    echo "<p><strong>Total records: $count</strong></p>";
    
    // Show sample data if any exists
    if ($count > 0) {
        echo "<h4>Sample Data:</h4>";
        $sampleData = $conn->query("SELECT * FROM incident_reports LIMIT 5");
        echo "<table border='1'>";
        echo "<tr><th>ID</th><th>Secretary Name</th><th>User ID</th><th>Status</th><th>Created At</th></tr>";
        while ($row = $sampleData->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['id'] . "</td>";
            echo "<td>" . htmlspecialchars($row['secretary_name']) . "</td>";
            echo "<td>" . $row['user_id'] . "</td>";
            echo "<td>" . $row['status'] . "</td>";
            echo "<td>" . $row['created_at'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Check status distribution
        echo "<h4>Status Distribution:</h4>";
        $statusResult = $conn->query("SELECT status, COUNT(*) as count FROM incident_reports GROUP BY status");
        echo "<ul>";
        while ($row = $statusResult->fetch_assoc()) {
            echo "<li>" . $row['status'] . ": " . $row['count'] . "</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>❌ Table exists but has no data</p>";
    }
    
} else {
    echo "<p>❌ Table 'incident_reports' does NOT exist</p>";
    echo "<p>Please run the database migration to create the table.</p>";
}

// Check if the database connection is working
echo "<h4>Database Connection:</h4>";
echo "<p>Database Name: " . $conn->select_db('barangay_system') . "</p>";

$conn->close();
?>

<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    table { border-collapse: collapse; margin: 10px 0; }
    th, td { padding: 8px; text-align: left; }
    th { background-color: #f2f2f2; }
</style>