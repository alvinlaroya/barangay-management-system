# Incident Reports Feature Setup

## Database Migration Required

To enable the Incident Reports feature, you need to run the following SQL command in your database:

### Option 1: Using phpMyAdmin
1. Open phpMyAdmin
2. Select your `barangay_system` database
3. Go to the SQL tab
4. Copy and paste the following SQL command:

```sql
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
```

4. Click "Go" to execute

### Option 2: Using MySQL Command Line
```bash
mysql -u your_username -p barangay_system < /opt/lampp/htdocs/barangay_system/migrations/add_incident_reports_table.sql
```

## Feature Overview

### New Features Added:

1. **Resident Incident Reports** (`/incident_reports/index.php`)
   - Rich text editor using Quill.js
   - Secretary name field
   - Content field with full HTML formatting
   - Auto-timestamp (created_at)
   - Status tracking (Pending, Under Review, Approved, Rejected)
   - View own report history
   - Modal for detailed report viewing

2. **Admin Management** (`/incident_reports/admin_index.php`)
   - View all incident reports from all residents
   - Update report status
   - Delete reports
   - DataTables integration for sorting/filtering
   - Full report content viewing
   - User information display

3. **Navigation Updates**
   - Added "Incident Reports" link to resident navbar
   - Added "Incident Reports" link to admin/staff navbar
   - Added incident report card to resident dashboard

### File Structure:
```
incident_reports/
├── index.php                  # Resident interface
├── admin_index.php           # Admin/Staff interface  
├── get_report_details.php    # Helper for resident modal
└── view_report.php           # Helper for admin modal

migrations/
└── add_incident_reports_table.sql  # Database migration
```

### Security Features:
- Session-based authentication
- Role-based access control
- SQL injection prevention with prepared statements
- XSS protection with htmlspecialchars()
- Residents can only view their own reports
- Admin/staff can view all reports

### Technologies Used:
- Quill.js for rich text editing
- Bootstrap 5 for responsive design
- DataTables for admin report management
- Font Awesome/Bootstrap Icons
- AJAX for modal content loading