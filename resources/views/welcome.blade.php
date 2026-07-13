<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>GlowCommerce | Premium E-Commerce Platform</title>
    <!-- Google Fonts: Inter & Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        /* Base Reset & Variables */
        :root {
            --bg-base: #09090b;
            --bg-panel: rgba(20, 20, 25, 0.6);
            --bg-input: rgba(10, 10, 12, 0.8);
            --border-glass: rgba(255, 255, 255, 0.06);
            --border-glow: rgba(99, 102, 241, 0.25);
            
            --accent-primary: #6366f1; /* Indigo */
            --accent-primary-glow: rgba(99, 102, 241, 0.5);
            --accent-success: #10b981; /* Emerald */
            --accent-danger: #ef4444; /* Red */
            --accent-bkash: #e2136e; /* bKash Pink */
            --accent-stripe: #6772e5; /* Stripe Purple */
            
            --text-main: #f4f4f5;
            --text-muted: #a1a1aa;
            --font-sans: 'Inter', sans-serif;
            --font-display: 'Outfit', sans-serif;
            --shadow-glow: 0 0 20px rgba(99, 102, 241, 0.15);
            --radius-lg: 16px;
            --radius-md: 8px;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            scrollbar-width: thin;
            scrollbar-color: var(--accent-primary) var(--bg-base);
        }

        body {
            background-color: var(--bg-base);
            color: var(--text-main);
            font-family: var(--font-sans);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
            background-image: 
                radial-gradient(circle at 10% 20%, rgba(99, 102, 241, 0.05) 0%, transparent 40%),
                radial-gradient(circle at 90% 80%, rgba(168, 85, 247, 0.05) 0%, transparent 45%);
            background-attachment: fixed;
        }

        /* Glassmorphism Classes */
        .glass-panel {
            background: var(--bg-panel);
            backdrop-filter: blur(16px);
            border: 1px solid var(--border-glass);
            border-radius: var(--radius-lg);
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37);
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid var(--border-glass);
            border-radius: var(--radius-md);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .glass-card:hover {
            border-color: var(--accent-primary-glow);
            transform: translateY(-4px);
            box-shadow: var(--shadow-glow);
            background: rgba(255, 255, 255, 0.04);
        }

        /* Header / Nav Styling */
        header {
            position: sticky;
            top: 0;
            z-index: 100;
            width: 100%;
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 40px;
            background: rgba(9, 9, 11, 0.8);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border-glass);
        }

        .logo {
            font-family: var(--font-display);
            font-size: 24px;
            font-weight: 800;
            background: linear-gradient(135deg, #a5b4fc 0%, #6366f1 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        /* Forms, Buttons, and Inputs */
        input, select, textarea {
            background-color: var(--bg-input);
            border: 1px solid var(--border-glass);
            color: var(--text-main);
            padding: 10px 14px;
            border-radius: var(--radius-md);
            outline: none;
            font-family: var(--font-sans);
            transition: all 0.2s ease;
        }

        input:focus, select:focus, textarea:focus {
            border-color: var(--accent-primary);
            box-shadow: 0 0 0 2px var(--border-glow);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-weight: 500;
            padding: 10px 18px;
            border-radius: var(--radius-md);
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
            font-family: var(--font-sans);
        }

        .btn-primary {
            background-color: var(--accent-primary);
            color: #ffffff;
        }

        .btn-primary:hover {
            background-color: #4f46e5;
            box-shadow: 0 0 12px rgba(99, 102, 241, 0.4);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border-glass);
            color: var(--text-main);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .btn-danger {
            background-color: var(--accent-danger);
            color: white;
        }

        .btn-danger:hover {
            background-color: #dc2626;
            box-shadow: 0 0 10px rgba(239, 68, 68, 0.4);
        }

        /* Badges */
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 2px 8px;
            font-size: 11px;
            font-weight: 600;
            border-radius: 9999px;
            text-transform: uppercase;
        }

        .badge-success { background: rgba(16, 185, 129, 0.15); color: var(--accent-success); border: 1px solid rgba(16, 185, 129, 0.3); }
        .badge-pending { background: rgba(245, 158, 11, 0.15); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.3); }
        .badge-danger { background: rgba(239, 68, 68, 0.15); color: var(--accent-danger); border: 1px solid rgba(239, 68, 68, 0.3); }

        /* Notification Toast */
        .toast {
            position: fixed;
            bottom: 24px;
            right: 24px;
            z-index: 1000;
            padding: 14px 20px;
            border-radius: var(--radius-md);
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
            transform: translateY(150px);
            opacity: 0;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 10px 25px rgba(0,0,0,0.5);
        }

        .toast.show {
            transform: translateY(0);
            opacity: 1;
        }

        .toast-success { background-color: #064e3b; color: #a7f3d0; border-left: 4px solid var(--accent-success); }
        .toast-error { background-color: #7f1d1d; color: #fca5a5; border-left: 4px solid var(--accent-danger); }

        /* Auth Container Layout */
        .auth-container {
            width: 100%;
            max-width: 450px;
            margin: auto;
            padding: 30px;
        }

        .auth-tabs {
            display: flex;
            border-bottom: 1px solid var(--border-glass);
            margin-bottom: 24px;
        }

        .auth-tab {
            flex: 1;
            text-align: center;
            padding: 12px;
            cursor: pointer;
            font-weight: 500;
            color: var(--text-muted);
            border-bottom: 2px solid transparent;
            transition: all 0.2s ease;
        }

        .auth-tab.active {
            color: var(--text-main);
            border-bottom-color: var(--accent-primary);
        }

        .form-group {
            margin-bottom: 18px;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        /* Dashboard Main Layout */
        main {
            flex: 1;
            display: flex;
            padding: 30px 40px;
            gap: 30px;
            max-width: 1600px;
            width: 100%;
            margin: 0 auto;
        }

        .content-area {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 30px;
        }

        .sidebar {
            width: 350px;
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            gap: 30px;
        }

        /* Navigation Tabs inside Dashboard */
        .dashboard-nav {
            display: flex;
            gap: 12px;
            border-bottom: 1px solid var(--border-glass);
            padding-bottom: 12px;
        }

        .nav-btn {
            background: none;
            border: none;
            color: var(--text-muted);
            font-weight: 500;
            padding: 8px 16px;
            border-radius: var(--radius-md);
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .nav-btn:hover {
            color: var(--text-main);
            background: rgba(255, 255, 255, 0.02);
        }

        .nav-btn.active {
            color: var(--text-main);
            background: rgba(99, 102, 241, 0.1);
            border: 1px solid rgba(99, 102, 241, 0.2);
        }

        /* Products Grid */
        .catalog-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
        }

        .search-bar {
            display: flex;
            gap: 10px;
            flex: 1;
            max-width: 400px;
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 24px;
        }

        .product-card {
            padding: 20px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 380px;
        }

        .product-visual {
            height: 140px;
            border-radius: var(--radius-md);
            margin-bottom: 16px;
            background: linear-gradient(135deg, #1e1b4b 0%, #311042 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .product-visual::after {
            content: '';
            position: absolute;
            width: 120%;
            height: 120%;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.15) 0%, transparent 60%);
            top: -10%;
            left: -10%;
        }

        .product-visual svg {
            width: 64px;
            height: 64px;
            opacity: 0.7;
            color: var(--accent-primary);
        }

        .product-title {
            font-family: var(--font-display);
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 6px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .product-sku {
            font-size: 11px;
            font-family: monospace;
            color: var(--text-muted);
            margin-bottom: 8px;
            text-transform: uppercase;
        }

        .product-desc {
            font-size: 13px;
            color: var(--text-muted);
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            margin-bottom: 12px;
            line-height: 1.4;
            height: 36px;
        }

        .product-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: auto;
        }

        .product-price {
            font-family: var(--font-display);
            font-size: 20px;
            font-weight: 700;
            color: #ffffff;
        }

        /* Cart Drawer */
        .cart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--border-glass);
            padding-bottom: 14px;
            margin-bottom: 16px;
        }

        .cart-items {
            display: flex;
            flex-direction: column;
            gap: 12px;
            max-height: 350px;
            overflow-y: auto;
            margin-bottom: 16px;
            padding-right: 4px;
        }

        .cart-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px;
            background: rgba(255, 255, 255, 0.01);
            border: 1px solid var(--border-glass);
            border-radius: var(--radius-md);
        }

        .cart-item-details {
            flex: 1;
            margin-right: 12px;
        }

        .cart-item-title {
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 4px;
        }

        .cart-item-price {
            font-size: 13px;
            color: var(--text-muted);
        }

        .cart-qty-ctrl {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .cart-qty-btn {
            width: 24px;
            height: 24px;
            border-radius: 4px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border-glass);
            color: var(--text-main);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 12px;
        }

        .cart-total-section {
            border-top: 1px solid var(--border-glass);
            padding-top: 14px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-bottom: 10px;
        }

        .cart-total-row {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
            color: var(--text-muted);
        }

        .cart-total-row.grand {
            font-size: 18px;
            font-weight: 700;
            color: #ffffff;
            font-family: var(--font-display);
        }

        /* Admin UI panel */
        .admin-grid {
            display: grid;
            grid-template-columns: 320px 1fr;
            gap: 30px;
        }

        .admin-form-panel {
            padding: 24px;
            height: fit-content;
        }

        .admin-table-panel {
            padding: 24px;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        th {
            text-align: left;
            padding: 12px 16px;
            color: var(--text-muted);
            border-bottom: 1px solid var(--border-glass);
            font-weight: 500;
        }

        td {
            padding: 14px 16px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.02);
            color: var(--text-main);
        }

        tr:hover td {
            background: rgba(255, 255, 255, 0.01);
        }

        /* Modals & Simulators Styling */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1000;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(8px);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            pointer-events: none;
            transition: all 0.3s ease;
        }

        .modal-overlay.show {
            opacity: 1;
            pointer-events: auto;
        }

        .modal-box {
            width: 100%;
            max-width: 500px;
            padding: 30px;
            border-radius: var(--radius-lg);
            transform: scale(0.9);
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .modal-overlay.show .modal-box {
            transform: scale(1);
        }

        .stripe-modal {
            background: #191b22;
            border: 1px solid rgba(103, 114, 229, 0.3);
            box-shadow: 0 0 30px rgba(103, 114, 229, 0.2);
        }

        .bkash-modal {
            background: #1e0914;
            border: 1px solid rgba(226, 19, 110, 0.3);
            box-shadow: 0 0 30px rgba(226, 19, 110, 0.2);
            max-width: 400px;
            padding: 0;
            overflow: hidden;
        }

        /* bKash Specific Mock layout */
        .bkash-header {
            background-color: var(--accent-bkash);
            padding: 20px;
            text-align: center;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .bkash-body {
            padding: 30px 24px;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .bkash-invoice {
            display: flex;
            justify-content: space-between;
            background: rgba(255, 255, 255, 0.02);
            border: 1px dashed rgba(226, 19, 110, 0.3);
            padding: 12px;
            border-radius: 6px;
            font-size: 13px;
        }

        .bkash-btn-group {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-top: 10px;
        }

        /* Stripe Credit Card layout Mock */
        .stripe-cc {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            border-radius: var(--radius-md);
            padding: 24px;
            height: 180px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            margin-bottom: 24px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 20px rgba(0,0,0,0.3);
        }

        .stripe-cc::after {
            content: '';
            position: absolute;
            width: 150px;
            height: 150px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
            top: -50px;
            right: -50px;
        }

        /* Switch Toggle */
        .switch-container {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            cursor: pointer;
        }

        .switch {
            width: 44px;
            height: 24px;
            border-radius: 9999px;
            background: rgba(255,255,255,0.1);
            position: relative;
            transition: background 0.2s ease;
            border: 1px solid var(--border-glass);
        }

        .switch-thumb {
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: white;
            position: absolute;
            top: 2px;
            left: 2px;
            transition: transform 0.2s ease;
        }

        .switch-container.active .switch {
            background: var(--accent-primary);
        }

        .switch-container.active .switch-thumb {
            transform: translateX(20px);
        }

        /* Tab Displays */
        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        /* Order/Payment Lists styling */
        .list-group {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .list-item {
            padding: 20px;
            border: 1px solid var(--border-glass);
            border-radius: var(--radius-md);
            background: rgba(255, 255, 255, 0.01);
        }

        .list-item-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.03);
            padding-bottom: 8px;
        }

        .raw-json-block {
            background: #000000;
            padding: 12px;
            border-radius: 6px;
            font-family: monospace;
            font-size: 12px;
            overflow-x: auto;
            max-height: 200px;
            margin-top: 10px;
            display: none;
        }
    </style>
</head>
<body>

    <!-- Notification Toast -->
    <div id="toast" class="toast"></div>

    <!-- MAIN APP HEADER -->
    <header>
        <div class="logo" onclick="app.showTab('shop')">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--accent-primary)"><circle cx="10" cy="20.5" r="1"/><circle cx="18" cy="20.5" r="1"/><path d="M2.5 2.5h3l2.7 12.4a2 2 0 0 0 2 1.6h7.7a2 2 0 0 0 2-1.6l1.6-8.4H5.1"/></svg>
            GlowCommerce
        </div>
        
        <div class="nav-actions">
            <!-- Dynamic Role Toggle for dev testing -->
            <div id="dev-admin-toggle" class="switch-container" onclick="app.toggleAdminRole()" style="display: none;">
                <span>Admin View</span>
                <div class="switch">
                    <div class="switch-thumb"></div>
                </div>
            </div>
            
            <div id="auth-status-nav" style="display: flex; align-items: center; gap: 16px;">
                <!-- Filled dynamically via JS -->
            </div>
        </div>
    </header>

    <!-- AUTHENTICATION PANELS (Unauthenticated State) -->
    <div id="auth-container" class="auth-container glass-panel" style="margin-top: 80px; display: none;">
        <div class="auth-tabs">
            <div class="auth-tab active" onclick="app.setAuthTab('login')">Login</div>
            <div class="auth-tab" onclick="app.setAuthTab('register')">Register</div>
        </div>
        
        <!-- Login Form -->
        <form id="login-form" onsubmit="app.handleLogin(event)">
            <div class="form-group">
                <label for="login-email">Email Address</label>
                <input type="email" id="login-email" required placeholder="name@example.com">
            </div>
            <div class="form-group" style="margin-bottom: 24px;">
                <label for="login-password">Password</label>
                <input type="password" id="login-password" required placeholder="••••••••">
            </div>
            <button class="btn btn-primary" type="submit" style="width: 100%;">Sign In</button>
        </form>

        <!-- Register Form -->
        <form id="register-form" onsubmit="app.handleRegister(event)" style="display: none;">
            <div class="form-group">
                <label for="reg-name">Full Name</label>
                <input type="text" id="reg-name" required placeholder="John Doe">
            </div>
            <div class="form-group">
                <label for="reg-email">Email Address</label>
                <input type="email" id="reg-email" required placeholder="john@example.com">
            </div>
            <div class="form-group">
                <label for="reg-password">Password</label>
                <input type="password" id="reg-password" required placeholder="Minimum 6 characters">
            </div>
            <div class="form-group" style="margin-bottom: 24px;">
                <label for="reg-password-confirm">Confirm Password</label>
                <input type="password" id="reg-password-confirm" required placeholder="Re-enter password">
            </div>
            <button class="btn btn-primary" type="submit" style="width: 100%;">Create Account</button>
        </form>
    </div>

    <!-- DASHBOARD VIEW (Authenticated State) -->
    <main id="dashboard-view" style="display: none;">
        <div class="content-area">
            
            <!-- Dashboard navigation tabs -->
            <div class="dashboard-nav">
                <button id="tab-btn-shop" class="nav-btn active" onclick="app.showTab('shop')">Shop Storefront</button>
                <button id="tab-btn-history" class="nav-btn" onclick="app.showTab('history')">My Orders & Payments</button>
                <button id="tab-btn-admin" class="nav-btn" onclick="app.showTab('admin')" style="display: none;">Admin Controls</button>
            </div>

            <!-- TAB: SHOP STOREFRONT -->
            <div id="tab-shop" class="tab-content active">
                <div class="catalog-header" style="margin-bottom: 24px;">
                    <h2>Browse Products</h2>
                    <div class="search-bar">
                        <input type="text" id="product-search" placeholder="Search by name or SKU..." oninput="app.renderCatalog()">
                    </div>
                </div>
                
                <div id="products-list-grid" class="products-grid">
                    <!-- Products injected dynamically -->
                </div>
            </div>

            <!-- TAB: ORDERS & PAYMENTS LOGS -->
            <div id="tab-history" class="tab-content">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
                    <div>
                        <h2 style="margin-bottom: 20px;">My Orders</h2>
                        <div id="user-orders-list" class="list-group">
                            <!-- Injected dynamically -->
                        </div>
                    </div>
                    <div>
                        <h2 style="margin-bottom: 20px;">Payment Ledger</h2>
                        <div id="user-payments-list" class="list-group">
                            <!-- Injected dynamically -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB: ADMIN CONTROL PANEL -->
            <div id="tab-admin" class="tab-content">
                <div class="admin-grid">
                    <!-- Add/Edit Product Panel -->
                    <div class="admin-form-panel glass-panel">
                        <h3 id="admin-form-title" style="margin-bottom: 20px;">Create Product</h3>
                        <form id="admin-product-form" onsubmit="app.handleSaveProduct(event)">
                            <input type="hidden" id="admin-prod-id">
                            <div class="form-group">
                                <label for="admin-prod-name">Name</label>
                                <input type="text" id="admin-prod-name" required placeholder="Premium Earbuds">
                            </div>
                            <div class="form-group">
                                <label for="admin-prod-sku">SKU (Unique)</label>
                                <input type="text" id="admin-prod-sku" required placeholder="SKU-EARBUDS">
                            </div>
                            <div class="form-group">
                                <label for="admin-prod-price">Price ($)</label>
                                <input type="number" id="admin-prod-price" step="0.01" min="0" required placeholder="99.99">
                            </div>
                            <div class="form-group">
                                <label for="admin-prod-stock">Stock Quantity</label>
                                <input type="number" id="admin-prod-stock" min="0" required placeholder="50">
                            </div>
                            <div class="form-group">
                                <label for="admin-prod-status">Status</label>
                                <select id="admin-prod-status">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                            <div class="form-group" style="margin-bottom: 20px;">
                                <label for="admin-prod-desc">Description</label>
                                <textarea id="admin-prod-desc" rows="3" placeholder="Detailed product specifications..."></textarea>
                            </div>
                            <div style="display: flex; gap: 10px;">
                                <button type="submit" class="btn btn-primary" style="flex: 1;">Save Product</button>
                                <button type="button" class="btn btn-secondary" onclick="app.resetAdminProductForm()">Cancel</button>
                            </div>
                        </form>
                    </div>

                    <!-- Products Table Panel -->
                    <div class="admin-table-panel glass-panel">
                        <h3 style="margin-bottom: 20px;">Inventory Dashboard</h3>
                        <table id="admin-inventory-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>SKU</th>
                                    <th>Price</th>
                                    <th>Stock</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Injected dynamically -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>

        <!-- SIDEBAR: SHOPPING CART & DOCK -->
        <div class="sidebar">
            <div class="glass-panel" style="padding: 24px;">
                <div class="cart-header">
                    <h3>Shopping Cart</h3>
                    <span id="cart-item-count" class="badge" style="background: var(--accent-primary); color: white;">0 Items</span>
                </div>
                
                <div id="cart-items-container" class="cart-items">
                    <!-- Injected dynamically -->
                </div>
                
                <div class="cart-total-section">
                    <div class="cart-total-row">
                        <span>Subtotal</span>
                        <span id="cart-subtotal">$0.00</span>
                    </div>
                    <div class="cart-total-row grand">
                        <span>Total</span>
                        <span id="cart-total">$0.00</span>
                    </div>
                </div>
                
                <button class="btn btn-primary" onclick="app.initiateCheckout()" style="width: 100%; margin-top: 10px;">Place Order</button>
            </div>
            
            <div class="glass-panel" style="padding: 20px; font-size: 13px; color: var(--text-muted);">
                <h4 style="color: var(--text-main); margin-bottom: 8px; font-family: var(--font-display);">Architecture Notes</h4>
                <p style="margin-bottom: 8px;">• Strategy pattern switches Stripe & bKash payment logic without affecting core order calculations.</p>
                <p style="margin-bottom: 8px;">• Concurrency safe stock reduction is enforced via database transactions with pessimistic locks (`lockForUpdate`).</p>
                <p>• API token is saved securely in local storage and attached to requests.</p>
            </div>
        </div>
    </main>

    <!-- STRIPE SIMULATOR OVERLAY MODAL -->
    <div id="stripe-modal-overlay" class="modal-overlay">
        <div class="modal-box stripe-modal">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3 style="display: flex; align-items: center; gap: 8px; color: white;">
                    <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24"><path d="M13.97 8.53c0-.62-.51-1.07-1.39-1.07-.88 0-1.89.34-2.73.83l-.7-4.14C10.09 3.65 11.45 3.32 13 3.32c3.55 0 5.86 1.83 5.86 5.25 0 4.14-5.69 4.79-5.69 6.27 0 .42.34.8 1.09.8a3.14 3.14 0 0 0 2.54-1.23l.77 4C16.8 19.34 15.22 20 13.56 20c-3.7 0-6.1-1.86-6.1-5.22.02-4.22 5.51-4.91 5.51-6.25z"/></svg>
                    Stripe Payment Simulator
                </h3>
                <span class="badge badge-pending">Sandbox Intent</span>
            </div>

            <!-- CC layout mockup -->
            <div class="stripe-cc">
                <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                    <span style="font-size: 14px; font-weight: 500; letter-spacing: 1px;">CREDIT CARD</span>
                    <span style="font-family: var(--font-display); font-weight: 800; font-size: 20px;">stripe</span>
                </div>
                <div style="font-size: 18px; font-family: monospace; letter-spacing: 2px;">•••• •••• •••• 4242</div>
                <div style="display: flex; justify-content: space-between; font-size: 12px; font-weight: 300;">
                    <span>CARDHOLDER NAME</span>
                    <span>EXP: 12/29</span>
                </div>
            </div>

            <div style="display: flex; flex-direction: column; gap: 14px; margin-bottom: 20px;">
                <div style="display: flex; justify-content: space-between; font-size: 14px;">
                    <span style="color: var(--text-muted);">Amount due</span>
                    <strong id="stripe-modal-amount" style="color: white;">$0.00</strong>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 14px;">
                    <span style="color: var(--text-muted);">Stripe Intent ID</span>
                    <span id="stripe-modal-intent-id" style="font-family: monospace;">pi_xxx</span>
                </div>
            </div>

            <div style="display: flex; gap: 12px;">
                <button class="btn btn-primary" onclick="app.confirmStripePayment(true)" style="flex: 1; background-color: var(--accent-stripe);">Simulate Success</button>
                <button class="btn btn-danger" onclick="app.confirmStripePayment(false)" style="flex: 1;">Simulate Failure</button>
            </div>
            <button class="btn btn-secondary" onclick="app.closeStripeModal()" style="width: 100%; margin-top: 12px;">Cancel Payment</button>
        </div>
    </div>

    <!-- BKASH SIMULATOR OVERLAY MODAL -->
    <div id="bkash-modal-overlay" class="modal-overlay">
        <div class="modal-box bkash-modal">
            <div class="bkash-header">
                <img src="https://www.logo.wine/a/logo/BKash/BKash-Logo.wine.svg" alt="bKash Logo" style="height: 50px; filter: brightness(0) invert(1);">
            </div>
            <div class="bkash-body">
                <div class="bkash-invoice">
                    <div>
                        <span style="color: var(--text-muted); display: block; font-size: 10px;">INVOICE NUMBER</span>
                        <strong id="bkash-modal-invoice">INV-xxx</strong>
                    </div>
                    <div style="text-align: right;">
                        <span style="color: var(--text-muted); display: block; font-size: 10px;">PAYABLE AMOUNT</span>
                        <strong id="bkash-modal-amount" style="color: var(--accent-bkash); font-size: 16px;">৳0.00</strong>
                    </div>
                </div>

                <div class="form-group">
                    <label style="color: var(--text-muted); font-size: 12px;">bKash Payment ID</label>
                    <input type="text" id="bkash-modal-payment-id" readonly style="font-family: monospace; font-size: 12px; background: rgba(0,0,0,0.2);">
                </div>

                <div class="form-group">
                    <label style="color: var(--text-muted); font-size: 12px;">Verify Mobile Number</label>
                    <input type="text" value="017XXXXXXXX" placeholder="01XXXXXXXXX" style="font-size: 13px;">
                </div>

                <div class="bkash-btn-group">
                    <button class="btn btn-primary" onclick="app.executeBkashPayment(true)" style="background-color: var(--accent-bkash);">Execute (Success)</button>
                    <button class="btn btn-danger" onclick="app.executeBkashPayment(false)">Fail Payment</button>
                </div>
                <button class="btn btn-secondary" onclick="app.closeBkashModal()" style="width: 100%;">Close Checkout</button>
            </div>
        </div>
    </div>

    <!-- JAVASCRIPT STATE CONTROLLER -->
    <script>
        class AppState {
            constructor() {
                this.user = null;
                this.token = localStorage.getItem('access_token');
                this.cart = [];
                this.products = [];
                this.activeTab = 'shop';
                this.authTab = 'login';
                this.activeOrder = null;
                
                // Initialize configurations
                this.init();
            }

            async init() {
                if (this.token) {
                    await this.fetchUser();
                } else {
                    this.showAuthPanel();
                }
                this.fetchProducts();
            }

            getHeaders() {
                return {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${this.token}`
                };
            }

            showToast(message, type = 'success') {
                const toast = document.getElementById('toast');
                toast.className = `toast toast-${type} show`;
                toast.innerText = message;
                setTimeout(() => toast.classList.remove('show'), 4000);
            }

            // Auth Functions
            setAuthTab(tab) {
                this.authTab = tab;
                document.querySelectorAll('.auth-tab').forEach(el => el.classList.remove('active'));
                if (tab === 'login') {
                    document.querySelectorAll('.auth-tab')[0].classList.add('active');
                    document.getElementById('login-form').style.display = 'block';
                    document.getElementById('register-form').style.display = 'none';
                } else {
                    document.querySelectorAll('.auth-tab')[1].classList.add('active');
                    document.getElementById('login-form').style.display = 'none';
                    document.getElementById('register-form').style.display = 'block';
                }
            }

            async handleLogin(e) {
                e.preventDefault();
                const email = document.getElementById('login-email').value;
                const password = document.getElementById('login-password').value;

                try {
                    const response = await fetch('/api/login', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                        body: JSON.stringify({ email, password })
                    });
                    const data = await response.json();

                    if (response.ok) {
                        localStorage.setItem('access_token', data.access_token);
                        this.token = data.access_token;
                        this.user = data.user;
                        this.showToast('Logged in successfully!');
                        this.hideAuthPanel();
                        await this.fetchUser();
                        this.fetchProducts();
                    } else {
                        this.showToast(data.message || 'Login failed', 'error');
                    }
                } catch (error) {
                    this.showToast('Login error occurred', 'error');
                }
            }

            async handleRegister(e) {
                e.preventDefault();
                const name = document.getElementById('reg-name').value;
                const email = document.getElementById('reg-email').value;
                const password = document.getElementById('reg-password').value;
                const confirm = document.getElementById('reg-password-confirm').value;

                if (password !== confirm) {
                    this.showToast('Passwords do not match', 'error');
                    return;
                }

                try {
                    const response = await fetch('/api/register', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                        body: JSON.stringify({ name, email, password, password_confirmation: confirm })
                    });
                    const data = await response.json();

                    if (response.ok) {
                        localStorage.setItem('access_token', data.access_token);
                        this.token = data.access_token;
                        this.user = data.user;
                        this.showToast('Account registered successfully!');
                        this.hideAuthPanel();
                        await this.fetchUser();
                        this.fetchProducts();
                    } else {
                        const errorMsg = data.errors ? Object.values(data.errors).flat().join(' ') : (data.message || 'Registration failed');
                        this.showToast(errorMsg, 'error');
                    }
                } catch (error) {
                    this.showToast('Registration error occurred', 'error');
                }
            }

            async fetchUser() {
                try {
                    const response = await fetch('/api/user', { headers: this.getHeaders() });
                    if (response.ok) {
                        this.user = await response.json();
                        this.renderAuthNav();
                        this.hideAuthPanel();
                        document.getElementById('dashboard-view').style.display = 'flex';
                        if (this.user.is_admin) {
                            document.getElementById('tab-btn-admin').style.display = 'block';
                        } else {
                            document.getElementById('tab-btn-admin').style.display = 'none';
                        }
                    } else {
                        // Token expired or invalid
                        this.handleLogout();
                    }
                } catch (error) {
                    this.handleLogout();
                }
            }

            handleLogout() {
                localStorage.removeItem('access_token');
                this.token = null;
                this.user = null;
                this.cart = [];
                this.renderCart();
                this.showAuthPanel();
                this.showToast('Logged out successfully.');
            }

            showAuthPanel() {
                document.getElementById('auth-container').style.display = 'block';
                document.getElementById('dashboard-view').style.display = 'none';
                document.getElementById('dev-admin-toggle').style.display = 'none';
                this.renderAuthNav();
            }

            hideAuthPanel() {
                document.getElementById('auth-container').style.display = 'none';
                document.getElementById('dashboard-view').style.display = 'flex';
                document.getElementById('dev-admin-toggle').style.display = 'flex';
                const adminToggle = document.getElementById('dev-admin-toggle');
                if (this.user && this.user.is_admin) {
                    adminToggle.classList.add('active');
                } else {
                    adminToggle.classList.remove('active');
                }
            }

            // Developer role toggle API call
            async toggleAdminRole() {
                try {
                    const response = await fetch('/api/user/toggle-admin', {
                        method: 'POST',
                        headers: this.getHeaders()
                    });
                    const data = await response.json();
                    if (response.ok) {
                        this.user = data.user;
                        this.showToast(`Switched view to ${this.user.is_admin ? 'Admin' : 'Customer'}`);
                        await this.fetchUser();
                        this.fetchProducts();
                    }
                } catch (error) {
                    this.showToast('Role change error', 'error');
                }
            }

            renderAuthNav() {
                const nav = document.getElementById('auth-status-nav');
                if (this.user) {
                    nav.innerHTML = `
                        <span style="font-size: 14px; font-weight: 500;">
                            ${this.user.name} 
                            <span class="badge ${this.user.is_admin ? 'badge-success' : 'badge-pending'}" style="margin-left: 6px;">
                                ${this.user.is_admin ? 'Admin' : 'Customer'}
                            </span>
                        </span>
                        <button class="btn btn-secondary btn-sm" onclick="app.handleLogout()">Log Out</button>
                    `;
                } else {
                    nav.innerHTML = `
                        <span style="font-size: 13px; color: var(--text-muted)">Unauthenticated Sandbox</span>
                    `;
                }
            }

            // Tabs controls
            showTab(tab) {
                this.activeTab = tab;
                document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
                document.querySelectorAll('.nav-btn').forEach(el => el.classList.remove('active'));
                
                document.getElementById(`tab-${tab}`).classList.add('active');
                document.getElementById(`tab-btn-${tab}`).classList.add('active');

                if (tab === 'history') {
                    this.fetchOrders();
                    this.fetchPayments();
                } else if (tab === 'admin') {
                    this.renderAdminInventory();
                }
            }

            // Products Logic
            async fetchProducts() {
                try {
                    const response = await fetch('/api/products', { headers: this.getHeaders() });
                    if (response.ok) {
                        const data = await response.json();
                        this.products = data.data || [];
                        this.renderCatalog();
                        if (this.user && this.user.is_admin) {
                            this.renderAdminInventory();
                        }
                    }
                } catch (error) {
                    console.error('Products fetch error', error);
                }
            }

            renderCatalog() {
                const grid = document.getElementById('products-list-grid');
                const searchVal = document.getElementById('product-search').value.toLowerCase();
                
                const filtered = this.products.filter(p => 
                    p.name.toLowerCase().includes(searchVal) || p.sku.toLowerCase().includes(searchVal)
                );

                if (filtered.length === 0) {
                    grid.innerHTML = `<div style="grid-column: 1/-1; text-align: center; color: var(--text-muted); padding: 40px;">No active products found match query.</div>`;
                    return;
                }

                grid.innerHTML = filtered.map(p => `
                    <div class="glass-card product-card">
                        <div>
                            <div class="product-visual">
                                <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" /></svg>
                            </div>
                            <div class="product-title">${p.name}</div>
                            <div class="product-sku">SKU: ${p.sku}</div>
                            <div class="product-desc">${p.description || 'No description provided.'}</div>
                        </div>
                        <div class="product-meta">
                            <div>
                                <div class="product-price">$${p.price}</div>
                                <div style="font-size: 11px; color: ${p.stock > 0 ? 'var(--accent-success)' : 'var(--accent-danger)'}">
                                    ${p.stock > 0 ? `Stock: ${p.stock}` : 'Out of Stock'}
                                </div>
                            </div>
                            <button class="btn btn-primary" onclick="app.addToCart(${p.id})" ${p.stock > 0 ? '' : 'disabled'}>
                                Add Cart
                            </button>
                        </div>
                    </div>
                `).join('');
            }

            // Cart Logic
            addToCart(productId) {
                const product = this.products.find(p => p.id === productId);
                if (!product) return;

                const cartItem = this.cart.find(item => item.product_id === productId);
                if (cartItem) {
                    if (cartItem.quantity < product.stock) {
                        cartItem.quantity++;
                    } else {
                        this.showToast('Cannot add more than available stock', 'error');
                    }
                } else {
                    this.cart.push({
                        product_id: productId,
                        name: product.name,
                        price: parseFloat(product.price),
                        quantity: 1,
                        stock: product.stock
                    });
                }
                this.renderCart();
            }

            updateCartQty(productId, amount) {
                const cartItem = this.cart.find(item => item.product_id === productId);
                if (!cartItem) return;

                cartItem.quantity += amount;
                if (cartItem.quantity <= 0) {
                    this.cart = this.cart.filter(item => item.product_id !== productId);
                } else if (cartItem.quantity > cartItem.stock) {
                    cartItem.quantity = cartItem.stock;
                    this.showToast('Cannot exceed product stock', 'error');
                }
                this.renderCart();
            }

            renderCart() {
                const container = document.getElementById('cart-items-container');
                const badge = document.getElementById('cart-item-count');
                const subtotalEl = document.getElementById('cart-subtotal');
                const totalEl = document.getElementById('cart-total');

                const totalItems = this.cart.reduce((sum, item) => sum + item.quantity, 0);
                badge.innerText = `${totalItems} Items`;

                if (this.cart.length === 0) {
                    container.innerHTML = `<div style="text-align: center; color: var(--text-muted); padding: 20px;">Your cart is empty.</div>`;
                    subtotalEl.innerText = '$0.00';
                    totalEl.innerText = '$0.00';
                    return;
                }

                container.innerHTML = this.cart.map(item => `
                    <div class="cart-item">
                        <div class="cart-item-details">
                            <div class="cart-item-title">${item.name}</div>
                            <div class="cart-item-price">$${item.price.toFixed(2)} × ${item.quantity}</div>
                        </div>
                        <div class="cart-qty-ctrl">
                            <button class="cart-qty-btn" onclick="app.updateCartQty(${item.product_id}, -1)">-</button>
                            <span>${item.quantity}</span>
                            <button class="cart-qty-btn" onclick="app.updateCartQty(${item.product_id}, 1)">+</button>
                        </div>
                    </div>
                `).join('');

                const sumAmount = this.cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
                subtotalEl.innerText = `$${sumAmount.toFixed(2)}`;
                totalEl.innerText = `$${sumAmount.toFixed(2)}`;
            }

            // Checkout flow integration
            async initiateCheckout() {
                if (this.cart.length === 0) {
                    this.showToast('Please add items to cart first', 'error');
                    return;
                }
                if (!this.user) {
                    this.showToast('Please sign in to place orders', 'error');
                    this.showAuthPanel();
                    return;
                }

                // Place Order API call
                try {
                    const items = this.cart.map(item => ({
                        product_id: item.product_id,
                        quantity: item.quantity
                    }));

                    const response = await fetch('/api/orders', {
                        method: 'POST',
                        headers: this.getHeaders(),
                        body: JSON.stringify({ items })
                    });
                    const data = await response.json();

                    if (response.ok) {
                        this.showToast('Order created successfully!');
                        this.activeOrder = data.order;
                        this.cart = [];
                        this.renderCart();
                        
                        // Prompt Gateway Selection
                        this.promptPaymentProvider();
                    } else {
                        this.showToast(data.message || 'Order creation failed', 'error');
                    }
                } catch (error) {
                    this.showToast('Error creating order', 'error');
                }
            }

            // Choose provider and call API checkout session
            promptPaymentProvider() {
                // We ask user dynamically via prompt or standard confirm for the provider
                const provider = confirm("Choose payment gateway:\nClick OK for Stripe\nClick Cancel for bKash") ? 'stripe' : 'bkash';
                this.initiatePayment(provider);
            }

            async initiatePayment(provider) {
                if (!this.activeOrder) return;

                this.showToast(`Initiating checkout with ${provider}...`);

                try {
                    const response = await fetch('/api/payments/checkout', {
                        method: 'POST',
                        headers: this.getHeaders(),
                        body: JSON.stringify({
                            order_id: this.activeOrder.id,
                            provider: provider
                        })
                    });
                    const data = await response.json();

                    if (response.ok) {
                        if (provider === 'stripe') {
                            this.openStripeModal(data);
                        } else {
                            this.openBkashModal(data);
                        }
                    } else {
                        this.showToast(data.message || 'Checkout creation failed', 'error');
                    }
                } catch (error) {
                    this.showToast('Checkout gateway error', 'error');
                }
            }

            // Stripe Modal Simulator Actions
            openStripeModal(checkoutData) {
                document.getElementById('stripe-modal-amount').innerText = `$${checkoutData.amount}`;
                document.getElementById('stripe-modal-intent-id').innerText = checkoutData.transaction_id;
                document.getElementById('stripe-modal-overlay').classList.add('show');
            }

            closeStripeModal() {
                document.getElementById('stripe-modal-overlay').classList.remove('show');
                this.showTab('history');
            }

            async confirmStripePayment(simulateSuccess) {
                const transactionId = document.getElementById('stripe-modal-intent-id').innerText;
                document.getElementById('stripe-modal-overlay').classList.remove('show');

                if (simulateSuccess) {
                    try {
                        const response = await fetch('/api/payments/stripe/confirm', {
                            method: 'POST',
                            headers: this.getHeaders(),
                            body: JSON.stringify({ payment_intent_id: transactionId })
                        });
                        const data = await response.json();
                        if (response.ok) {
                            this.showToast('Stripe payment confirmed successfully!');
                            this.fetchProducts();
                            this.showTab('history');
                        } else {
                            this.showToast(data.message || 'Payment confirmation failed', 'error');
                        }
                    } catch (error) {
                        this.showToast('Confirm api connection error', 'error');
                    }
                } else {
                    // Fail intent via webhook simulation or failed confirm request
                    try {
                        const webhookResponse = await fetch('/api/payments/stripe/webhook', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                            body: JSON.stringify({
                                type: 'payment_intent.payment_failed',
                                data: {
                                    object: {
                                        id: transactionId,
                                        amount: 100,
                                        metadata: { order_id: this.activeOrder.id }
                                    }
                                }
                            })
                        });
                        if (webhookResponse.ok) {
                            this.showToast('Stripe payment failed (Simulation). Order Canceled.', 'error');
                            this.showTab('history');
                        }
                    } catch (error) {
                        this.showToast('Failed connection simulator', 'error');
                    }
                }
            }

            // bKash Modal Simulator Actions
            openBkashModal(checkoutData) {
                document.getElementById('bkash-modal-invoice').innerText = 'INV-' + this.activeOrder.id;
                document.getElementById('bkash-modal-amount').innerText = `৳${(checkoutData.amount * 115).toFixed(2)}`; // Mock BDT conversion
                document.getElementById('bkash-modal-payment-id').value = checkoutData.transaction_id;
                document.getElementById('bkash-modal-overlay').classList.add('show');
            }

            closeBkashModal() {
                document.getElementById('bkash-modal-overlay').classList.remove('show');
                this.showTab('history');
            }

            async executeBkashPayment(simulateSuccess) {
                const paymentId = document.getElementById('bkash-modal-payment-id').value;
                document.getElementById('bkash-modal-overlay').classList.remove('show');

                if (simulateSuccess) {
                    try {
                        const response = await fetch('/api/payments/bkash/execute', {
                            method: 'POST',
                            headers: this.getHeaders(),
                            body: JSON.stringify({ payment_id: paymentId })
                        });
                        const data = await response.json();
                        if (response.ok) {
                            this.showToast('bKash payment executed successfully!');
                            this.fetchProducts();
                            this.showTab('history');
                        } else {
                            this.showToast(data.message || 'bKash execution failed', 'error');
                        }
                    } catch (error) {
                        this.showToast('Execute api connection error', 'error');
                    }
                } else {
                    this.showToast('bKash payment execution failed/canceled', 'error');
                    this.showTab('history');
                }
            }

            // Orders & Payments logs fetch
            async fetchOrders() {
                try {
                    const response = await fetch('/api/orders', { headers: this.getHeaders() });
                    if (response.ok) {
                        const data = await response.json();
                        const list = document.getElementById('user-orders-list');
                        const orders = data.data || [];
                        
                        if (orders.length === 0) {
                            list.innerHTML = `<div style="text-align: center; color: var(--text-muted); padding: 20px;">No orders logged.</div>`;
                            return;
                        }

                        list.innerHTML = orders.map(o => `
                            <div class="list-item">
                                <div class="list-item-header">
                                    <strong>Order ID: #${o.id}</strong>
                                    <span class="badge ${o.status === 'paid' ? 'badge-success' : (o.status === 'pending' ? 'badge-pending' : 'badge-danger')}">${o.status}</span>
                                </div>
                                <div style="font-size: 13px; color: var(--text-muted); margin-bottom: 8px;">
                                    Placed: ${new Date(o.created_at).toLocaleString()}
                                </div>
                                <div style="font-size: 14px; margin-bottom: 10px;">
                                    ${o.items.map(item => `• ${item.product?.name || 'Deleted Product'} (Qty: ${item.quantity})`).join('<br>')}
                                </div>
                                <div style="text-align: right; font-weight: 600; font-size: 16px;">
                                    Total: $${parseFloat(o.total_amount).toFixed(2)}
                                </div>
                            </div>
                        `).join('');
                    }
                } catch (error) {
                    console.error('Fetch orders error', error);
                }
            }

            async fetchPayments() {
                try {
                    const response = await fetch('/api/payments', { headers: this.getHeaders() });
                    if (response.ok) {
                        const data = await response.json();
                        const list = document.getElementById('user-payments-list');
                        const payments = data.data || [];

                        if (payments.length === 0) {
                            list.innerHTML = `<div style="text-align: center; color: var(--text-muted); padding: 20px;">No payments logged.</div>`;
                            return;
                        }

                        list.innerHTML = payments.map(p => `
                            <div class="list-item">
                                <div class="list-item-header">
                                    <strong style="text-transform: uppercase;">${p.provider} Checkout</strong>
                                    <span class="badge ${p.status === 'success' ? 'badge-success' : (p.status === 'pending' ? 'badge-pending' : 'badge-danger')}">${p.status}</span>
                                </div>
                                <div style="font-size: 13px; display: flex; flex-direction: column; gap: 4px;">
                                    <span>Transaction: <code style="font-family: monospace; font-size: 12px; color: #a5b4fc;">${p.transaction_id}</code></span>
                                    <span>Order Ref: #${p.order_id}</span>
                                    <span>Amount Paid: $${parseFloat(p.amount).toFixed(2)}</span>
                                    <span>Date: ${new Date(p.created_at).toLocaleString()}</span>
                                </div>
                                <button class="btn btn-secondary btn-sm" onclick="app.toggleRawJson(this)" style="margin-top: 10px; font-size: 11px; padding: 6px 10px;">Show Gateway Payload</button>
                                <pre class="raw-json-block">${JSON.stringify(p.raw_response, null, 2)}</pre>
                            </div>
                        `).join('');
                    }
                } catch (error) {
                    console.error('Fetch payments error', error);
                }
            }

            toggleRawJson(btn) {
                const pre = btn.nextElementSibling;
                if (pre.style.display === 'block') {
                    pre.style.display = 'none';
                    btn.innerText = 'Show Gateway Payload';
                } else {
                    pre.style.display = 'block';
                    btn.innerText = 'Hide Gateway Payload';
                }
            }

            // Admin Inventory Logic
            async renderAdminInventory() {
                const tbody = document.querySelector('#admin-inventory-table tbody');
                
                try {
                    // Fetch all products (since user is admin, index will return active & inactive)
                    const response = await fetch('/api/products', { headers: this.getHeaders() });
                    const data = await response.json();
                    const allProducts = data.data || [];

                    if (allProducts.length === 0) {
                        tbody.innerHTML = `<tr><td colspan="6" style="text-align: center; color: var(--text-muted);">No products registered in inventory.</td></tr>`;
                        return;
                    }

                    tbody.innerHTML = allProducts.map(p => `
                        <tr>
                            <td><strong>${p.name}</strong></td>
                            <td><code style="font-family: monospace;">${p.sku}</code></td>
                            <td>$${p.price}</td>
                            <td>${p.stock}</td>
                            <td>
                                <span class="badge ${p.status === 'active' ? 'badge-success' : 'badge-danger'}">
                                    ${p.status}
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-secondary btn-sm" onclick="app.editAdminProduct(${p.id})" style="padding: 6px 10px; font-size: 12px; margin-right: 6px;">Edit</button>
                                <button class="btn btn-danger btn-sm" onclick="app.deleteAdminProduct(${p.id})" style="padding: 6px 10px; font-size: 12px;">Delete</button>
                            </td>
                        </tr>
                    `).join('');
                } catch (error) {
                    console.error('Inventory fetch error', error);
                }
            }

            async handleSaveProduct(e) {
                e.preventDefault();
                const id = document.getElementById('admin-prod-id').value;
                const name = document.getElementById('admin-prod-name').value;
                const sku = document.getElementById('admin-prod-sku').value;
                const price = document.getElementById('admin-prod-price').value;
                const stock = document.getElementById('admin-prod-stock').value;
                const status = document.getElementById('admin-prod-status').value;
                const description = document.getElementById('admin-prod-desc').value;

                const payload = { name, sku, price, stock, status, description };

                const url = id ? `/api/products/${id}` : '/api/products';
                const method = id ? 'PUT' : 'POST';

                try {
                    const response = await fetch(url, {
                        method: method,
                        headers: this.getHeaders(),
                        body: JSON.stringify(payload)
                    });
                    const data = await response.json();

                    if (response.ok) {
                        this.showToast(`Product ${id ? 'updated' : 'created'} successfully!`);
                        this.resetAdminProductForm();
                        this.fetchProducts();
                    } else {
                        const errorMsg = data.errors ? Object.values(data.errors).flat().join(' ') : (data.message || 'Save failed');
                        this.showToast(errorMsg, 'error');
                    }
                } catch (error) {
                    this.showToast('Save connection error', 'error');
                }
            }

            editAdminProduct(productId) {
                const product = this.products.find(p => p.id === productId);
                if (!product) return;

                document.getElementById('admin-form-title').innerText = 'Edit Product';
                document.getElementById('admin-prod-id').value = product.id;
                document.getElementById('admin-prod-name').value = product.name;
                document.getElementById('admin-prod-sku').value = product.sku;
                document.getElementById('admin-prod-price').value = product.price;
                document.getElementById('admin-prod-stock').value = product.stock;
                document.getElementById('admin-prod-status').value = product.status;
                document.getElementById('admin-prod-desc').value = product.description || '';
            }

            async deleteAdminProduct(productId) {
                if (!confirm("Are you sure you want to delete this product?")) return;

                try {
                    const response = await fetch(`/api/products/${productId}`, {
                        method: 'DELETE',
                        headers: this.getHeaders()
                    });
                    if (response.ok) {
                        this.showToast('Product deleted successfully.');
                        this.fetchProducts();
                    } else {
                        this.showToast('Delete request failed', 'error');
                    }
                } catch (error) {
                    this.showToast('Delete connection error', 'error');
                }
            }

            resetAdminProductForm() {
                document.getElementById('admin-form-title').innerText = 'Create Product';
                document.getElementById('admin-prod-id').value = '';
                document.getElementById('admin-product-form').reset();
            }
        }

        // Instantiate App State
        const app = new AppState();
    </script>
</body>
</html>
