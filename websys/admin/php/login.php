<?php
session_start();
include "../dbconn/db.php";

$login_error = '';
$signup_error = '';
$signup_success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'login') {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        $stmt = $conn->prepare("SELECT admin_id, full_name, username, password FROM admins WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $admin = $result->fetch_assoc();
            if (password_verify($password, $admin['password']) || $password === $admin['password']) {
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id'] = $admin['admin_id'];
                $_SESSION['full_name'] = $admin['full_name'];
                $_SESSION['username'] = $admin['username'];
                header("Location: dashboard.php");
                exit();
            } else {
                $login_error = "Invalid username or password.";
            }
        } else {
            $login_error = "Invalid username or password.";
        }

    } elseif (isset($_POST['action']) && $_POST['action'] === 'signup') {
        $full_name = trim($_POST['full_name'] ?? '');
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        if (!$full_name || !$username || !$password || !$confirm_password) {
            $signup_error = "Please fill in all fields.";
        } elseif ($password !== $confirm_password) {
            $signup_error = "Passwords do not match.";
        } else {
            $check = $conn->prepare("SELECT admin_id FROM admins WHERE username = ?");
            $check->bind_param("s", $username);
            $check->execute();
            $check_result = $check->get_result();

            if ($check_result->num_rows > 0) {
                $signup_error = "Username already exists.";
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $insert = $conn->prepare("INSERT INTO admins (full_name, username, password) VALUES (?, ?, ?)");
                $insert->bind_param("sss", $full_name, $username, $hashed_password);
                if ($insert->execute()) {
                    $signup_success = "Admin account created successfully. You can now log in.";
                } else {
                    $signup_error = "Error creating admin: " . $insert->error;
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Admin Login & Sign Up</title>
<link rel="stylesheet" href="../css/style.css" />
<script>
    function showForm(form) {
        document.getElementById('login-form').style.display = form === 'login' ? 'block' : 'none';
        document.getElementById('signup-form').style.display = form === 'signup' ? 'block' : 'none';
    }
</script>
</head>
<body>

<div class="container" id="login-form" <?= (isset($_POST['action']) && $_POST['action'] === 'signup') ? 'style="display:none;"' : '' ?>>
    <h2>Admin Login</h2>
    <?php if ($login_error): ?>
        <div class="error"><?= htmlspecialchars($login_error) ?></div>
    <?php endif; ?>
    <form method="post" action="">
        <input type="hidden" name="action" value="login" />
        <input type="text" name="username" placeholder="Username" required autofocus />
        <input type="password" name="password" placeholder="Password" required />
        <button type="submit">Login</button>
    </form>
    <div class="switch-link">
        Don't have an account? <a onclick="showForm('signup')">Sign Up</a>
    </div>
</div>

<div class="container" id="signup-form" <?= (isset($_POST['action']) && $_POST['action'] === 'signup') ? '' : 'style="display:none;"' ?>>
    <h2>Admin Sign Up</h2>
    <?php if ($signup_error): ?>
        <div class="error"><?= htmlspecialchars($signup_error) ?></div>
    <?php elseif ($signup_success): ?>
        <div class="success"><?= htmlspecialchars($signup_success) ?></div>
    <?php endif; ?>
    <form method="post" action="">
        <input type="hidden" name="action" value="signup" />
        <input type="text" name="full_name" placeholder="Full Name" required />
        <input type="text" name="username" placeholder="Username" required />
        <input type="password" name="password" placeholder="Password" required />
        <input type="password" name="confirm_password" placeholder="Confirm Password" required />
        <button type="submit">Sign Up</button>
    </form>
    <div class="switch-link">
        Already have an account? <a onclick="showForm('login')">Log In</a>
    </div>
</div>

</body>
</html>
