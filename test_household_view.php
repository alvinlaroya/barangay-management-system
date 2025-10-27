<?php
include 'config.php';

$household_id = 2; // Test with household ID 2

echo "<h2>Testing Household View for ID: $household_id</h2>";

// Test the exact query used in view.php
$members_stmt = $conn->prepare("
    SELECT hm.id, 
           hm.household_id,
           hm.resident_id,
           hm.full_name, 
           hm.birthdate, 
           hm.gender, 
           hm.relation_to_head, 
           hm.occupation,
           r.first_name as resident_first_name, 
           r.middle_name as resident_middle_name, 
           r.last_name as resident_last_name,
           r.contact as resident_contact,
           r.email as resident_email
    FROM household_members hm 
    LEFT JOIN residents r ON hm.resident_id = r.id 
    WHERE hm.household_id = ? 
    ORDER BY 
        CASE hm.relation_to_head 
            WHEN 'Head' THEN 1 
            WHEN 'Spouse' THEN 2 
            ELSE 3 
        END, 
        hm.full_name
");

$members_stmt->bind_param("i", $household_id);
$members_stmt->execute();
$members_result = $members_stmt->get_result();

echo "<p>Query executed successfully!</p>";
echo "<p>Members found: " . $members_result->num_rows . "</p>";

if ($members_result->num_rows > 0) {
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>Full Name</th><th>Resident ID</th><th>Resident Name</th><th>Relation</th><th>Gender</th><th>Contact</th></tr>";
    
    while ($member = $members_result->fetch_assoc()) {
        $is_resident = !empty($member['resident_id']);
        $display_name = $is_resident && !empty($member['resident_first_name']) ? 
            trim($member['resident_first_name'] . ' ' . ($member['resident_middle_name'] ? $member['resident_middle_name'] . ' ' : '') . $member['resident_last_name']) :
            $member['full_name'];
            
        echo "<tr>";
        echo "<td>{$member['id']}</td>";
        echo "<td>{$member['full_name']}</td>";
        echo "<td>{$member['resident_id']}</td>";
        echo "<td>$display_name</td>";
        echo "<td>{$member['relation_to_head']}</td>";
        echo "<td>{$member['gender']}</td>";
        echo "<td>" . ($member['resident_contact'] ?: 'N/A') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No members found for this household.</p>";
}

echo "<br><p><strong>✅ Household members fetch test completed successfully!</strong></p>";
?>