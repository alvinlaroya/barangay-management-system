<?php
if (session_status() === PHP_SESSION_NONE) session_start();

$userName = $_SESSION['name'] ?? 'User';
$userRole = $_SESSION['role'] ?? '';

$base = '/barangay_system'; // Adjust to match your folder name
$currentPage = basename($_SERVER['SCRIPT_NAME']);
?>

<!-- Bootstrap 5 Navbar -->
<nav class="navbar navbar-expand-lg navbar-light shadow-sm" style="background-color: #ffffff; border-bottom: 1px solid #dee2e6;">
  <div class="container-fluid">
    <a class="navbar-brand fw-semibold text-primary" href="<?= $base ?>/dashboard.php">
      <i class="bi bi-house-door-fill me-1"></i> Barangay System
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <?php if (in_array($userRole, ['admin', 'staff'])): ?>
          <li class="nav-item">
            <a class="nav-link <?= $currentPage === 'dashboard.php' ? 'active fw-bold text-primary' : '' ?>" href="<?= $base ?>/dashboard.php">Dashboard</a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= strpos($currentPage, 'residents') === 0 ? 'active fw-bold text-primary' : '' ?>" href="<?= $base ?>/residents/index.php">Residents</a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= strpos($currentPage, 'households') === 0 ? 'active fw-bold text-primary' : '' ?>" href="<?= $base ?>/households/index.php">Households</a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= strpos($currentPage, 'clearance') === 0 ? 'active fw-bold text-primary' : '' ?>" href="<?= $base ?>/clearance/index.php">Clearances</a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= strpos($currentPage, 'blotter') === 0 ? 'active fw-bold text-primary' : '' ?>" href="<?= $base ?>/blotter/index.php">Blotter</a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= strpos($currentPage, 'officials') === 0 ? 'active fw-bold text-primary' : '' ?>" href="<?= $base ?>/officials/index.php">Officials</a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= $currentPage === 'reports.php' ? 'active fw-bold text-primary' : '' ?>" href="<?= $base ?>/reports.php">Reports</a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= $currentPage === 'settings.php' ? 'active fw-bold text-primary' : '' ?>" href="<?= $base ?>/settings.php">Settings</a>
          </li>
        <?php elseif ($userRole === 'resident'): ?>
          <li class="nav-item">
            <a class="nav-link <?= $currentPage === 'resident_dashboard.php' ? 'active fw-bold text-primary' : '' ?>" href="<?= $base ?>/resident_dashboard.php">Home</a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= $currentPage === 'resident_requests.php' ? 'active fw-bold text-primary' : '' ?>" href="<?= $base ?>/resident_requests.php">My Requests</a>
          </li>
        <?php endif; ?>
      </ul>

      <div class="d-flex align-items-center">
        <span class="me-3 text-muted small">Welcome, <strong><?= htmlspecialchars($userName) ?></strong></span>
        <a href="<?= $base ?>/logout.php" class="btn btn-sm btn-outline-primary">
          <i class="bi bi-box-arrow-right me-1"></i> Logout
        </a>
      </div>
    </div>
  </div>
</nav>

<!-- Include Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
