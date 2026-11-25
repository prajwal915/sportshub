<?php
require_once 'config.php';

if (isLoggedIn()) {
    redirect('index.html');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    if ($password !== $confirm_password) {
        $error = "Passwords do not match!";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters!";
    } else {
        $hashed_password = md5($password);
        
        try {
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            $stmt->execute([$name, $email, $hashed_password]);
            $success = "Registration successful! Please login.";
        } catch(PDOException $e) {
            $error = "Email already exists!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Sports Hub</title>
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
            max-width: 450px;
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
        .success {
            color: green;
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
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <h2>🏆 SPORTS HUB</h2>
            <h3>Create New Account</h3>
            <?php if($error): ?>
                <div class="error"><?php echo $error; ?></div>
            <?php endif; ?>
            <?php if($success): ?>
                <div class="success"><?php echo $success; ?></div>
            <?php endif; ?>
            <form method="POST">
                <input type="text" name="name" placeholder="Full Name" required>
                <input type="email" name="email" placeholder="Email Address" required>
                <input type="password" name="password" placeholder="Password (min 6 characters)" required>
                <input type="password" name="confirm_password" placeholder="Confirm Password" required>
                <button type="submit">Create Account</button>
            </form>
            <p class="switch-text">Already have an account? <a href="login.php">Login</a></p>
            <a href="index.html" style="display: block; text-align: center; margin-top: 15px; color: #666;">← Back to Store</a>
        </div>
    </div>
</body>
</html>