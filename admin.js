// Admin Panel JavaScript

// Load admin dashboard
async function loadAdminDashboard() {
    await loadStats();
    await loadProducts();
}

// Load statistics
async function loadStats() {
    try {
        const response = await fetch('get_stats.php');
        const data = await response.json();
        
        document.getElementById('totalProducts').innerText = data.totalProducts || 0;
        document.getElementById('totalOrders').innerText = data.totalOrders || 0;
        document.getElementById('totalUsers').innerText = data.totalUsers || 0;
        document.getElementById('totalRevenue').innerText = `₹${(data.totalRevenue || 0).toLocaleString()}`;
    } catch (error) {
        console.error('Error loading stats:', error);
    }
}

// Load products
async function loadProducts() {
    try {
        const response = await fetch('get_products.php');
        const products = await response.json();
        
        const tbody = document.getElementById('productsTableBody');
        if (products.length === 0) {
            tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;">No products found</td></tr>';
            return;
        }
        
        let html = '';
        products.forEach(p => {
            html += `
                <tr>
                    <td>${p.id}</td>
                    <td><img src="images/${p.image}" width="40" height="40" style="object-fit:cover; border-radius:8px;" onerror="this.src='https://via.placeholder.com/40'"></td>
                    <td><strong>${escapeHtml(p.name)}</strong></td>
                    <td>${p.category}</td>
                    <td>₹${parseInt(p.price).toLocaleString()}</td>
                    <td>${p.stock}</td>
                    <td class="action-buttons">
                        <button class="edit-btn" onclick="editProduct(${p.id})"><i class="fas fa-edit"></i> Edit</button>
                        <button class="delete-btn" onclick="deleteProduct(${p.id})"><i class="fas fa-trash"></i> Delete</button>
                    </td>
                </tr>
            `;
        });
        tbody.innerHTML = html;
    } catch (error) {
        console.error('Error loading products:', error);
    }
}

// Load orders
async function loadOrders() {
    try {
        const response = await fetch('get_orders.php');
        const orders = await response.json();
        
        const tbody = document.getElementById('ordersTableBody');
        if (orders.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;">No orders found</td></tr>';
            return;
        }
        
        let html = '';
        orders.forEach(o => {
            const statusClass = getStatusClass(o.status);
            html += `
                <tr>
                    <td>${o.order_number}</td>
                    <td>User #${o.user_id}</td>
                    <td>₹${parseInt(o.total_amount).toLocaleString()}</td>
                    <td>
                        <select class="status-select ${statusClass}" onchange="updateOrderStatus(${o.id}, this.value)">
                            <option value="pending" ${o.status === 'pending' ? 'selected' : ''}>Pending</option>
                            <option value="processing" ${o.status === 'processing' ? 'selected' : ''}>Processing</option>
                            <option value="shipped" ${o.status === 'shipped' ? 'selected' : ''}>Shipped</option>
                            <option value="delivered" ${o.status === 'delivered' ? 'selected' : ''}>Delivered</option>
                            <option value="cancelled" ${o.status === 'cancelled' ? 'selected' : ''}>Cancelled</option>
                        </select>
                    </td>
                    <td>${new Date(o.created_at).toLocaleDateString()}</td>
                    <td>
                        <button class="view-btn" onclick="viewOrder(${o.id})"><i class="fas fa-eye"></i> View</button>
                    </td>
                </tr>
            `;
        });
        tbody.innerHTML = html;
    } catch (error) {
        console.error('Error loading orders:', error);
    }
}

// Load users
async function loadUsers() {
    try {
        const response = await fetch('get_users.php');
        const users = await response.json();
        
        const tbody = document.getElementById('usersTableBody');
        if (users.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;">No users found</td></tr>';
            return;
        }
        
        let html = '';
        users.forEach(u => {
            html += `
                <tr>
                    <td>${u.id}</td>
                    <td>${escapeHtml(u.name)}</td>
                    <td>${u.email}</td>
                    <td><span class="role-${u.role}">${u.role}</span></td>
                    <td>${new Date(u.created_at).toLocaleDateString()}</td>
                    <td>
                        ${u.role !== 'admin' ? `<button class="delete-btn" onclick="deleteUser(${u.id})"><i class="fas fa-trash"></i> Delete</button>` : 'Admin Account'}
                    </td>
                </tr>
            `;
        });
        tbody.innerHTML = html;
    } catch (error) {
        console.error('Error loading users:', error);
    }
}

// Update order status
async function updateOrderStatus(orderId, status) {
    try {
        const response = await fetch('update_order_status.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `order_id=${orderId}&status=${status}`
        });
        const data = await response.json();
        if (data.success) {
            showToast('Order status updated!', 'success');
        }
    } catch (error) {
        showToast('Failed to update status', 'error');
    }
}

