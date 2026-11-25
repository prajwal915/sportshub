<?php
require_once 'config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

// Get user orders
$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll();

// Get cart count
$cart_count = getCartCount();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Dashboard - Sports Hub</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .dashboard-container {
            margin-top: 100px;
            padding: 2rem 0;
            min-height: 60vh;
        }
        .welcome-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            border-radius: 15px;
            margin-bottom: 2rem;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }
        .stat-card i {
            font-size: 2rem;
            color: #e94560;
            margin-bottom: 0.5rem;
        }
        .stat-card h3 {
            font-size: 1.8rem;
            color: #1a1a2e;
        }
        .orders-list {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }
        .order-item {
            border-bottom: 1px solid #eee;
            padding: 1rem;
        }
        .order-item:last-child {
            border-bottom: none;
        }
        .status-pending { color: #f39c12; }
        .status-processing { color: #3498db; }
        .status-shipped { color: #9b59b6; }
        .status-delivered { color: #27ae60; }
        .empty-orders {
            text-align: center;
            padding: 3rem;
            color: #666;
        }
        .navbar {
            background: #1a1a2e;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            padding: 1rem 0;
        }
        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo a {
            color: white;
            text-decoration: none;
            font-size: 1.5rem;
            font-weight: bold;
        }
        .logo .highlight { color: #e94560; }
        .nav-links {
            display: flex;
            list-style: none;
            gap: 2rem;
        }
        .nav-links a {
            color: white;
            text-decoration: none;
        }
        .nav-links a:hover { color: #e94560; }
        .cart-badge {
            background: #e94560;
            border-radius: 50%;
            padding: 2px 8px;
            margin-left: 5px;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="logo">
                <a href="index.html">SPORTS <span class="highlight">HUB</span></a>
            </div>
            <ul class="nav-links">
                <li><a href="index.html">Home</a></li>
                <li><a href="cart.html">Cart <span class="cart-badge"><?php echo $cart_count; ?></span></a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="dashboard-container">
        <div class="container">
            <div class="welcome-card">
                <h2>Welcome back, <?php echo htmlspecialchars($user_name); ?>!</h2>
                <p>Manage your orders and account from here.</p>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <i class="fas fa-shopping-bag"></i>
                    <h3><?php echo count($orders); ?></h3>
                    <p>Total Orders</p>
                </div>
                <div class="stat-card">
                    <i class="fas fa-rupee-sign"></i>
                    <h3>₹<?php echo number_format(array_sum(array_column($orders, 'total_amount')), 0); ?></h3>
                    <p>Total Spent</p>
                </div>
                <div class="stat-card">
                    <i class="fas fa-heart"></i>
                    <h3>0</h3>
                    <p>Wishlist Items</p>
                </div>
            </div>

            <div class="orders-list">
                <h3>My Orders</h3>
                <?php if(count($orders) > 0): ?>
                    <?php foreach($orders as $order): ?>
                        <div class="order-item">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <div>
                                    <strong>Order #<?php echo $order['order_number']; ?></strong>
                                    <br>
                                    <small><?php echo date('d M Y', strtotime($order['created_at'])); ?></small>
                                </div>
                                <div class="status-<?php echo $order['status']; ?>">
                                    <strong><?php echo strtoupper($order['status']); ?></strong>
                                </div>
                                <div>
                                    <strong>₹<?php echo number_format($order['total_amount'], 0); ?></strong>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-orders">
                        <i class="fas fa-box-open" style="font-size: 3rem; margin-bottom: 1rem;"></i>
                        <p>No orders yet!</p>
                        <a href="index.html" class="btn-primary" style="display: inline-block; margin-top: 1rem;">Start Shopping</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>