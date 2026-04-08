<?php
require_once '../components/connect.php';

// Redirect if already logged in
if (isset($_SESSION['admin_id'])) {
    header('Location: dashboard.php');
    exit();
}

// Handle registration
if (isset($_POST['register'])) {
    $name     = filter_var(trim($_POST['name']), FILTER_SANITIZE_SPECIAL_CHARS);
    $email    = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = filter_var($_POST['password'], FILTER_SANITIZE_SPECIAL_CHARS);
    $confirm  = filter_var($_POST['confirm_password'], FILTER_SANITIZE_SPECIAL_CHARS);

    if (empty($name) || empty($email) || empty($password) || empty($confirm)) {
        $warning_msg[] = 'Please fill in all fields.';
    } elseif ($password !== $confirm) {
        $warning_msg[] = 'Passwords do not match.';
    } elseif (strlen($password) < 6) {
        $warning_msg[] = 'Password must be at least 6 characters.';
    } else {
        $stmt = $conn->prepare("SELECT id FROM admin WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $warning_msg[] = 'An admin with this email already exists.';
        } else {
            $profile_name = '';
            if (isset($_FILES['profile']) && $_FILES['profile']['error'] === 0) {
                $original   = $_FILES['profile']['name'];
                $ext        = strtolower(pathinfo($original, PATHINFO_EXTENSION));
                $allowed    = ['jpg', 'jpeg', 'png'];
                $size       = $_FILES['profile']['size'];

                if (!in_array($ext, $allowed)) {
                    $warning_msg[] = 'Profile image must be JPG, JPEG, or PNG.';
                } elseif ($size > 2000000) {
                    $warning_msg[] = 'Profile image must be under 2MB.';
                } else {
                    $profile_name = unique_id() . '.' . $ext;
                    move_uploaded_file($_FILES['profile']['tmp_name'], '../uploads/' . $profile_name);
                }
            }

            if (empty($warning_msg)) {
                $hashed   = sha1($password);
                $admin_id = unique_id();
                $stmt     = $conn->prepare("INSERT INTO admin (id, name, email, password, profile) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$admin_id, $name, $email, $hashed, $profile_name]);
                $success_msg[] = 'Registration successful! You can now log in.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Register — Fabric Store</title>
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
            padding: 20px;
            -webkit-font-smoothing: antialiased;
        }

        .auth-container { width: 100%; max-width: 440px; }

        .auth-box {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 44px 42px;
            box-shadow: 0 4px 28px rgba(28, 28, 28, 0.07);
        }

        .auth-logo { text-align: center; margin-bottom: 36px; }

        .auth-logo-icon {
            width: 56px;
            height: 56px;
            background: var(--ink);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
        }

        .auth-logo-icon i { font-size: 26px; color: var(--gold); }

        .auth-logo h1 {
            font-family: 'Shippori Mincho', serif;
            color: var(--ink-soft);
            font-size: 20px;
            font-weight: 600;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 6px;
        }

        .gold-line {
            width: 32px;
            height: 1px;
            background: var(--gold);
            margin: 12px auto 14px;
            opacity: 0.6;
        }

        .auth-logo p { color: var(--text-muted); font-size: 12.5px; }

        .form-group { margin-bottom: 16px; }

        .form-group label {
            display: block;
            color: var(--text-muted);
            font-size: 10px;
            font-weight: 500;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 1.8px;
        }

        .input-wrap { position: relative; }

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

        input[type="file"] {
            width: 100%;
            padding: 10px 13px;
            background: var(--pearl-warm);
            border: 1px dashed rgba(28, 28, 28, 0.15);
            border-radius: 5px;
            color: var(--text-muted);
            font-size: 13px;
            font-family: 'DM Sans', sans-serif;
            box-sizing: border-box;
            cursor: pointer;
            transition: border-color 0.2s;
        }

        input[type="file"]:hover { border-color: var(--gold); }

        .upload-hint { font-size: 11px; color: var(--text-muted); margin-top: 5px; letter-spacing: 0.3px; }

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
            margin-top: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-full:hover { background: #2E2E2E; }

        .auth-divider {
            text-align: center;
            margin-top: 22px;
            color: var(--text-muted);
            font-size: 13px;
        }

        .auth-divider a { color: var(--gold); text-decoration: none; font-weight: 500; }
        .auth-divider a:hover { text-decoration: underline; }

        .optional-tag {
            color: var(--text-muted);
            font-weight: 400;
            text-transform: none;
            letter-spacing: 0;
            font-size: 10px;
        }

        input:-webkit-autofill,
        input:-webkit-autofill:hover,
        input:-webkit-autofill:focus {
            -webkit-box-shadow: 0 0 0px 1000px var(--pearl-warm) inset !important;
            -webkit-text-fill-color: var(--ink-soft) !important;
        }

        .swal2-confirm { background: var(--ink) !important; }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-box">
            <div class="auth-logo">
                <div class="auth-logo-icon">
                    <i class='bx bx-user-plus'></i>
                </div>
                <h1>Create Account</h1>
                <div class="gold-line"></div>
                <p>Register a new admin account</p>
            </div>
            <form method="POST" action="" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Full Name</label>
                    <div class="input-wrap">
                        <i class='bx bx-user'></i>
                        <input type="text" name="name" placeholder="Your name" required maxlength="50"
                               oninput="this.value = this.value.replace(/\s/g,'')">
                    </div>
                </div>
                <div class="form-group">
                    <label>Email Address</label>
                    <div class="input-wrap">
                        <i class='bx bx-envelope'></i>
                        <input type="email" name="email" placeholder="admin@example.com" required maxlength="100"
                               oninput="this.value = this.value.replace(/\s/g,'')">
                    </div>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <div class="input-wrap">
                        <i class='bx bx-lock-alt'></i>
                        <input type="password" name="password" placeholder="Min. 6 characters" required maxlength="100"
                               oninput="this.value = this.value.replace(/\s/g,'')">
                    </div>
                </div>
                <div class="form-group">
                    <label>Confirm Password</label>
                    <div class="input-wrap">
                        <i class='bx bx-lock-open-alt'></i>
                        <input type="password" name="confirm_password" placeholder="Re-enter password" required maxlength="100"
                               oninput="this.value = this.value.replace(/\s/g,'')">
                    </div>
                </div>
                <div class="form-group">
                    <label>Profile Picture <span class="optional-tag">(optional)</span></label>
                    <input type="file" name="profile" accept="image/*">
                    <p class="upload-hint">JPG, JPEG or PNG — max 2MB</p>
                </div>
                <button type="submit" name="register" class="btn-full">
                    <i class='bx bx-user-check'></i> Create Account
                </button>
            </form>
            <div class="auth-divider">
                Already have an account? <a href="login.php">Sign in here</a>
            </div>
        </div>
    </div>
    <?php include '../components/alert.php'; ?>
</body>
</html>