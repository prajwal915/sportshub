<?php
require_once 'config.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('login.php');
}

// Handle product add/edit/delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add_product') {
        $stmt = $pdo->prepare("INSERT INTO products (name, category, price, stock, image, brand) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$_POST['name'], $_POST['category'], $_POST['price'], $_POST['stock'], $_POST['image'], $_POST['brand']]);
    } elseif ($_POST['action'] === 'update_product') {
        $stmt = $pdo->prepare("UPDATE products SET name=?, category=?, price=?, stock=?, image=?, brand=? WHERE id=?");
        $stmt->execute([$_POST['name'], $_POST['category'], $_POST['price'], $_POST['stock'], $_POST['image'], $_POST['brand'], $_POST['product_id']]);
    } elseif ($_POST['action'] === 'delete_product') {
        $stmt = $pdo->prepare("DELETE FROM products WHERE id=?");
        $stmt->execute([$_POST['product_id']]);
    } elseif ($_POST['action'] === 'update_order') {
        $stmt = $pdo->prepare("UPDATE orders SET status=? WHERE id=?");
        $stmt->execute([$_POST['status'], $_POST['order_id']]);
    }
}

// Get statistics
$total_products = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$total_orders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$total_users = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$total_revenue = $pdo->query("SELECT SUM(total_amount) FROM orders")->fetchColumn();

