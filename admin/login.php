<?php
require_once '../components/connect.php';

// Redirect if already logged in
if (isset($_SESSION['admin_id'])) {
    header('Location: dashboard.php');
    exit();
}

// Handle login form submission
if (isset($_POST['login'])) {
    $email    = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = sha1(filter_var($_POST['password'], FILTER_SANITIZE_SPECIAL_CHARS));

    if (empty($email) || empty($_POST['password'])) {
        $warning_msg[] = 'Please fill in all fields.';
    } else {
        $stmt = $conn->prepare("SELECT * FROM admin WHERE email = ? AND password = ?");
        $stmt->execute([$email, $password]);
        $admin = $stmt->fetch();

        if ($admin) {
            $_SESSION['admin_id'] = $admin['id'];
            header('Location: dashboard.php');
            exit();
        } else {
            $warning_msg[] = 'Invalid email or password. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login — Fabric Store</title>
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="admin_style.css">
    <style>
    body {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        background: var(--bg-dark);
        margin: 0;
    }

    .auth-container {
        width: 100%;
        max-width: 420px;
        padding: 20px;
    }

    .auth-box {
        background: var(--sidebar-bg);
        border-radius: 16px;
        padding: 40px 36px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.4);
        border: 1px solid rgba(255,255,255,0.05);
    }

    .auth-logo {
        text-align: center;
        margin-bottom: 32px;
    }

    .auth-logo i {
        font-size: 48px;
        color: var(--accent);
    }

    .auth-logo h1 {
        color: var(--text-primary);
        font-size: 24px;
        margin: 8px 0 4px;
        letter-spacing: -0.5px;
    }

    .auth-logo p {
        color: var(--text-muted);
        font-size: 14px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        color: var(--text-secondary);
        font-size: 13px;
        font-weight: 600;
        margin-bottom: 8px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .input-wrap {
        position: relative;
        display: flex;
        align-items: center;
    }

    .input-wrap i {
        position: absolute;
        left: 16px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-muted);
        font-size: 18px;
        pointer-events: none;
    }

    .form-group input {
        width: 100%;
        padding: 12px 14px 12px 55px;
        background: var(--bg-dark);
        border: 1px solid var(--border-color);
        border-radius: 8px;
        color: var(--text-primary);
        font-size: 15px;
        outline: none;
        transition: border-color 0.2s;
        box-sizing: border-box;
    }

    .form-group input:focus {
        border-color: var(--accent);
    }

    .btn-full {
        width: 100%;
        padding: 13px;
        background: var(--accent);
        color: #fff;
        border: none;
        border-radius: 8px;
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s, transform 0.1s;
        letter-spacing: 0.3px;
        margin-top: 4px;
    }

    .btn-full:hover {
        background: var(--accent-hover);
        transform: translateY(-1px);
    }

    .auth-link {
        text-align: center;
        margin-top: 24px;
        color: var(--text-muted);
        font-size: 14px;
    }

    .auth-link a {
        color: var(--accent);
        text-decoration: none;
        font-weight: 600;
    }

    .auth-link a:hover {
        text-decoration: underline;
    }

    /* Autofill Fix */
    input:-webkit-autofill,
    input:-webkit-autofill:hover,
    input:-webkit-autofill:focus {
        -webkit-box-shadow: 0 0 0px 1000px var(--bg-dark) inset !important;
        -webkit-text-fill-color: var(--text-primary) !important;
        border: 1px solid var(--border-color) !important;
    }

    .input-wrap input {
        padding-left: 55px !important;
    }
</style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-box">
            <div class="auth-logo">
                <i class='bx bx-store-alt'></i>
                <h1>Fabric Store</h1>
                <p>Admin Panel — Sign in to continue</p>
            </div>
            <form method="POST" action="">
                <div class="form-group">
                    <label>Email Address</label>
                    <div class="input-wrap">
                        <i class='bx bx-envelope'></i>
                        <input type="email" name="email" placeholder="admin@example.com" required
                               oninput="this.value = this.value.replace(/\s/g,'')">
                    </div>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <div class="input-wrap">
                        <i class='bx bx-lock-alt'></i>
                        <input type="password" name="password" placeholder="••••••••" required
                               oninput="this.value = this.value.replace(/\s/g,'')">
                    </div>
                </div>
                <button type="submit" name="login" class="btn-full">
                    <i class='bx bx-log-in'></i> Sign In
                </button>
            </form>
            <div class="auth-link">
                Don't have an account? <a href="register.php">Register here</a>
            </div>
        </div>
    </div>
    <?php include '../components/alert.php'; ?>
</body>
</html>