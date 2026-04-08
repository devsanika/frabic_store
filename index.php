<?php
require_once 'components/connect.php';

// Fetch active products
$stmt = $conn->prepare("SELECT * FROM products WHERE status = 'active'");
$stmt->execute();
$products = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fabric Store — Premium Fabrics & Textiles</title>
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <style>
        /* ─── CSS Variables ─── */
        :root {
            --cream:      #faf7f2;
            --warm-white: #f5f0e8;
            --sand:       #e8dcc8;
            --tan:        #c8a882;
            --brown:      #8b6340;
            --dark-brown: #3d2b1a;
            --text-dark:  #2a1f14;
            --text-mid:   #6b5040;
            --text-light: #a08060;
            --accent:     #8b6340;
            --accent-dark:#6b4d30;
            --shadow-sm:  0 2px 8px rgba(61,43,26,0.08);
            --shadow-md:  0 8px 30px rgba(61,43,26,0.12);
            --shadow-lg:  0 20px 60px rgba(61,43,26,0.15);
            --radius:     12px;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Georgia', 'Times New Roman', serif;
            background: var(--cream);
            color: var(--text-dark);
            min-height: 100vh;
        }

        /* ─── Navigation ─── */
        .navbar {
            background: var(--dark-brown);
            padding: 0 5%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 70px;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 2px 20px rgba(0,0,0,0.3);
        }
        .nav-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: var(--cream);
        }
        .nav-brand i { font-size: 28px; color: var(--tan); }
        .nav-brand span { font-size: 22px; font-weight: 700; letter-spacing: -0.5px; }
        .nav-links { display: flex; gap: 8px; }
        .nav-links a {
            color: rgba(250,247,242,0.7);
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 14px;
            font-family: 'Arial', sans-serif;
            transition: all 0.2s;
        }
        .nav-links a:hover { color: var(--cream); background: rgba(255,255,255,0.1); }
        .nav-links a.admin-link {
            color: var(--tan);
            border: 1px solid var(--tan);
        }
        .nav-links a.admin-link:hover {
            background: var(--tan);
            color: var(--dark-brown);
        }

        /* ─── Hero Banner ─── */
        .hero {
            background: linear-gradient(135deg, var(--dark-brown) 0%, #5a3820 50%, var(--brown) 100%);
            padding: 80px 5% 90px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23c8a882' fill-opacity='0.06'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
        .hero-badge {
            display: inline-block;
            background: rgba(200,168,130,0.2);
            border: 1px solid rgba(200,168,130,0.4);
            color: var(--tan);
            font-size: 12px;
            font-family: 'Arial', sans-serif;
            text-transform: uppercase;
            letter-spacing: 3px;
            padding: 6px 20px;
            border-radius: 20px;
            margin-bottom: 24px;
        }
        .hero h1 {
            font-size: clamp(36px, 6vw, 64px);
            color: var(--cream);
            line-height: 1.1;
            letter-spacing: -2px;
            margin-bottom: 16px;
        }
        .hero h1 span { color: var(--tan); }
        .hero p {
            font-size: 18px;
            color: rgba(250,247,242,0.7);
            max-width: 500px;
            margin: 0 auto 36px;
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
        }
        .hero-stats {
            display: flex;
            justify-content: center;
            gap: 40px;
            margin-top: 20px;
        }
        .hero-stat { color: var(--tan); text-align: center; }
        .hero-stat strong { display: block; font-size: 28px; color: var(--cream); }
        .hero-stat span { font-size: 13px; font-family: 'Arial', sans-serif; opacity: 0.8; }

        /* ─── Section ─── */
        .section { padding: 60px 5%; }
        .section-header {
            text-align: center;
            margin-bottom: 48px;
        }
        .section-header h2 {
            font-size: clamp(28px, 4vw, 40px);
            color: var(--dark-brown);
            letter-spacing: -1px;
            margin-bottom: 10px;
        }
        .section-header p {
            color: var(--text-light);
            font-size: 16px;
            font-family: 'Arial', sans-serif;
        }
        .section-divider {
            width: 60px;
            height: 3px;
            background: var(--tan);
            margin: 16px auto 0;
            border-radius: 2px;
        }

        /* ─── Product Grid ─── */
        .product-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 28px;
        }

        .product-card {
            background: #fff;
            border-radius: var(--radius);
            overflow: hidden;
            box-shadow: var(--shadow-sm);
            transition: transform 0.25s ease, box-shadow 0.25s ease;
            border: 1px solid var(--sand);
        }
        .product-card:hover {
            transform: translateY(-6px);
            box-shadow: var(--shadow-lg);
        }

        .product-card-img {
            width: 100%;
            aspect-ratio: 4/3;
            object-fit: cover;
            display: block;
            background: var(--warm-white);
        }

        .img-placeholder-pub {
            width: 100%;
            aspect-ratio: 4/3;
            background: var(--warm-white);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: var(--tan);
        }
        .img-placeholder-pub i { font-size: 48px; }
        .img-placeholder-pub p { font-size: 13px; color: var(--text-light); margin-top: 8px; font-family: 'Arial', sans-serif; }

        .product-card-body {
            padding: 20px;
        }

        .product-card-name {
            font-size: 17px;
            font-weight: 700;
            color: var(--dark-brown);
            margin-bottom: 8px;
            line-height: 1.3;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .product-card-price {
            font-size: 22px;
            font-weight: 700;
            color: var(--accent);
            margin-bottom: 16px;
            font-family: 'Arial', sans-serif;
        }
        .product-card-price::before { content: '₹'; font-size: 15px; vertical-align: super; }

        .btn-view {
            display: block;
            width: 100%;
            padding: 12px;
            background: var(--dark-brown);
            color: var(--cream);
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-family: 'Arial', sans-serif;
            font-weight: 600;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            transition: background 0.2s, transform 0.1s;
            letter-spacing: 0.3px;
        }
        .btn-view:hover { background: var(--brown); transform: scale(1.01); }

        /* ─── Empty State ─── */
        .empty-pub {
            text-align: center;
            padding: 80px 20px;
            color: var(--text-light);
        }
        .empty-pub i { font-size: 72px; display: block; margin-bottom: 20px; color: var(--sand); }
        .empty-pub h3 { font-size: 24px; color: var(--text-mid); margin-bottom: 10px; }
        .empty-pub p { font-size: 15px; font-family: 'Arial', sans-serif; }

        /* ─── Features strip ─── */
        .features {
            background: var(--warm-white);
            border-top: 1px solid var(--sand);
            border-bottom: 1px solid var(--sand);
            padding: 40px 5%;
        }
        .features-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 24px;
            max-width: 900px;
            margin: 0 auto;
        }
        .feature-item { text-align: center; }
        .feature-item i { font-size: 32px; color: var(--tan); display: block; margin-bottom: 10px; }
        .feature-item strong { display: block; color: var(--dark-brown); font-size: 14px; margin-bottom: 4px; font-family: 'Arial', sans-serif; }
        .feature-item span { font-size: 13px; color: var(--text-light); font-family: 'Arial', sans-serif; }

        /* ─── Footer ─── */
        .footer {
            background: var(--dark-brown);
            color: rgba(250,247,242,0.6);
            text-align: center;
            padding: 28px 5%;
            font-size: 14px;
            font-family: 'Arial', sans-serif;
        }
        .footer strong { color: var(--tan); }

        /* ─── Responsive ─── */
        @media (max-width: 900px) {
            .product-grid { grid-template-columns: repeat(2, 1fr); }
            .features-grid { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 580px) {
            .product-grid { grid-template-columns: 1fr; }
            .features-grid { grid-template-columns: 1fr; }
            .hero-stats { gap: 20px; }
            .nav-links a.admin-link { font-size: 12px; padding: 6px 10px; }
        }
    </style>
</head>
<body>

<!-- Navigation -->
<nav class="navbar">
    <a href="index.php" class="nav-brand">
        <i class='bx bx-store-alt'></i>
        <span>Fabric Store</span>
    </a>
    <div class="nav-links">
        <a href="index.php">Shop</a>
        <a href="admin/login.php" class="admin-link">
            <i class='bx bx-shield'></i> Admin
        </a>
    </div>
</nav>

<!-- Hero -->
<section class="hero">
    <div class="hero-badge">New Collection 2025</div>
    <h1>Premium <span>Fabrics</span><br>& Textiles</h1>
    <p>Discover our curated collection of fine fabrics for every creative need.</p>
    <?php
    $total_active = count($products);
    ?>
    <div class="hero-stats">
        <div class="hero-stat">
            <strong><?php echo $total_active; ?>+</strong>
            <span>Products</span>
        </div>
        <div class="hero-stat">
            <strong>100%</strong>
            <span>Quality</span>
        </div>
        <div class="hero-stat">
            <strong>Fast</strong>
            <span>Delivery</span>
        </div>
    </div>
</section>

<!-- Features -->
<div class="features">
    <div class="features-grid">
        <div class="feature-item">
            <i class='bx bx-badge-check'></i>
            <strong>Premium Quality</strong>
            <span>Certified fabrics</span>
        </div>
        <div class="feature-item">
            <i class='bx bx-package'></i>
            <strong>Secure Packaging</strong>
            <span>Safe delivery</span>
        </div>
        <div class="feature-item">
            <i class='bx bx-undo'></i>
            <strong>Easy Returns</strong>
            <span>30-day policy</span>
        </div>
        <div class="feature-item">
            <i class='bx bx-headphone'></i>
            <strong>24/7 Support</strong>
            <span>Always here</span>
        </div>
    </div>
</div>

<!-- Products Section -->
<section class="section">
    <div class="section-header">
        <h2>Our Collection</h2>
        <p>Handpicked fabrics for every occasion</p>
        <div class="section-divider"></div>
    </div>

    <?php if (empty($products)): ?>
        <div class="empty-pub">
            <i class='bx bx-package'></i>
            <h3>No Products Available</h3>
            <p>Check back soon — new fabrics are being added.</p>
        </div>
    <?php else: ?>
        <div class="product-grid">
            <?php foreach ($products as $p): ?>
                <div class="product-card">
                    <?php if ($p['image'] && file_exists('uploads/' . $p['image'])): ?>
                        <img class="product-card-img"
                             src="uploads/<?php echo htmlspecialchars($p['image']); ?>"
                             alt="<?php echo htmlspecialchars($p['name']); ?>"
                             loading="lazy">
                    <?php else: ?>
                        <div class="img-placeholder-pub">
                            <i class='bx bx-image'></i>
                            <p>No Image</p>
                        </div>
                    <?php endif; ?>
                    <div class="product-card-body">
                        <h3 class="product-card-name"><?php echo htmlspecialchars($p['name']); ?></h3>
                        <div class="product-card-price"><?php echo number_format($p['price']); ?></div>
                        <a href="#" class="btn-view">View Details</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<!-- Footer -->
<footer class="footer">
    <p>&copy; <?php echo date('Y'); ?> <strong>Fabric Store</strong> — All rights reserved. | Premium Fabrics & Textiles</p>
</footer>

</body>
</html>