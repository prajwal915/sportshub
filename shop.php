<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop - Sports Hub</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body>
    <nav class="navbar"><div class="container"><div class="logo"><a href="index.html">SPORTS HUB</a></div>
    <ul class="nav-links"><li><a href="index.html">Home</a></li><li><a href="shop.html">Shop</a></li><li><a href="cart.php">Cart</a></li>
    <?php if(isLoggedIn()): ?><li><a href="logout.php">Logout</a></li><?php else: ?><li><a href="login.php">Login</a></li><?php endif; ?>
    </ul></div></nav>

    <div class="products-section" style="margin-top: 100px;">
        <div class="container">
            <h2 class="section-title">All <span class="highlight">Products</span></h2>
            <div class="products-grid">
                <?php
                $stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");
                $products = $stmt->fetchAll();
                foreach($products as $product):
                    $imageUrl = "https://source.unsplash.com/400x400/?" . urlencode($product['category'] . '+sports+equipment');
                ?>
                <div class="product-card">
                    <div class="product-image">
                        <img src="<?php echo $imageUrl; ?>" alt="<?php echo $product['name']; ?>" onerror="this.src='https://picsum.photos/id/20/400/400'">
                    </div>
                    <div class="product-info">
                        <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                        <p class="price">₹<?php echo number_format($product['price'], 0); ?></p>
                        <form action="add_to_cart.php" method="POST">
                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                            <button type="submit" class="add-to-cart">Add to Cart</button>
                        </form>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</body>
</html>