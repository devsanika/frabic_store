<?php
require_once '../components/connect.php';

// Auth check
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Get product ID
$pid = isset($_GET['id']) ? filter_var($_GET['id'], FILTER_SANITIZE_SPECIAL_CHARS) : '';
if (empty($pid)) {
    header('Location: view_products.php');
    exit();
}

// Fetch product
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$pid]);
$product = $stmt->fetch();

if (!$product) {
    $_SESSION['warning_msg'] = 'Product not found.';
    header('Location: view_products.php');
    exit();
}

// Handle Update
if (isset($_POST['update'])) {
    $name   = filter_var(trim($_POST['name']), FILTER_SANITIZE_SPECIAL_CHARS);
    $price  = filter_var($_POST['price'], FILTER_SANITIZE_NUMBER_INT);
    $detail = filter_var(trim($_POST['product_detail']), FILTER_SANITIZE_SPECIAL_CHARS);
    $status = filter_var($_POST['status'], FILTER_SANITIZE_SPECIAL_CHARS);

    if (empty($name) || empty($price) || empty($detail)) {
        $warning_msg[] = 'Please fill in all required fields.';
    } else {
        $new_image = $product['image']; // keep old image by default

        // Handle new image upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $original = $_FILES['image']['name'];
            $ext      = strtolower(pathinfo($original, PATHINFO_EXTENSION));
            $allowed  = ['jpg', 'jpeg', 'png'];
            $size     = $_FILES['image']['size'];

            if (!in_array($ext, $allowed)) {
                $warning_msg[] = 'Image must be JPG, JPEG, or PNG format only.';
            } elseif ($size > 2000000) {
                $warning_msg[] = 'Image size must not exceed 2MB.';
            } else {
                // Check duplicate name (excluding current product's image)
                $check = $conn->prepare("SELECT id FROM products WHERE image = ? AND id != ?");
                $check->execute([$original, $pid]);
                if ($check->fetch()) {
                    $warning_msg[] = 'An image with this filename already exists. Please rename the file.';
                } else {
                    // Delete old image if different
                    if ($product['image'] && $product['image'] !== $original) {
                        $old_path = '../uploads/' . $product['image'];
                        if (file_exists($old_path)) {
                            unlink($old_path);
                        }
                    }
                    $new_image = $original;
                    move_uploaded_file($_FILES['image']['tmp_name'], '../uploads/' . $new_image);
                }
            }
        }

        if (empty($warning_msg)) {
            $stmt = $conn->prepare("UPDATE products SET name=?, price=?, product_detail=?, status=?, image=? WHERE id=?");
            $stmt->execute([$name, $price, $detail, $status, $new_image, $pid]);
            $success_msg[] = 'Product updated successfully!';
            // Refresh product data
            $stmt2 = $conn->prepare("SELECT * FROM products WHERE id = ?");
            $stmt2->execute([$pid]);
            $product = $stmt2->fetch();
        }
    }
}

// Handle Delete from edit page
if (isset($_POST['delete'])) {
    $img_path = '../uploads/' . $product['image'];
    if ($product['image'] && file_exists($img_path)) {
        unlink($img_path);
    }
    $del = $conn->prepare("DELETE FROM products WHERE id = ?");
    $del->execute([$pid]);
    $_SESSION['success_msg'] = 'Product deleted successfully.';
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
        <span>Edit Product</span>
    </div>
    <h2 class="heading">Edit Product</h2>
</div>

<div class="form-container">
    <form method="POST" action="" enctype="multipart/form-data" class="product-form">
        <div class="form-row">
            <div class="form-group">
                <label><i class='bx bx-flag'></i> Status</label>
                <select name="status" class="form-select">
                    <option value="active" <?php echo $product['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                    <option value="deactive" <?php echo $product['status'] === 'deactive' ? 'selected' : ''; ?>>Deactive</option>
                </select>
            </div>
            <div class="form-group">
                <label><i class='bx bx-rupee'></i> Price (₹)</label>
                <input type="number" name="price" value="<?php echo htmlspecialchars($product['price']); ?>" required min="0">
            </div>
        </div>

        <div class="form-group">
            <label><i class='bx bx-tag'></i> Product Name</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required maxlength="100">
        </div>

        <div class="form-group">
            <label><i class='bx bx-detail'></i> Product Description</label>
            <textarea name="product_detail" rows="5" required maxlength="1000"><?php echo htmlspecialchars($product['product_detail']); ?></textarea>
        </div>

        <div class="form-group">
            <label><i class='bx bx-image'></i> Product Image</label>
            <?php if ($product['image'] && file_exists('../uploads/' . $product['image'])): ?>
                <div class="current-image-wrap">
                    <img src="../uploads/<?php echo htmlspecialchars($product['image']); ?>" alt="Current Image" class="current-img">
                    <span class="img-label">Current Image</span>
                </div>
            <?php endif; ?>
            <div class="file-upload-zone" style="margin-top:12px;">
                <i class='bx bx-cloud-upload'></i>
                <p>Upload new image to replace (optional)</p>
                <small>JPG, JPEG, PNG only — max 2MB</small>
                <input type="file" name="image" accept="image/*">
            </div>
        </div>

        <div class="form-actions">
            <a href="view_products.php" class="btn btn-outline">
                <i class='bx bx-arrow-back'></i> Back
            </a>
            <button type="submit" name="delete" class="btn btn-danger"
                    onclick="return confirm('Permanently delete this product?')">
                <i class='bx bx-trash'></i> Delete
            </button>
            <button type="submit" name="update" class="btn">
                <i class='bx bx-save'></i> Update Product
            </button>
        </div>
    </form>
</div>

</div><!-- end .main-content -->
<?php include '../components/alert.php'; ?>
</body>
</html>