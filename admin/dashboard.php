<?php
require_once '../components/connect.php';

// Auth check
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Fetch dashboard counts
$stats = [];

// Total products
$stmt = $conn->prepare("SELECT COUNT(*) AS cnt FROM products");
$stmt->execute();
$stats['total_products'] = $stmt->fetch()['cnt'];

// Active products
$stmt = $conn->prepare("SELECT COUNT(*) AS cnt FROM products WHERE status = 'active'");
$stmt->execute();
$stats['active_products'] = $stmt->fetch()['cnt'];

// Deactive products
$stmt = $conn->prepare("SELECT COUNT(*) AS cnt FROM products WHERE status = 'deactive'");
$stmt->execute();
$stats['deactive_products'] = $stmt->fetch()['cnt'];

// Registered users
$stmt = $conn->prepare("SELECT COUNT(*) AS cnt FROM users");
$stmt->execute();
$stats['total_users'] = $stmt->fetch()['cnt'];

// Registered admins
$stmt = $conn->prepare("SELECT COUNT(*) AS cnt FROM admin");
$stmt->execute();
$stats['total_admins'] = $stmt->fetch()['cnt'];

// Messages
$stmt = $conn->prepare("SELECT COUNT(*) AS cnt FROM message");
$stmt->execute();
$stats['total_messages'] = $stmt->fetch()['cnt'];

// Total orders
$stmt = $conn->prepare("SELECT COUNT(*) AS cnt FROM orders");
$stmt->execute();
$stats['total_orders'] = $stmt->fetch()['cnt'];

// Confirmed orders
$stmt = $conn->prepare("SELECT COUNT(*) AS cnt FROM orders WHERE status = 'in progress'");
$stmt->execute();
$stats['confirmed_orders'] = $stmt->fetch()['cnt'];

// Cancelled orders
$stmt = $conn->prepare("SELECT COUNT(*) AS cnt FROM orders WHERE status = 'canceled'");
$stmt->execute();
$stats['cancelled_orders'] = $stmt->fetch()['cnt'];

// Admin name for welcome
$stmt = $conn->prepare("SELECT name FROM admin WHERE id = ?");
$stmt->execute([$_SESSION['admin_id']]);
$admin_row = $stmt->fetch();
$admin_display_name = $admin_row ? htmlspecialchars($admin_row['name']) : 'Admin';

include '../components/admin_header.php';
?>

<div class="page-header">
    <div class="title2">
        <a href="dashboard.php">Dashboard</a>
        <i class='bx bx-chevron-right'></i>
        <span>Overview</span>
    </div>
    <h2 class="heading">Dashboard</h2>
</div>

<div class="stats-grid">

    <!-- Welcome Box -->
    <div class="stat-card welcome-card">
        <div class="stat-icon"><i class='bx bx-smile'></i></div>
        <div class="stat-info">
            <h3>Hello, <?php echo $admin_display_name; ?>!</h3>
            <p>Welcome to your admin panel</p>
        </div>
        <a href="dashboard.php" class="btn btn-outline">My Profile</a>
    </div>

    <!-- Total Products -->
    <div class="stat-card">
        <div class="stat-icon products-icon"><i class='bx bx-package'></i></div>
        <div class="stat-info">
            <h3><?php echo $stats['total_products']; ?></h3>
            <p>Total Products</p>
        </div>
        <a href="view_products.php" class="btn">View All</a>
    </div>

    <!-- Active Products -->
    <div class="stat-card">
        <div class="stat-icon active-icon"><i class='bx bx-check-circle'></i></div>
        <div class="stat-info">
            <h3><?php echo $stats['active_products']; ?></h3>
            <p>Active Products</p>
        </div>
        <a href="view_products.php?status=active" class="btn btn-success">View</a>
    </div>

    <!-- Deactive Products -->
    <div class="stat-card">
        <div class="stat-icon deactive-icon"><i class='bx bx-x-circle'></i></div>
        <div class="stat-info">
            <h3><?php echo $stats['deactive_products']; ?></h3>
            <p>Deactive Products</p>
        </div>
        <a href="view_products.php?status=deactive" class="btn btn-danger">View</a>
    </div>

    <!-- Registered Users -->
    <div class="stat-card">
        <div class="stat-icon users-icon"><i class='bx bx-group'></i></div>
        <div class="stat-info">
            <h3><?php echo $stats['total_users']; ?></h3>
            <p>Registered Users</p>
        </div>
        <a href="users.php" class="btn">View All</a>
    </div>

    <!-- Registered Admins -->
    <div class="stat-card">
        <div class="stat-icon admin-icon"><i class='bx bx-shield'></i></div>
        <div class="stat-info">
            <h3><?php echo $stats['total_admins']; ?></h3>
            <p>Registered Admins</p>
        </div>
        <a href="register.php" class="btn">Add Admin</a>
    </div>

    <!-- Messages -->
    <div class="stat-card">
        <div class="stat-icon msg-icon"><i class='bx bx-message-dots'></i></div>
        <div class="stat-info">
            <h3><?php echo $stats['total_messages']; ?></h3>
            <p>Total Messages</p>
        </div>
        <a href="messages.php" class="btn">View All</a>
    </div>

    <!-- Total Orders -->
    <div class="stat-card">
        <div class="stat-icon orders-icon"><i class='bx bx-cart-alt'></i></div>
        <div class="stat-info">
            <h3><?php echo $stats['total_orders']; ?></h3>
            <p>Total Orders</p>
        </div>
        <a href="orders.php" class="btn">View All</a>
    </div>

    <!-- Confirmed Orders -->
    <div class="stat-card">
        <div class="stat-icon confirmed-icon"><i class='bx bx-badge-check'></i></div>
        <div class="stat-info">
            <h3><?php echo $stats['confirmed_orders']; ?></h3>
            <p>Confirmed Orders</p>
        </div>
        <a href="orders.php" class="btn btn-success">View</a>
    </div>

    <!-- Cancelled Orders -->
    <div class="stat-card">
        <div class="stat-icon cancelled-icon"><i class='bx bx-error-circle'></i></div>
        <div class="stat-info">
            <h3><?php echo $stats['cancelled_orders']; ?></h3>
            <p>Cancelled Orders</p>
        </div>
        <a href="orders.php" class="btn btn-danger">View</a>
    </div>

</div>

</div><!-- end .main-content -->
<?php include '../components/alert.php'; ?>
</body>
</html>
