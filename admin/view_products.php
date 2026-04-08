<?php
require_once '../components/connect.php';

$status_filter = isset($_GET['status']) ? $_GET['status'] : '';

// Auth check
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Handle Delete
if (isset($_POST['delete_product'])) {
    $pid = filter_var($_POST['product_id'], FILTER_SANITIZE_SPECIAL_CHARS);

    $stmt = $conn->prepare("SELECT image FROM products WHERE id = ?");
    $stmt->execute([$pid]);
    $product = $stmt->fetch();

    if ($product) {
        $img_path = '../uploads/' . $product['image'];
        if ($product['image'] && file_exists($img_path)) {
            unlink($img_path);
        }
        $del = $conn->prepare("DELETE FROM products WHERE id = ?");
        $del->execute([$pid]);
        $success_msg[] = 'Product deleted successfully.';
    } else {
        $warning_msg[] = 'Product not found.';
    }
}

if ($status_filter == 'active' || $status_filter == 'deactive') {
    $stmt = $conn->prepare("SELECT * FROM products WHERE status = ? ORDER BY id DESC");
    $stmt->execute([$status_filter]);
} else {
    $stmt = $conn->prepare("SELECT * FROM products ORDER BY id DESC");
    $stmt->execute();
}

$products = $stmt->fetchAll();

include '../components/admin_header.php';
?>

<div class="page-header">
    <div class="title2">
        <a href="dashboard.php">Dashboard</a>
        <i class='bx bx-chevron-right'></i>
        <span>View Products</span>
    </div>
    <div style="display:flex; align-items:center; justify-content:space-between;">
        <h2 class="heading">All Products</h2>
        <a href="add_product.php" class="btn" style="margin-left:auto;">
            <i class='bx bx-plus'></i> Add Product
        </a>
    </div>
</div>

<?php if (empty($products)): ?>
    <div class="empty-state">
        <i class='bx bx-package'></i>
        <h3>No Products Found</h3>
        <p>Start by adding your first fabric product.</p>
        <a href="add_product.php" class="btn">Add Product</a>
    </div>
<?php else: ?>
    <div class="product-grid">
        <?php foreach ($products as $p): ?>
            <?php
                $img_src = '../uploads/' . htmlspecialchars($p['image']);
                $img_exists = $p['image'] && file_exists('../uploads/' . $p['image']);
            ?>
            <div class="product-card">
                <div class="product-img-wrap">
                    <?php if ($img_exists): ?>
                        <img src="<?php echo $img_src; ?>" alt="<?php echo htmlspecialchars($p['name']); ?>">
                    <?php else: ?>
                        <div class="img-placeholder"><i class='bx bx-image'></i></div>
                    <?php endif; ?>
                    <span class="status-badge <?php echo $p['status'] === 'active' ? 'badge-active' : 'badge-deactive'; ?>">
                        <?php echo ucfirst(htmlspecialchars($p['status'])); ?>
                    </span>
                </div>
                <div class="product-info">
                    <h4 class="product-name"><?php echo htmlspecialchars($p['name']); ?></h4>
                    <p class="product-price">&#8377;<?php echo number_format($p['price']); ?></p>
                </div>
                <div class="product-actions">
                    <a href="read_product.php?post_id=<?php echo urlencode($p['id']); ?>" class="btn btn-sm btn-outline">
                        <i class='bx bx-show'></i> View
                    </a>
                    <a href="edit_product.php?id=<?php echo urlencode($p['id']); ?>" class="btn btn-sm">
                        <i class='bx bx-edit'></i> Edit
                    </a>
                    <form method="POST" action="" style="display:inline;">
                        <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($p['id']); ?>">
                        <button type="submit" name="delete_product" class="btn btn-sm btn-danger"
                                onclick="return confirm('Are you sure you want to delete this product? This action cannot be undone.')">
                            <i class='bx bx-trash'></i> Delete
                        </button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

</div><!-- end .main-content -->
<?php include '../components/alert.php'; ?>
</body>
</html>