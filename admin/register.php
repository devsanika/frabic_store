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
        // Check duplicate email
        $stmt = $conn->prepare("SELECT id FROM admin WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $warning_msg[] = 'An admin with this email already exists.';
        } else {
            // Handle profile picture upload
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
                    $upload_path  = '../uploads/' . $profile_name;
                    move_uploaded_file($_FILES['profile']['tmp_name'], $upload_path);
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="admin_style.css">
    <style>
        body { display: flex; justify-content: center; align-items: center; min-height: 100vh; background: var(--bg-dark); margin: 0; padding: 20px; box-sizing: border-box; }
        .auth-container { width: 100%; max-width: 460px; }
        .auth-box { background: var(--sidebar-bg); border-radius: 16px; padding: 40px 36px; box-shadow: 0 20px 60px rgba(0,0,0,0.4); border: 1px solid rgba(255,255,255,0.05); }
        .auth-logo { text-align: center; margin-bottom: 32px; }
        .auth-logo i { font-size: 48px; color: var(--accent); }
        .auth-logo h1 { color: var(--text-primary); font-size: 24px; margin: 8px 0 4px; letter-spacing: -0.5px; }
        .auth-logo p { color: var(--text-muted); font-size: 14px; }
        .form-group { margin-bottom: 18px; }
        .form-group label { display: block; color: var(--text-secondary); font-size: 13px; font-weight: 600; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px; }
        .input-wrap { position: relative; }
        .input-wrap i { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 18px; pointer-events: none; }
        .form-group input[type="text"],
        .form-group input[type="email"],
        .form-group input[type="password"] { width: 100%; padding: 12px 14px 12px 44px; background: var(--bg-dark); border: 1px solid var(--border-color); border-radius: 8px; color: var(--text-primary); font-size: 15px; outline: none; transition: border-color 0.2s; box-sizing: border-box; }
        .form-group input[type="file"] { width: 100%; padding: 10px 14px; background: var(--bg-dark); border: 1px solid var(--border-color); border-radius: 8px; color: var(--text-muted); font-size: 14px; box-sizing: border-box; cursor: pointer; }
        .form-group input:focus { border-color: var(--accent); }
        .btn-full { width: 100%; padding: 13px; background: var(--accent); color: #fff; border: none; border-radius: 8px; font-size: 15px; font-weight: 600; cursor: pointer; transition: background 0.2s, transform 0.1s; letter-spacing: 0.3px; margin-top: 4px; }
        .btn-full:hover { background: var(--accent-hover); transform: translateY(-1px); }
        .auth-link { text-align: center; margin-top: 24px; color: var(--text-muted); font-size: 14px; }
        .auth-link a { color: var(--accent); text-decoration: none; font-weight: 600; }
        .auth-link a:hover { text-decoration: underline; }
        .upload-hint { font-size: 12px; color: var(--text-muted); margin-top: 6px; }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-box">
            <div class="auth-logo">
                <i class='bx bx-user-plus'></i>
                <h1>Create Account</h1>
                <p>Register a new admin account</p>
            </div>
            <form method="POST" action="" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Username</label>
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
                    <label>Profile Picture</label>
                    <input type="file" name="profile" accept="image/*">
                    <p class="upload-hint">JPG, JPEG or PNG only — max 2MB (optional)</p>
                </div>
                <button type="submit" name="register" class="btn-full">
                    <i class='bx bx-user-check'></i> Create Account
                </button>
            </form>
            <div class="auth-link">
                Already have an account? <a href="login.php">Sign in here</a>
            </div>
        </div>
    </div>
    <?php include '../components/alert.php'; ?>
</body>
</html>