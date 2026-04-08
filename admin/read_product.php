<?php
require_once '../components/connect.php';

// Auth check
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

$post_id = isset($_GET['post_id']) ? filter_var($_GET['post_id'], FILTER_SANITIZE_SPECIAL_CHARS) : '';
if (empty($post_id)) {
    header('Location: view_products.php');
    exit();
}

$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$post_id]);
$product = $stmt->fetch();

if (!$product) {
    $_SESSION['warning_msg'] = 'Product not found.';
    header('Location: view_products.php');
    exit();
}

include '../components/admin_header.php';
?>

<div class="page-header">
    <div class="title2">
        <a href="dashboard.php">Dashboard</a>
        <i class='bx bx-chevron-right'></i>
        <a href="view_products.php">View Products</a>
        <i class='bx bx-chevron-right'></i>
        <span>Product Detail</span>
    </div>
    <h2 class="heading">Product Detail</h2>
</div>

<div class="read-product-wrap">
    <div class="read-product-img">
        <?php if ($product['image'] && file_exists('../uploads/' . $product['image'])): ?>
            <img src="../uploads/<?php echo htmlspecialchars($product['image']); ?>"
                 alt="<?php echo htmlspecialchars($product['name']); ?>">
        <?php else: ?>
            <div class="img-placeholder large"><i class='bx bx-image'></i><p>No Image</p></div>
        <?php endif; ?>
    </div>

    <div class="read-product-info">
        <span class="status-badge <?php echo $product['status'] === 'active' ? 'badge-active' : 'badge-deactive'; ?>" style="font-size:14px; padding:6px 16px;">
            <?php echo ucfirst(htmlspecialchars($product['status'])); ?>
        </span>

        <h2 class="read-product-name"><?php echo htmlspecialchars($product['name']); ?></h2>

        <div class="read-product-price">
            <i class='bx bx-rupee'></i>
            <span><?php echo number_format($product['price']); ?></span>
        </div>

        <div class="read-product-detail">
            <h4>Description</h4>
            <p><?php echo nl2br(htmlspecialchars($product['product_detail'])); ?></p>
        </div>

        <div class="read-product-meta">
            <span><strong>Product ID:</strong> <?php echo htmlspecialchars($product['id']); ?></span>
        </div>

        <div class="form-actions" style="margin-top:24px;">
            <a href="view_products.php" class="btn btn-outline">
                <i class='bx bx-arrow-back'></i> Back to Products
            </a>
            <a href="edit_product.php?id=<?php echo urlencode($product['id']); ?>" class="btn">
                <i class='bx bx-edit'></i> Edit Product
            </a>
        </div>
    </div>
</div>

</div><!-- end .main-content -->
<?php include '../components/alert.php'; ?>
</body>
</html>