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
    <link href="https://fonts.googleapis.com/css2?family=Shippori+Mincho:wght@400;500;600;700&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --ink:        #1C1C1C;
            --ink-soft:   #2E2E2E;
            --pearl:      #F7F5F2;
            --pearl-warm: #F0EDE8;
            --gold:       #C6A96B;
            --stone:      #D6D0C8;
            --text-mid:   #6B6560;
            --text-muted: #A09890;
            --border:     rgba(28, 28, 28, 0.09);
        }

        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--pearl);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
        }

        .auth-container {
            width: 100%;
            max-width: 420px;
            padding: 20px;
        }

        .auth-box {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 48px 42px;
            box-shadow: 0 4px 28px rgba(28, 28, 28, 0.07);
        }

        .auth-logo {
            text-align: center;
            margin-bottom: 40px;
        }

        .auth-logo-icon {
            width: 56px;
            height: 56px;
            background: var(--ink);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 18px;
        }

        .auth-logo-icon i { font-size: 26px; color: var(--gold); }

        .auth-logo h1 {
            font-family: 'Shippori Mincho', serif;
            color: var(--ink-soft);
            font-size: 22px;
            font-weight: 600;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 6px;
        }

        .auth-logo p {
            color: var(--text-muted);
            font-size: 12.5px;
            letter-spacing: 0.5px;
        }

        .form-group { margin-bottom: 18px; }

        .form-group label {
            display: block;
            color: var(--text-muted);
            font-size: 10px;
            font-weight: 500;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 1.8px;
        }

        .input-wrap {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-wrap i {
            position: absolute;
            left: 13px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--stone);
            font-size: 17px;
            pointer-events: none;
        }

        .input-wrap input {
            width: 100%;
            padding: 12px 13px 12px 42px;
            background: var(--pearl-warm);
            border: 1px solid var(--border);
            border-radius: 5px;
            color: var(--ink-soft);
            font-size: 14px;
            font-family: 'DM Sans', sans-serif;
            outline: none;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
            box-sizing: border-box;
        }

        .input-wrap input:focus {
            border-color: var(--ink-soft);
            background: #fff;
            box-shadow: 0 0 0 3px rgba(28, 28, 28, 0.04);
        }

        .btn-full {
            width: 100%;
            padding: 13px;
            background: var(--ink);
            color: #fff;
            border: 1px solid var(--ink);
            border-radius: 5px;
            font-size: 13px;
            font-weight: 500;
            font-family: 'DM Sans', sans-serif;
            cursor: pointer;
            transition: background 0.2s ease;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-top: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-full:hover { background: #2E2E2E; }

        .auth-divider {
            text-align: center;
            margin: 24px 0 0;
            color: var(--text-muted);
            font-size: 13px;
        }

        .auth-divider a {
            color: var(--gold);
            text-decoration: none;
            font-weight: 500;
        }

        .auth-divider a:hover { text-decoration: underline; }

        .gold-line {
            width: 36px;
            height: 1px;
            background: var(--gold);
            margin: 0 auto 28px;
            opacity: 0.6;
        }

        input:-webkit-autofill,
        input:-webkit-autofill:hover,
        input:-webkit-autofill:focus {
            -webkit-box-shadow: 0 0 0px 1000px var(--pearl-warm) inset !important;
            -webkit-text-fill-color: var(--ink-soft) !important;
            border: 1px solid var(--border) !important;
        }

        .swal2-confirm { background: var(--ink) !important; }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-box">
            <div class="auth-logo">
                <div class="auth-logo-icon">
                    <i class='bx bx-store-alt'></i>
                </div>
                <h1>Fabric Store</h1>
                <div class="gold-line"></div>
                <p>Admin Portal — Sign in to continue</p>
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
            <div class="auth-divider">
                Don't have an account? <a href="register.php">Register here</a>
            </div>
        </div>
    </div>
    <?php include '../components/alert.php'; ?>
</body>
</html>