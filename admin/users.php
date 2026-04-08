<?php
require_once '../components/connect.php';

// Auth check
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Fetch all users
$stmt = $conn->prepare("SELECT * FROM users");
$stmt->execute();
$users = $stmt->fetchAll();

include '../components/admin_header.php';
?>

<div class="page-header">
    <div class="title2">
        <a href="dashboard.php">Dashboard</a>
        <i class='bx bx-chevron-right'></i>
        <span>Users</span>
    </div>
    <h2 class="heading">Registered Users</h2>
</div>

<?php if (empty($users)): ?>
    <div class="empty-state">
        <i class='bx bx-group'></i>
        <h3>No Users Registered</h3>
        <p>Users who register on your store will appear here.</p>
    </div>
<?php else: ?>
    <div class="users-grid">
        <?php foreach ($users as $user): ?>
            <div class="user-card">
                <div class="user-avatar">
                    <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                </div>
                <div class="user-info">
                    <h4><?php echo htmlspecialchars($user['name']); ?></h4>
                    <p class="user-email">
                        <i class='bx bx-envelope'></i>
                        <?php echo htmlspecialchars($user['email']); ?>
                    </p>
                    <span class="user-id">ID: #<?php echo htmlspecialchars($user['id']); ?></span>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <p style="color: var(--text-muted); margin-top: 20px; font-size: 14px; text-align:center;">
        Total: <strong style="color:var(--text-primary)"><?php echo count($users); ?></strong> registered users
    </p>
<?php endif; ?>

</div><!-- end .main-content -->
<?php include '../components/alert.php'; ?>
</body>
</html>