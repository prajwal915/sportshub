// Sports Hub - Main JavaScript

// API URL (for PHP, we use relative paths)
const API_URL = '';

// Global variables
let currentUser = null;
let cart = [];

// Check login status from PHP session
function checkLoginStatus() {
    // Check if user is logged in via PHP session
    fetch('check_session.php')
        .then(response => response.json())
        .then(data => {
            if (data.logged_in) {
                currentUser = data.user;
                updateAuthUI();
                loadCart();
            }
        })
        .catch(error => console.error('Error checking login:', error));
}

// Update UI based on login status
function updateAuthUI() {
    const authNav = document.getElementById('authNav');
    const adminNav = document.getElementById('adminNav');
    
    if (currentUser) {
        if (authNav) {
            authNav.innerHTML = `<a href="dashboard.php"><i class="fas fa-user"></i> ${currentUser.name}</a>`;
        }
        if (adminNav && currentUser.role === 'admin') {
            adminNav.style.display = 'block';
        }
    } else {
        if (authNav) {
            authNav.innerHTML = `<a href="#" onclick="openLoginModal()"><i class="fas fa-user"></i> Login</a>`;
        }
        if (adminNav) {
            adminNav.style.display = 'none';
        }
    }
}

// Open login modal
function openLoginModal() {
    document.getElementById('loginModal').style.display = 'flex';
}

// Close login modal
function closeLoginModal() {
    document.getElementById('loginModal').style.display = 'none';
}

// Show register form
function showRegister() {
    document.getElementById('loginForm').style.display = 'none';
    document.getElementById('registerForm').style.display = 'block';
}

// Show login form
function showLogin() {
    document.getElementById('loginForm').style.display = 'block';
    document.getElementById('registerForm').style.display = 'none';
}

// Login function
async function login() {
    const email = document.getElementById('loginEmail').value;
    const password = document.getElementById('loginPassword').value;
    
    if (!email || !password) {
        showToast('Please fill all fields', 'error');
        return;
    }
    
    try {
        const response = await fetch('login.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `email=${encodeURIComponent(email)}&password=${encodeURIComponent(password)}`
        });
        
        const text = await response.text();
        if (text.includes('redirect')) {
            showToast('Login successful!', 'success');
            setTimeout(() => {
                window.location.href = 'index.html';
            }, 1000);
        } else {
            showToast('Invalid credentials', 'error');
        }
    } catch (error) {
        showToast('Login failed', 'error');
    }
}

// Register function
async function register() {
    const name = document.getElementById('regName').value;
    const email = document.getElementById('regEmail').value;
    const password = document.getElementById('regPassword').value;
    const confirm = document.getElementById('regConfirmPassword').value;
    
    if (!name || !email || !password) {
        showToast('Please fill all fields', 'error');
        return;
    }
    
    if (password !== confirm) {
        showToast('Passwords do not match', 'error');
        return;
    }
    
    try {
        const response = await fetch('register.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `name=${encodeURIComponent(name)}&email=${encodeURIComponent(email)}&password=${encodeURIComponent(password)}&confirm_password=${encodeURIComponent(confirm)}`
        });
        
        const text = await response.text();
        if (text.includes('successful')) {
            showToast('Registration successful! Please login.', 'success');
            showLogin();
        } else {
            showToast('Registration failed', 'error');
        }
    } catch (error) {
        showToast('Registration failed', 'error');
    }
}

// Add to cart
async function addToCart(productId) {
    if (!currentUser) {
        showToast('Please login first', 'error');
        openLoginModal();
        return;
    }
    
    try {
        const response = await fetch('add_to_cart.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `product_id=${productId}&quantity=1`
        });
        
        const data = await response.json();
        if (data.success) {
            showToast('Added to cart!', 'success');
            loadCart();
        } else {
            showToast(data.message || 'Failed to add', 'error');
        }
    } catch (error) {
        showToast('Failed to add to cart', 'error');
    }
}

// Load cart
async function loadCart() {
    try {
        const response = await fetch('get_cart_count.php');
        const data = await response.json();
        updateCartCount(data.count || 0);
    } catch (error) {
        console.error('Error loading cart:', error);
    }
}

// Update cart count display
function updateCartCount(count) {
    const cartCountElements = document.querySelectorAll('#cartCount');
    cartCountElements.forEach(el => {
        if (el) el.textContent = count;
    });
}

// Toggle mobile menu
function toggleMenu() {
    const navLinks = document.getElementById('navLinks');
    if (navLinks) {
        navLinks.classList.toggle('active');
    }
}

// Scroll to products section
function scrollToProducts() {
    const productsSection = document.getElementById('productsSection');
    if (productsSection) {
        productsSection.scrollIntoView({ behavior: 'smooth' });
    }
}

// Show toast notification
function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = 'toast';
    toast.style.background = type === 'error' ? '#e74c3c' : '#27ae60';
    toast.innerHTML = message;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.remove();
    }, 3000);
}

// Load products via AJAX
async function loadProducts(category = 'all') {
    const productsGrid = document.getElementById('productsGrid');
    if (!productsGrid) return;
    
    productsGrid.innerHTML = '<div class="loading">Loading products...</div>';
    
    try {
        let url = 'get_products.php';
        if (category !== 'all') {
            url += `?category=${category}`;
        }
        
        const response = await fetch(url);
        const products = await response.json();
        
        if (products.length === 0) {
            productsGrid.innerHTML = '<div class="loading">No products found</div>';
            return;
        }
        
        let html = '';
        products.forEach(p => {
            html += `
                <div class="product-card">
                    <div class="product-image">
                        <img src="images/${p.image}" alt="${p.name}" onerror="this.src='https://via.placeholder.com/300'">
                    </div>
                    <div class="product-info">
                        <h3>${p.name}</h3>
                        <p>${p.brand || 'Sports Hub'}</p>
                        <p class="price">₹${parseInt(p.price).toLocaleString()}</p>
                        <button class="add-to-cart" onclick="addToCart(${p.id})">
                            Add to Cart
                        </button>
                    </div>
                </div>
            `;
        });
        productsGrid.innerHTML = html;
        
        // Update section title
        const sectionTitle = document.querySelector('.section-header h2');
        if (sectionTitle && category !== 'all') {
            sectionTitle.innerHTML = `${category.charAt(0).toUpperCase() + category.slice(1)} <span class="highlight">Products</span>`;
        }
    } catch (error) {
        productsGrid.innerHTML = '<div class="loading">Failed to load products</div>';
        console.error('Error loading products:', error);
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
    checkLoginStatus();
    
    // Close modal when clicking outside
    window.onclick = function(event) {
        const modal = document.getElementById('loginModal');
        if (event.target === modal) {
            closeLoginModal();
        }
    };
});