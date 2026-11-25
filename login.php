<?php
require_once 'config.php';

if (isLoggedIn()) {
    redirect('index.html');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = md5($_POST['password']);
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND password = ?");
    $stmt->execute([$email, $password]);
    $user = $stmt->fetch();
    
    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['role'] = $user['role'];
        
        if ($user['role'] === 'admin') {
            redirect('admin_panel.php');
        } else {
            redirect('index.html');
        }
    } else {
        $error = "Invalid email or password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sports Hub</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .auth-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .auth-card {
            background: white;
            padding: 40px;
            border-radius: 20px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
        .auth-card h2 {
            text-align: center;
            margin-bottom: 10px;
            color: #1a1a2e;
        }
        .auth-card h3 {
            text-align: center;
            margin-bottom: 30px;
            color: #e94560;
        }
        .auth-card input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
        }
        .auth-card button {
            width: 100%;
            padding: 12px;
            background: #e94560;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 20px;
        }
        .auth-card button:hover {
            background: #c73e54;
        }
        .error {
            color: red;
            text-align: center;
            margin-bottom: 15px;
        }
        .switch-text {
            text-align: center;
            margin-top: 20px;
        }
        .switch-text a {
            color: #e94560;
            text-decoration: none;
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <h2>🏆 SPORTS HUB</h2>
            <h3>Login to Your Account</h3>
            <?php if($error): ?>
                <div class="error"><?php echo $error; ?></div>
            <?php endif; ?>
            <form method="POST">
                <input type="email" name="email" placeholder="Email Address" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit">Login</button>
            </form>
            <p class="switch-text">Don't have an account? <a href="register.php">Create Account</a></p>
            <a href="index.html" class="back-link">← Back to Store</a>
        </div>
    </div>
</body>
</html>