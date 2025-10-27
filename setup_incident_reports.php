<?php
// Script to create incident_reports table if it doesn't exist
include 'config.php';

// Check if table exists
$tableCheck = $conn->query("SHOW TABLES LIKE 'incident_reports'");

if ($tableCheck->num_rows == 0) {
    echo "Table 'incident_reports' doesn't exist. Creating it now...<br>";
    
    $createTableSQL = "
    CREATE TABLE `incident_reports` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `secretary_name` varchar(255) NOT NULL,
      `content` longtext NOT NULL,
      `user_id` int(11) NOT NULL,
      `status` varchar(50) NOT NULL DEFAULT 'Pending',
      `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
      `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
      PRIMARY KEY (`id`),
      KEY `user_id` (`user_id`),
      CONSTRAINT `incident_reports_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
    ";
    
    if ($conn->query($createTableSQL)) {
        echo "✅ Table 'incident_reports' created successfully!<br>";
        
        // Test insert some sample data
        echo "Inserting sample data...<br>";
        $sampleSQL = "
        INSERT INTO `incident_reports` (`secretary_name`, `content`, `user_id`, `status`) VALUES 
        ('John Secretary', '<p>Sample incident report content</p>', 1, 'Pending'),
        ('Jane Secretary', '<p>Another incident report</p>', 1, 'Under Review');
        ";
        
        if ($conn->query($sampleSQL)) {
            echo "✅ Sample data inserted successfully!<br>";
        } else {
            echo "❌ Error inserting sample data: " . $conn->error . "<br>";
        }
        
    } else {
        echo "❌ Error creating table: " . $conn->error . "<br>";
    }
} else {
    echo "✅ Table 'incident_reports' already exists.<br>";
    
    // Check count
    $countResult = $conn->query("SELECT COUNT(*) as total FROM incident_reports");
    if ($countResult) {
        $count = $countResult->fetch_assoc()['total'];
        echo "Total incident reports: $count<br>";
        
        if ($count == 0) {
            echo "Table is empty. Adding sample data...<br>";
            $sampleSQL = "
            INSERT INTO `incident_reports` (`secretary_name`, `content`, `user_id`, `status`) VALUES 
            ('John Secretary', '<p>Sample incident report content</p>', 1, 'Pending'),
            ('Jane Secretary', '<p>Another incident report</p>', 1, 'Under Review');
            ";
            
            if ($conn->query($sampleSQL)) {
                echo "✅ Sample data inserted successfully!<br>";
            } else {
                echo "❌ Error inserting sample data: " . $conn->error . "<br>";
            }
        }
    } else {
        echo "❌ Error querying table: " . $conn->error . "<br>";
    }
}

echo "<br><a href='dashboard.php'>Go back to Dashboard</a>";
$conn->close();
?>

<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
</style>