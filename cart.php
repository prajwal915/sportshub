<?php
require_once 'config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$user_id = $_SESSION['user_id'];

// Handle remove from cart
if (isset($_GET['remove'])) {
    $stmt = $pdo->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
    $stmt->execute([$_GET['remove'], $user_id]);
    redirect('cart.php');
}

// Handle update quantity
if (isset($_POST['update'])) {
    $stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?");
    $stmt->execute([$_POST['quantity'], $_POST['cart_id'], $user_id]);
    redirect('cart.php');
}

// Get cart items
$stmt = $pdo->prepare("SELECT c.*, p.name, p.price, p.image FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = ?");
$stmt->execute([$user_id]);
$cart_items = $stmt->fetchAll();

$total = 0;
foreach ($cart_items as $item) {
    $total += $item['price'] * $item['quantity'];
}
$shipping = $total > 999 ? 0 : 50;
$grand_total = $total + $shipping;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart - Sports Hub</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .cart-page { margin-top: 100px; padding: 2rem 0; min-height: 60vh; }
        .cart-table { width: 100%; background: white; border-radius: 15px; overflow: hidden; }
        .cart-table th, .cart-table td { padding: 15px; text-align: left; border-bottom: 1px solid #eee; }
        .cart-table th { background: #1a1a2e; color: white; }
        .quantity-input { width: 60px; padding: 5px; text-align: center; }
        .cart-summary { background: white; padding: 1.5rem; border-radius: 15px; margin-top: 2rem; }
        .checkout-btn { background: #e94560; color: white; border: none; padding: 15px; width: 100%; border-radius: 8px; cursor: pointer; font-size: 1rem; }
        .empty-cart { text-align: center; padding: 4rem; background: white; border-radius: 15px; }
        .product-image { width: 60px; height: 60px; object-fit: cover; border-radius: 8px; }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="logo"><a href="index.html">SPORTS HUB</a></div>
            <ul class="nav-links">
                <li><a href="index.html">Home</a></li>
                <li><a href="shop.html">Shop</a></li>
                <li><a href="cart.php">Cart</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="cart-page">
        <div class="container">
            <h1>Your Shopping Cart</h1>
            
            <?php if(empty($cart_items)): ?>
                <div class="empty-cart">
                    <i class="fas fa-shopping-cart" style="font-size: 4rem; color: #ccc;"></i>
                    <h3>Your cart is empty</h3>
                    <a href="shop.html" class="btn-primary">Continue Shopping</a>
                </div>
            <?php else: ?>
                <table class="cart-table">
                    <thead>
                        <tr><th>Product</th><th>Name</th><th>Price</th><th>Quantity</th><th>Total</th><th></th></tr>
                    </thead>
                    <tbody>
                        <?php foreach($cart_items as $item): ?>
                        <tr>
                            <td><img src="https://source.unsplash.com/60x60/?<?php echo urlencode($item['name'] . ' sport'); ?>" 
     class="product-image" 
     onerror="this.src='https://picsum.photos/id/20/60/60'">" 
     
</td>
                            <td><?php echo htmlspecialchars($item['name']); ?></td>
                            <td>₹<?php echo number_format($item['price'], 0); ?></td>
                            <td>
                                <form method="POST" style="display: flex; gap: 5px;">
                                    <input type="hidden" name="cart_id" value="<?php echo $item['id']; ?>">
                                    <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" class="quantity-input">
                                    <button type="submit" name="update" class="btn-secondary">Update</button>
                                </form>
                            </td>
                            <td>₹<?php echo number_format($item['price'] * $item['quantity'], 0); ?></td>
                            <td><a href="cart.php?remove=<?php echo $item['id']; ?>" class="delete-btn" onclick="return confirm('Remove item?')">Remove</a></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <div class="cart-summary">
                    <h3>Order Summary</h3>
                    <p>Subtotal: ₹<?php echo number_format($total, 0); ?></p>
                    <p>Shipping: <?php echo $shipping == 0 ? 'FREE' : '₹' . $shipping; ?></p>
                    <hr>
                    <h3>Total: ₹<?php echo number_format($grand_total, 0); ?></h3>
                    <a href="checkout.php" class="checkout-btn">Proceed to Checkout</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>