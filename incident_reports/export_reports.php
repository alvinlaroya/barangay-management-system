<?php
session_start();
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'staff'])) {
    header("Location: ../login.php");
    exit();
}

include '../config.php';

// Set headers for CSV download
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="incident_reports_' . date('Y-m-d_H-i-s') . '.csv"');

// Open output stream
$output = fopen('php://output', 'w');

// Add CSV header
fputcsv($output, [
    'Report ID',
    'Reporter Name',
    'Email',
    'Contact',
    'Purok',
    'Address',
    'Secretary Name',
    'Status',
    'Content (Plain Text)',
    'Submitted Date',
    'Last Updated'
]);

// Fetch all incident reports with user information
$query = "SELECT ir.*, 
                 u.name as user_name, 
                 u.username as username,
                 u.contact as user_contact,
                 r.first_name as resident_first_name,
                 r.middle_name as resident_middle_name,
                 r.last_name as resident_last_name,
                 r.email as resident_email,
                 r.address as resident_address,
                 r.purok as resident_purok,
                 r.contact as resident_contact
          FROM incident_reports ir 
          LEFT JOIN users u ON ir.user_id = u.id 
          LEFT JOIN residents r ON u.resident_id = r.id
          ORDER BY ir.created_at DESC";

$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    while ($report = $result->fetch_assoc()) {
        // Determine reporter name
        $reporterName = $report['resident_first_name'] ? 
            trim($report['resident_first_name'] . ' ' . ($report['resident_middle_name'] ?? '') . ' ' . $report['resident_last_name']) :
            $report['user_name'];
        
        // Determine email and contact
        $reporterEmail = $report['resident_email'] ?: 'N/A';
        $reporterContact = $report['resident_contact'] ?: ($report['user_contact'] ?: 'N/A');
        
        // Clean content (remove HTML tags)
        $cleanContent = strip_tags($report['content']);
        $cleanContent = html_entity_decode($cleanContent);
        $cleanContent = preg_replace('/\s+/', ' ', trim($cleanContent)); // Clean up whitespace
        
        // Add row to CSV
        fputcsv($output, [
            $report['id'],
            $reporterName,
            $reporterEmail,
            $reporterContact,
            $report['resident_purok'] ?? '',
            $report['resident_address'] ?? '',
            $report['secretary_name'],
            $report['status'],
            $cleanContent,
            $report['created_at'],
            $report['updated_at']
        ]);
    }
}

// Close output stream
fclose($output);
exit();
?>