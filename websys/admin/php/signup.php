<?php
session_start();
include "../dbconn/db.php";

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $_POST['full_name'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    $check = $conn->prepare("SELECT user_id FROM users WHERE username = ?");
    $check->bind_param("s", $username);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $error = "Username already exists";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (full_name, username, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $full_name, $username, $password);

        if ($stmt->execute()) {
            $success = "Account created successfully. You may now log in.";
        } else {
            $error = "Failed to create account";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Sign Up</title>
    <link rel="stylesheet" href="../css/user.css">
</head>
<body>

<div class="auth-container">
    <h2>Create Account</h2>

    <?php if ($error): ?>
        <p class="error"><?= $error ?></p>
    <?php endif; ?>

    <?php if ($success): ?>
        <p class="success"><?= $success ?></p>
        <a href="login.php"><button>Go to Login</button></a>
    <?php else: ?>
        <form method="post">
            <input type="text" name="full_name" placeholder="Full Name" required>
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Create Account</button>
        </form>
        <a href="login.php"><button>Back to Login</button></a>
    <?php endif; ?>
</div>

</body>
</html>
