<?php
// Fetch admin info for display
$admin_id = $_SESSION['admin_id'];
$stmt = $conn->prepare("SELECT * FROM admin WHERE id = ?");
$stmt->execute([$admin_id]);
$admin_info = $stmt->fetch();
$admin_name    = $admin_info ? htmlspecialchars($admin_info['name']) : 'Admin';
$admin_profile = $admin_info && $admin_info['profile'] ? $admin_info['profile'] : '';

// Determine current page for active nav highlight
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fabric Store Admin</title>
    <!-- Boxicons CDN -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Admin CSS -->
    <link rel="stylesheet" href="<?php echo (strpos($current_page, 'admin') !== false || dirname($_SERVER['PHP_SELF']) !== '/') ? '' : 'admin/'; ?>admin_style.css">
    <!-- Inline path fix for pages in /admin/ -->
    <?php
    $css_path = '../admin/admin_style.css';
    // Check if we are inside /admin/ folder
    if (strpos($_SERVER['SCRIPT_FILENAME'], DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR) !== false) {
        $css_path = 'admin_style.css';
    }
    ?>
    <link rel="stylesheet" href="<?php echo $css_path; ?>">
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <div class="sidebar-brand">
        <i class='bx bx-store-alt'></i>
        <span>Fabric Store</span>
    </div>

    <div class="admin-profile">
        <?php if ($admin_profile && file_exists(__DIR__ . '/../uploads/' . $admin_profile)): ?>
            <img src="<?php
                $depth = (strpos($_SERVER['SCRIPT_FILENAME'], DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR) !== false) ? '../' : '';
                echo $depth . 'uploads/' . htmlspecialchars($admin_profile);
            ?>" alt="Profile" class="profile-img">
        <?php else: ?>
            <div class="profile-placeholder"><i class='bx bx-user'></i></div>
        <?php endif; ?>
        <div class="admin-info">
            <span class="admin-label">Welcome back,</span>
            <span class="admin-name"><?php echo $admin_name; ?></span>
        </div>
    </div>

    <nav class="sidebar-nav">
        <a href="dashboard.php" class="nav-link <?php echo $current_page === 'dashboard.php' ? 'active' : ''; ?>">
            <i class='bx bxs-dashboard'></i>
            <span>Dashboard</span>
        </a>
        <a href="add_product.php" class="nav-link <?php echo $current_page === 'add_product.php' ? 'active' : ''; ?>">
            <i class='bx bx-plus-circle'></i>
            <span>Add Product</span>
        </a>
        <a href="view_products.php" class="nav-link <?php echo $current_page === 'view_products.php' ? 'active' : ''; ?>">
            <i class='bx bx-package'></i>
            <span>View Products</span>
        </a>
        <a href="orders.php" class="nav-link <?php echo $current_page === 'orders.php' ? 'active' : ''; ?>">
            <i class='bx bx-cart-alt'></i>
            <span>Orders</span>
        </a>
        <a href="messages.php" class="nav-link <?php echo $current_page === 'messages.php' ? 'active' : ''; ?>">
            <i class='bx bx-message-dots'></i>
            <span>Messages</span>
        </a>
        <a href="users.php" class="nav-link <?php echo $current_page === 'users.php' ? 'active' : ''; ?>">
            <i class='bx bx-group'></i>
            <span>Users</span>
        </a>
    </nav>

    <div class="sidebar-footer">
        <a href="admin_logout.php" class="nav-link logout-link">
            <i class='bx bx-log-out'></i>
            <span>Logout</span>
        </a>
    </div>
</div>

<!-- Main Content Wrapper -->
<div class="main-content">