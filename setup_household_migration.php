<?php
// Script to add resident_id column to household_members table
include 'config.php';

echo "<h3>Household Members Migration</h3>";

// Check if resident_id column already exists
$columnCheck = $conn->query("SHOW COLUMNS FROM household_members LIKE 'resident_id'");

if ($columnCheck->num_rows == 0) {
    echo "Adding resident_id column to household_members table...<br>";
    
    $alterSQL = "
    ALTER TABLE `household_members` 
    ADD COLUMN `resident_id` int(11) DEFAULT NULL AFTER `household_id`,
    ADD KEY `resident_id` (`resident_id`),
    ADD CONSTRAINT `household_members_resident_fk` FOREIGN KEY (`resident_id`) REFERENCES `residents` (`id`) ON DELETE SET NULL;
    ";
    
    if ($conn->query($alterSQL)) {
        echo "✅ Successfully added resident_id column and foreign key constraint!<br>";
    } else {
        echo "❌ Error adding resident_id column: " . $conn->error . "<br>";
    }
} else {
    echo "✅ Column 'resident_id' already exists in household_members table.<br>";
}

// Update existing household members to link with residents where possible
echo "<br>Attempting to link existing household members with residents...<br>";

$updateQuery = "
UPDATE household_members hm 
LEFT JOIN residents r ON (
    LOWER(TRIM(hm.full_name)) = LOWER(TRIM(CONCAT(r.first_name, ' ', IFNULL(r.middle_name, ''), ' ', r.last_name)))
    OR LOWER(TRIM(hm.full_name)) = LOWER(TRIM(CONCAT(r.first_name, ' ', r.last_name)))
)
SET hm.resident_id = r.id
WHERE hm.resident_id IS NULL AND r.id IS NOT NULL;
";

$result = $conn->query($updateQuery);
if ($result) {
    $affected = $conn->affected_rows;
    echo "✅ Successfully linked $affected existing household members with residents!<br>";
} else {
    echo "❌ Error linking household members: " . $conn->error . "<br>";
}

// Show statistics
echo "<br><h4>Statistics:</h4>";
$statsQuery = "
SELECT 
    COUNT(*) as total_members,
    SUM(CASE WHEN resident_id IS NOT NULL THEN 1 ELSE 0 END) as linked_members,
    SUM(CASE WHEN resident_id IS NULL THEN 1 ELSE 0 END) as unlinked_members
FROM household_members
";
$stats = $conn->query($statsQuery)->fetch_assoc();

echo "<ul>";
echo "<li>Total household members: " . $stats['total_members'] . "</li>";
echo "<li>Linked to residents: " . $stats['linked_members'] . "</li>";
echo "<li>Manual entries (unlinked): " . $stats['unlinked_members'] . "</li>";
echo "</ul>";

echo "<br><a href='households/index.php'>Go to Households Management</a>";
$conn->close();
?>

<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
</style>