<?php
require_once '../components/connect.php';

// Auth check
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Handle product submission (publish or draft)
if (isset($_POST['publish']) || isset($_POST['draft'])) {
    $status       = isset($_POST['publish']) ? 'active' : 'deactive';
    $name         = filter_var(trim($_POST['name']), FILTER_SANITIZE_SPECIAL_CHARS);
    $price        = filter_var($_POST['price'], FILTER_SANITIZE_NUMBER_INT);
    $detail       = filter_var(trim($_POST['product_detail']), FILTER_SANITIZE_SPECIAL_CHARS);

    if (empty($name) || empty($price) || empty($detail)) {
        $warning_msg[] = 'Please fill in all required fields.';
    } elseif (!isset($_FILES['image']) || $_FILES['image']['error'] !== 0) {
        $warning_msg[] = 'Please upload a product image.';
    } else {
        $original = $_FILES['image']['name'];
        $ext      = strtolower(pathinfo($original, PATHINFO_EXTENSION));
        $allowed  = ['jpg', 'jpeg', 'png'];
        $size     = $_FILES['image']['size'];

        if (!in_array($ext, $allowed)) {
            $warning_msg[] = 'Image must be JPG, JPEG, or PNG format only.';
        } elseif ($size > 2000000) {
            $warning_msg[] = 'Image size must not exceed 2MB.';
        } else {
            // Check for duplicate image name
            $check = $conn->prepare("SELECT id FROM products WHERE image = ?");
            $check->execute([$original]);
            if ($check->fetch()) {
                $warning_msg[] = 'An image with this filename already exists. Please rename the file and try again.';
            } else {
                $image_name = $original;
                $upload_path = '../uploads/' . $image_name;
                if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                    $pid  = unique_id();
                    $stmt = $conn->prepare("INSERT INTO products (id, name, price, image, product_detail, status) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$pid, $name, $price, $image_name, $detail, $status]);
                    $success_msg[] = $status === 'active'
                        ? 'Product published successfully!'
                        : 'Product saved as draft.';
                } else {
                    $warning_msg[] = 'Failed to upload image. Check folder permissions.';
                }
            }
        }
    }
}

include '../components/admin_header.php';
?>

<div class="page-header">
    <div class="title2">
        <a href="dashboard.php">Dashboard</a>
        <i class='bx bx-chevron-right'></i>
        <span>Add Product</span>
    </div>
    <h2 class="heading">Add New Product</h2>
</div>

<div class="form-wrapper">
<div class="form-container">
    <form method="POST" action="" enctype="multipart/form-data" class="product-form">
        <div class="form-row">
            <div class="form-group">
                <label><i class='bx bx-tag'></i> Product Name</label>
                <input type="text" name="name" placeholder="e.g. Cotton Linen Fabric" required maxlength="100">
            </div>
            <div class="form-group">
                <label><i class='bx bx-rupee'></i> Price (₹)</label>
                <input type="number" name="price" placeholder="e.g. 499" required min="0">
            </div>
        </div>

        <div class="form-group">
            <label><i class='bx bx-detail'></i> Product Description</label>
            <textarea name="product_detail" rows="5" placeholder="Describe the fabric material, usage, care instructions..." required maxlength="1000"></textarea>
            <span class="char-hint">Max 1000 characters</span>
        </div>

        <div class="form-group">
            <label><i class='bx bx-image'></i> Product Image</label>
            <div class="file-upload-zone" id="uploadZone">
                <i class='bx bx-cloud-upload'></i>
                <p>Drag & drop or <span>browse files</span></p>
                <small>JPG, JPEG, PNG only — max 2MB</small>
                <input type="file" name="image" id="imageInput" accept="image/*" required>
            </div>
            <div class="image-preview-wrap" id="previewWrap" style="display:none;">
                <img id="imagePreview" src="" alt="Preview">
                <button type="button" id="removeImage" class="btn btn-danger btn-sm">
                    <i class='bx bx-x'></i> Remove
                </button>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" name="draft" class="btn btn-outline">
                <i class='bx bx-save'></i> Save as Draft
            </button>
            <button type="submit" name="publish" class="btn">
                <i class='bx bx-send'></i> Publish Product
            </button>
        </div>
    </form>
</div>
</div>

<script>
// Image preview
const input = document.getElementById('imageInput');
const previewWrap = document.getElementById('previewWrap');
const preview = document.getElementById('imagePreview');
const zone = document.getElementById('uploadZone');
const removeBtn = document.getElementById('removeImage');

input.addEventListener('change', function() {
    if (this.files && this.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            preview.src = e.target.result;
            zone.style.display = 'none';
            previewWrap.style.display = 'flex';
        };
        reader.readAsDataURL(this.files[0]);
    }
});

removeBtn.addEventListener('click', function() {
    input.value = '';
    preview.src = '';
    previewWrap.style.display = 'none';
    zone.style.display = 'flex';
});

zone.addEventListener('click', (e) => {
    if (e.target !== input) {
        input.click();
    }
});
zone.addEventListener('dragover', e => { e.preventDefault(); zone.classList.add('drag-over'); });
zone.addEventListener('dragleave', () => zone.classList.remove('drag-over'));
zone.addEventListener('drop', e => {
    e.preventDefault();
    zone.classList.remove('drag-over');
    if (e.dataTransfer.files.length) {
        input.files = e.dataTransfer.files;
        input.dispatchEvent(new Event('change'));
    }
});

// Char counter
const textarea = document.querySelector('textarea[name="product_detail"]');
const hint = document.querySelector('.char-hint');
textarea.addEventListener('input', () => {
    hint.textContent = `${textarea.value.length} / 1000 characters`;
});
</script>

</div><!-- end .main-content -->
<?php include '../components/alert.php'; ?>
</body>
</html>