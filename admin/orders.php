<?php
require_once '../components/connect.php';

// Auth check
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Handle Delete Order
if (isset($_POST['delete_order'])) {
    $oid = filter_var($_POST['order_id'], FILTER_SANITIZE_SPECIAL_CHARS);
    $chk = $conn->prepare("SELECT id FROM orders WHERE id = ?");
    $chk->execute([$oid]);
    if ($chk->fetch()) {
        $del = $conn->prepare("DELETE FROM orders WHERE id = ?");
        $del->execute([$oid]);
        $_SESSION['success_msg'] = 'Order deleted successfully.';
    } else {
        $_SESSION['warning_msg'] = 'Order not found.';
    }
    header('Location: orders.php');
    exit();
}

// Handle Update Order + Payment Status
if (isset($_POST['update_order'])) {

    $oid = filter_var($_POST['order_id'], FILTER_SANITIZE_SPECIAL_CHARS);

    $status = isset($_POST['status']) 
        ? filter_var($_POST['status'], FILTER_SANITIZE_SPECIAL_CHARS) 
        : null;

    $payment_status = isset($_POST['payment_status']) 
        ? filter_var($_POST['payment_status'], FILTER_SANITIZE_SPECIAL_CHARS) 
        : null;

    $updated = false; // track if anything changed

    if (!empty($status)) {
        $upd = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $upd->execute([$status, $oid]);
        $updated = true;
    }

    if (!empty($payment_status)) {
        $upd = $conn->prepare("UPDATE orders SET payment_status = ? WHERE id = ?");
        $upd->execute([$payment_status, $oid]);
        $updated = true;
    }

    if ($updated) {
        $_SESSION['success_msg'] = 'Order updated successfully.';
    } else {
        $_SESSION['warning_msg'] = 'No changes were made.';
    }

    header('Location: orders.php');
    exit();
}

// Fetch all orders
$stmt = $conn->prepare("SELECT * FROM orders");
$stmt->execute();
$orders = $stmt->fetchAll();

include '../components/admin_header.php';
?>

<div class="page-header">
    <div class="title2">
        <a href="dashboard.php">Dashboard</a>
        <i class='bx bx-chevron-right'></i>
        <span>Orders</span>
    </div>
    <h2 class="heading">Order Management</h2>
</div>

<?php if (empty($orders)): ?>
    <div class="empty-state">
        <i class='bx bx-cart-alt'></i>
        <h3>No Orders Yet</h3>
        <p>Orders placed by customers will appear here.</p>
    </div>
<?php else: ?>
    <div class="orders-grid">
        <?php foreach ($orders as $order): ?>
            <div class="order-card">
                <div class="order-card-header">
                    <div>
                        <span class="order-id">#<?php echo htmlspecialchars($order['id']); ?></span>
                        <span class="order-date"><?php echo htmlspecialchars($order['date']); ?></span>
                    </div>
                    <span class="status-badge <?php echo $order['status'] === 'in progress' ? 'badge-active' : 'badge-deactive'; ?>">
                        <?php echo ucfirst(htmlspecialchars($order['status'] ?? 'Pending')); ?>
                    </span>
                </div>

                <div class="order-details-grid">
                    <div class="order-detail-item">
                        <i class='bx bx-user'></i>
                        <div>
                            <span class="detail-label">Customer</span>
                            <span class="detail-value"><?php echo htmlspecialchars($order['name']); ?></span>
                        </div>
                    </div>
                    <div class="order-detail-item">
                        <i class='bx bx-id-card'></i>
                        <div>
                            <span class="detail-label">User ID</span>
                            <span class="detail-value"><?php echo htmlspecialchars($order['user_id']); ?></span>
                        </div>
                    </div>
                    <div class="order-detail-item">
                        <i class='bx bx-phone'></i>
                        <div>
                            <span class="detail-label">Phone</span>
                            <span class="detail-value"><?php echo htmlspecialchars($order['number']); ?></span>
                        </div>
                    </div>
                    <div class="order-detail-item">
                        <i class='bx bx-envelope'></i>
                        <div>
                            <span class="detail-label">Email</span>
                            <span class="detail-value"><?php echo htmlspecialchars($order['email']); ?></span>
                        </div>
                    </div>
                    <div class="order-detail-item">
                        <i class='bx bx-rupee'></i>
                        <div>
                            <span class="detail-label">Total Price</span>
                            <span class="detail-value">&#8377;<?php echo number_format($order['price']); ?></span>
                        </div>
                    </div>
                    <div class="order-detail-item">
                        <i class='bx bx-credit-card'></i>
                        <div>
                            <span class="detail-label">Payment Method</span>
                            <span class="detail-value"><?php echo htmlspecialchars($order['method'] ?? 'N/A'); ?></span>
                        </div>
                    </div>
                    <div class="order-detail-item">
    <i class='bx bx-wallet'></i>
    <div>
        <span class="detail-label">Payment Status</span>
        <span class="detail-value">
            <?php echo ucfirst(htmlspecialchars($order['payment_status'] ?? 'pending')); ?>
        </span>
    </div>
</div>
                    <div class="order-detail-item" style="grid-column:1/-1;">
                        <i class='bx bx-map'></i>
                        <div>
                            <span class="detail-label">Address (<?php echo htmlspecialchars($order['address_type'] ?? ''); ?>)</span>
                            <span class="detail-value"><?php echo htmlspecialchars($order['address']); ?></span>
                        </div>
                    </div>
                </div>

                <form method="POST" action="" class="order-update-form">
                    <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($order['id']); ?>">
                    <div class="order-form-row">

    <!-- ORDER STATUS -->
    <select name="status" class="form-select">
        <option disabled selected>
            Order: <?php echo ucfirst(htmlspecialchars($order['status'] ?? 'pending')); ?>
        </option>
        <option value="pending">Pending</option>
        <option value="confirmed">Confirmed</option>
        <option value="shipped">Shipped</option>
        <option value="out for delivery">Out for Delivery</option>
        <option value="delivered">Delivered</option>
        <option value="canceled">Canceled</option>
        <option value="returned">Returned</option>
        <option value="refunded">Refunded</option>
    </select>

    <!-- PAYMENT STATUS -->
    <select name="payment_status" class="form-select">
        <option disabled selected>
            Payment: <?php echo ucfirst(htmlspecialchars($order['payment_status'] ?? 'pending')); ?>
        </option>
        <option value="pending">Pending</option>
        <option value="complete">Complete</option>
    </select>

    <button type="submit" name="update_order" class="btn btn-sm">
        <i class='bx bx-refresh'></i> Update
    </button>

    <button type="submit" name="delete_order" class="btn btn-sm btn-danger"
        onclick="return confirm('Delete this order permanently?')">
        <i class='bx bx-trash'></i> Delete
    </button>

</div>
                </form>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

</div><!-- end .main-content -->
<?php include '../components/alert.php'; ?>
</body>
</html>