$products = $pdo->query("SELECT * FROM products ORDER BY id DESC")->fetchAll();
$orders = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC")->fetchAll();
$users = $pdo->query("SELECT id, name, email, role, created_at FROM users")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Sports Hub</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Poppins', sans-serif;
            background: #f5f5f5;
        }
        .admin-nav {
            background: #1a1a2e;
            color: white;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
        }
        .admin-nav h2 {
            color: #e94560;
        }
        .admin-nav a {
            color: white;
            text-decoration: none;
            margin-left: 1.5rem;
        }
        .admin-nav a:hover {
            color: #e94560;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 100px 20px 40px;
        }
        .stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
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
        }
        .stat-card h3 {
            font-size: 1.8rem;
            margin: 0.5rem 0;
        }
        .panel {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }
        .panel h3 {
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #e94560;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        th {
            background: #f5f5f5;
        }
        .btn {
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .btn-edit { background: #3498db; color: white; }
        .btn-delete { background: #e74c3c; color: white; }
        .btn-add {
            background: #27ae60;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            margin-bottom: 1rem;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        .form-group input, .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 8px;
        }
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 2000;
            align-items: center;
            justify-content: center;
        }
        .modal-content {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            width: 90%;
            max-width: 500px;
        }
        .close {
            float: right;
            font-size: 1.5rem;
            cursor: pointer;
        }
        .status-select {
            padding: 5px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="admin-nav">
        <h2>🏆 SPORTS HUB - ADMIN</h2>
        <div>
            <a href="index.html">Home</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <div class="container">
        <div class="stats">
            <div class="stat-card"><i class="fas fa-box"></i><h3><?php echo $total_products; ?></h3><p>Products</p></div>
            <div class="stat-card"><i class="fas fa-shopping-cart"></i><h3><?php echo $total_orders; ?></h3><p>Orders</p></div>
            <div class="stat-card"><i class="fas fa-users"></i><h3><?php echo $total_users; ?></h3><p>Users</p></div>
            <div class="stat-card"><i class="fas fa-rupee-sign"></i><h3>₹<?php echo number_format($total_revenue, 0); ?></h3><p>Revenue</p></div>
        </div>

        <!-- Products Panel -->
        <div class="panel">
            <h3>Products Management</h3>
            <button class="btn-add" onclick="openProductModal()">+ Add New Product</button>
            <table>
                <thead>
                    <tr><th>ID</th><th>Image</th><th>Name</th><th>Category</th><th>Price</th><th>Stock</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    <?php foreach($products as $product): ?>
                    <tr>
                        <td><?php echo $product['id']; ?></td>
                        <td><img src="images/<?php echo $product['image']; ?>" width="40" height="40" style="object-fit:cover; border-radius:8px;"></td>
                        <td><?php echo htmlspecialchars($product['name']); ?></td>
                        <td><?php echo $product['category']; ?></td>
                        <td>₹<?php echo number_format($product['price'], 0); ?></td>
                        <td><?php echo $product['stock']; ?></td>
                        <td>
                            <button class="btn btn-edit" onclick="editProduct(<?php echo $product['id']; ?>, '<?php echo addslashes($product['name']); ?>', '<?php echo $product['category']; ?>', <?php echo $product['price']; ?>, <?php echo $product['stock']; ?>, '<?php echo $product['image']; ?>', '<?php echo addslashes($product['brand']); ?>')">Edit</button>
                            <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this product?')">
                                <input type="hidden" name="action" value="delete_product">
                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                <button type="submit" class="btn btn-delete">Delete</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Orders Panel -->
        <div class="panel">
            <h3>Orders Management</h3>
            <table>
                <thead>
                    <tr><th>Order #</th><th>User ID</th><th>Amount</th><th>Status</th><th>Date</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    <?php foreach($orders as $order): ?>
                    <tr>
                        <td><?php echo $order['order_number']; ?></td>
                        <td><?php echo $order['user_id']; ?></td>
                        <td>₹<?php echo number_format($order['total_amount'], 0); ?></td>
                        <td>
                            <form method="POST">
                                <input type="hidden" name="action" value="update_order">
                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                <select name="status" onchange="this.form.submit()" class="status-select">
                                    <option value="pending" <?php echo $order['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="processing" <?php echo $order['status'] == 'processing' ? 'selected' : ''; ?>>Processing</option>
                                    <option value="shipped" <?php echo $order['status'] == 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                                    <option value="delivered" <?php echo $order['status'] == 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                </select>
                            </form>
                        </td>
                        <td><?php echo date('d M Y', strtotime($order['created_at'])); ?></td>
                        <td>-</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Users Panel -->
        <div class="panel">
            <h3>Users Management</h3>
            <table>
                <thead>
                    <tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Joined</th></tr>
                </thead>
                <tbody>
                    <?php foreach($users as $user): ?>
                    <tr>
                        <td><?php echo $user['id']; ?></td>
                        <td><?php echo htmlspecialchars($user['name']); ?></td>
                        <td><?php echo $user['email']; ?></td>
                        <td><?php echo $user['role']; ?></td>
                        <td><?php echo date('d M Y', strtotime($user['created_at'])); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Product Modal -->
    <div id="productModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h3 id="modalTitle">Add Product</h3>
            <form method="POST" id="productForm">
                <input type="hidden" name="action" id="formAction" value="add_product">
                <input type="hidden" name="product_id" id="product_id">
                <div class="form-group"><input type="text" name="name" id="product_name" placeholder="Product Name" required></div>
                <div class="form-group">
                    <select name="category" id="product_category" required>
                        <option value="football">Football</option>
                        <option value="cricket">Cricket</option>
                        <option value="basketball">Basketball</option>
                        <option value="tennis">Tennis</option>
                        <option value="accessories">Accessories</option>
                    </select>
                </div>
                <div class="form-group"><input type="number" name="price" id="product_price" placeholder="Price" required></div>
                <div class="form-group"><input type="number" name="stock" id="product_stock" placeholder="Stock" required></div>
                <div class="form-group"><input type="text" name="image" id="product_image" placeholder="Image filename (e.g., jersey.jpg)" required></div>
                <div class="form-group"><input type="text" name="brand" id="product_brand" placeholder="Brand"></div>
                <button type="submit" class="btn-add">Save Product</button>
            </form>
        </div>
    </div>

    <script>
        function openProductModal() {
            document.getElementById('modalTitle').innerText = 'Add Product';
            document.getElementById('formAction').value = 'add_product';
            document.getElementById('product_id').value = '';
            document.getElementById('product_name').value = '';
            document.getElementById('product_category').value = 'football';
            document.getElementById('product_price').value = '';
            document.getElementById('product_stock').value = '';
            document.getElementById('product_image').value = '';
            document.getElementById('product_brand').value = '';
            document.getElementById('productModal').style.display = 'flex';
        }

        function editProduct(id, name, category, price, stock, image, brand) {
            document.getElementById('modalTitle').innerText = 'Edit Product';
            document.getElementById('formAction').value = 'update_product';
            document.getElementById('product_id').value = id;
            document.getElementById('product_name').value = name;
            document.getElementById('product_category').value = category;
            document.getElementById('product_price').value = price;
            document.getElementById('product_stock').value = stock;
            document.getElementById('product_image').value = image;
            document.getElementById('product_brand').value = brand;
            document.getElementById('productModal').style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('productModal').style.display = 'none';
        }

        window.onclick = function(event) {
            if (event.target == document.getElementById('productModal')) {
                closeModal();
            }
        }
    </script>
</body>
</html>