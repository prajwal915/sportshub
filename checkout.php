<?php
require_once 'config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$user_id = $_SESSION['user_id'];

// Get cart items
$stmt = $pdo->prepare("SELECT c.*, p.name, p.price FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = ?");
$stmt->execute([$user_id]);
$cart_items = $stmt->fetchAll();

if (empty($cart_items)) {
    redirect('cart.php');
}

$total = 0;
foreach ($cart_items as $item) {
    $total += $item['price'] * $item['quantity'];
}
$shipping = $total > 999 ? 0 : 50;
$grand_total = $total + $shipping;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_number = 'ORD' . time() . rand(100, 999);
    
    // Insert order
    $stmt = $pdo->prepare("INSERT INTO orders (order_number, user_id, total_amount, payment_method, shipping_name, shipping_address, shipping_city, shipping_pincode, shipping_phone) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$order_number, $user_id, $grand_total, $_POST['payment_method'], $_POST['name'], $_POST['address'], $_POST['city'], $_POST['pincode'], $_POST['phone']]);
    $order_id = $pdo->lastInsertId();
    
    // Insert order items
    foreach ($cart_items as $item) {
        $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, product_name, quantity, price) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$order_id, $item['product_id'], $item['name'], $item['quantity'], $item['price']]);
    }
    
    // Clear cart
    $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
    $stmt->execute([$user_id]);
    
    redirect('dashboard.php');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Sports Hub</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .checkout-container { margin-top: 100px; padding: 2rem; display: grid; grid-template-columns: 1fr 350px; gap: 2rem; max-width: 1200px; margin: 100px auto 40px; }
        .checkout-form { background: white; padding: 2rem; border-radius: 15px; }
        .form-group { margin-bottom: 1rem; }
        .form-group input, .form-group select { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; }
        .order-summary { background: white; padding: 1.5rem; border-radius: 15px; height: fit-content; }
        .summary-item { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #eee; }
        .total { font-size: 1.2rem; font-weight: bold; margin-top: 1rem; padding-top: 1rem; border-top: 2px solid #e94560; }
        .btn-submit { background: #e94560; color: white; border: none; padding: 15px; width: 100%; border-radius: 8px; cursor: pointer; font-size: 1rem; margin-top: 1rem; }
    </style>
</head>
<body>
    <nav class="navbar"><div class="container"><div class="logo"><a href="index.html">SPORTS HUB</a></div></div></nav>

    <div class="checkout-container">
        <div class="checkout-form">
            <h2>Shipping Information</h2>
            <form method="POST">
                <div class="form-group"><input type="text" name="name" placeholder="Full Name" required></div>
                <div class="form-group"><input type="tel" name="phone" placeholder="Phone Number" required></div>
                <div class="form-group"><input type="text" name="address" placeholder="Address" required></div>
                <div class="form-group"><input type="text" name="city" placeholder="City" required></div>
                <div class="form-group"><input type="text" name="pincode" placeholder="Pincode" required></div>
                <div class="form-group">
                    <select name="payment_method" required>
                        <option value="COD">Cash on Delivery</option>
                        <option value="Card">Credit/Debit Card</option>
                        <option value="UPI">UPI</option>
                    </select>
                </div>
                <button type="submit" class="btn-submit">Place Order</button>
            </form>
        </div>
        
        <div class="order-summary">
            <h3>Order Summary</h3>
            <?php foreach($cart_items as $item): ?>
                <div class="summary-item">
                    <span><?php echo htmlspecialchars($item['name']); ?> x<?php echo $item['quantity']; ?></span>
                    <span>₹<?php echo number_format($item['price'] * $item['quantity'], 0); ?></span>
                </div>
            <?php endforeach; ?>
            <div class="summary-item"><span>Subtotal</span><span>₹<?php echo number_format($total, 0); ?></span></div>
            <div class="summary-item"><span>Shipping</span><span><?php echo $shipping == 0 ? 'FREE' : '₹' . $shipping; ?></span></div>
            <div class="total"><span>Total</span><span>₹<?php echo number_format($grand_total, 0); ?></span></div>
        </div>
    </div>
</body>
</html>