// Add product
async function addProduct(productData) {
    try {
        const response = await fetch('add_product.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams(productData)
        });
        const data = await response.json();
        if (data.success) {
            showToast('Product added!', 'success');
            closeProductModal();
            loadProducts();
            loadStats();
        } else {
            showToast(data.message || 'Failed to add product', 'error');
        }
    } catch (error) {
        showToast('Failed to add product', 'error');
    }
}

// Update product
async function updateProduct(productId, productData) {
    productData.product_id = productId;
    productData.action = 'update';
    try {
        const response = await fetch('update_product.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams(productData)
        });
        const data = await response.json();
        if (data.success) {
            showToast('Product updated!', 'success');
            closeProductModal();
            loadProducts();
            loadStats();
        } else {
            showToast(data.message || 'Failed to update product', 'error');
        }
    } catch (error) {
        showToast('Failed to update product', 'error');
    }
}

// Delete product
async function deleteProduct(productId) {
    if (confirm('Are you sure you want to delete this product?')) {
        try {
            const response = await fetch('delete_product.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `product_id=${productId}`
            });
            const data = await response.json();
            if (data.success) {
                showToast('Product deleted!', 'success');
                loadProducts();
                loadStats();
            } else {
                showToast('Failed to delete product', 'error');
            }
        } catch (error) {
            showToast('Failed to delete product', 'error');
        }
    }
}

// Delete user
async function deleteUser(userId) {
    if (confirm('Are you sure you want to delete this user?')) {
        try {
            const response = await fetch('delete_user.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `user_id=${userId}`
            });
            const data = await response.json();
            if (data.success) {
                showToast('User deleted!', 'success');
                loadUsers();
                loadStats();
            } else {
                showToast('Failed to delete user', 'error');
            }
        } catch (error) {
            showToast('Failed to delete user', 'error');
        }
    }
}

// Open product modal for add
function openProductModal() {
    document.getElementById('modalTitle').innerText = 'Add Product';
    document.getElementById('productId').value = '';
    document.getElementById('productName').value = '';
    document.getElementById('productCategory').value = 'football';
    document.getElementById('productBrand').value = '';
    document.getElementById('productPrice').value = '';
    document.getElementById('productStock').value = '';
    document.getElementById('productImage').value = '';
    document.getElementById('productModal').style.display = 'flex';
}

// Edit product
async function editProduct(productId) {
    try {
        const response = await fetch(`get_product.php?id=${productId}`);
        const product = await response.json();
        
        document.getElementById('modalTitle').innerText = 'Edit Product';
        document.getElementById('productId').value = product.id;
        document.getElementById('productName').value = product.name;
        document.getElementById('productCategory').value = product.category;
        document.getElementById('productBrand').value = product.brand || '';
        document.getElementById('productPrice').value = product.price;
        document.getElementById('productStock').value = product.stock;
        document.getElementById('productImage').value = product.image;
        document.getElementById('productModal').style.display = 'flex';
    } catch (error) {
        showToast('Failed to load product', 'error');
    }
}

// Close product modal
function closeProductModal() {
    document.getElementById('productModal').style.display = 'none';
}

// Handle product form submit
document.getElementById('productForm')?.addEventListener('submit', (e) => {
    e.preventDefault();
    
    const productId = document.getElementById('productId').value;
    const productData = {
        name: document.getElementById('productName').value,
        category: document.getElementById('productCategory').value,
        brand: document.getElementById('productBrand').value,
        price: document.getElementById('productPrice').value,
        stock: document.getElementById('productStock').value,
        image: document.getElementById('productImage').value
    };
    
    if (productId) {
        updateProduct(productId, productData);
    } else {
        addProduct(productData);
    }
});

// Show/hide panels
function showProducts() {
    document.getElementById('productsPanel').style.display = 'block';
    document.getElementById('ordersPanel').style.display = 'none';
    document.getElementById('usersPanel').style.display = 'none';
    loadProducts();
}

function showOrders() {
    document.getElementById('productsPanel').style.display = 'none';
    document.getElementById('ordersPanel').style.display = 'block';
    document.getElementById('usersPanel').style.display = 'none';
    loadOrders();
}

function showUsers() {
    document.getElementById('productsPanel').style.display = 'none';
    document.getElementById('ordersPanel').style.display = 'none';
    document.getElementById('usersPanel').style.display = 'block';
    loadUsers();
}

// Helper functions
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function getStatusClass(status) {
    const classes = {
        pending: 'status-pending',
        processing: 'status-processing',
        shipped: 'status-shipped',
        delivered: 'status-delivered',
        cancelled: 'status-cancelled'
    };
    return classes[status] || 'status-pending';
}

function showToast(message, type) {
    const toast = document.createElement('div');
    toast.className = 'toast';
    toast.style.cssText = `position:fixed;bottom:20px;right:20px;background:${type === 'error' ? '#e74c3c' : '#27ae60'};color:white;padding:12px 24px;border-radius:8px;z-index:3000;`;
    toast.innerHTML = message;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
}

// Initialize
document.addEventListener('DOMContentLoaded', () => {
    loadAdminDashboard();
});