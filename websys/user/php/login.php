        <?php
        session_start();
        include "../dbconn/db.php";

        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'];
            $password = $_POST['password'];

            $stmt = $conn->prepare("SELECT user_id, password FROM users WHERE username = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($user = $result->fetch_assoc()) {
                if ($password === $user['password']) {
                    $_SESSION['user_id'] = $user['user_id'];
                    header("Location: dashboard.php");
                    exit;
                } else {
                    $error = "Invalid password";
                }
            } else {
                $error = "User not found";
            }
        }
        ?>

        <!DOCTYPE html>
        <html>
        <head>
            <title>User Login</title>
            <link rel="stylesheet" href="../css/user.css">
        </head>
        <body>
        <div class="login-box">
            <h2>User Login</h2>

            <?php if ($error): ?>
                <p class="error"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>

            <form method="post">
                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit">Login</button>
            </form>
            <div class="signup-link">
    <p>Don’t have an account?</p>
    <a href="signup.php">
        <button type="button">Create Account</button>
    </a>
</div>
        </div>
        

        </body>
        </html>
