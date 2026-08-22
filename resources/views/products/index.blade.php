<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AromaPOS Coffee - Catalog</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- FontAwesome for Premium Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary: #C08A58; /* Coffee Brown/Amber */
            --primary-glow: rgba(192, 138, 88, 0.15);
            --bg-dark: #0f1115;
            --bg-card: rgba(26, 29, 36, 0.7);
            --bg-input: #232731;
            --border-color: rgba(255, 255, 255, 0.08);
            --text-primary: #f3f4f6;
            --text-secondary: #9ca3af;
            --text-muted: #6b7280;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --font-main: 'Outfit', sans-serif;
            --glass-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: var(--font-main);
        }

        body {
            background-color: var(--bg-dark);
            color: var(--text-primary);
            min-height: 100vh;
            background-image: 
                radial-gradient(at 10% 20%, rgba(192, 138, 88, 0.05) 0px, transparent 50%),
                radial-gradient(at 90% 80%, rgba(59, 130, 246, 0.05) 0px, transparent 50%);
            background-attachment: fixed;
            padding: 2rem 1.5rem;
            line-height: 1.5;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        /* Toast Notifications */
        .toast {
            position: fixed;
            top: 20px;
            right: 20px;
            background: rgba(16, 185, 129, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
            padding: 12px 24px;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.3);
            z-index: 1000;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: slideIn 0.3s ease-out, fadeOut 0.5s ease-in 3.5s forwards;
            font-weight: 500;
        }

        @keyframes slideIn {
            from { transform: translateX(120%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        @keyframes fadeOut {
            from { opacity: 1; }
            to { opacity: 0; visibility: hidden; }
        }

        /* Header Navigation */
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            background: var(--bg-card);
            backdrop-filter: blur(16px);
            border: 1px solid var(--border-color);
            padding: 1rem 1.5rem;
            border-radius: 16px;
            box-shadow: var(--glass-shadow);
        }

        .logo-section {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logo-icon {
            background: var(--primary);
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            box-shadow: 0 0 15px var(--primary);
        }

        .logo-text h1 {
            font-size: 1.3rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            background: linear-gradient(135deg, #fff 0%, var(--primary) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .logo-text span {
            font-size: 0.75rem;
            color: var(--text-secondary);
            display: block;
            margin-top: -2px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            font-size: 0.9rem;
            border: none;
            text-decoration: none;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
            box-shadow: 0 4px 15px rgba(192, 138, 88, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(192, 138, 88, 0.5);
            background: #cd9a6b;
        }

        .btn-secondary {
            background: var(--bg-input);
            color: var(--text-primary);
            border: 1px solid var(--border-color);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.05);
            transform: translateY(-2px);
        }

        .btn-danger {
            background: rgba(239, 68, 68, 0.15);
            color: var(--danger);
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        .btn-danger:hover {
            background: var(--danger);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
        }

        /* Dashboard Overview Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1.25rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: var(--bg-card);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid var(--border-color);
            padding: 1.5rem;
            border-radius: 16px;
            display: flex;
            align-items: center;
            gap: 1rem;
            box-shadow: var(--glass-shadow);
            position: relative;
            overflow: hidden;
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-4px);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 3px;
            background: var(--border-color);
        }

        .stat-card.primary::before { background: var(--primary); }
        .stat-card.success::before { background: var(--success); }
        .stat-card.warning::before { background: var(--warning); }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .stat-card.primary .stat-icon {
            background: rgba(192, 138, 88, 0.1);
            color: var(--primary);
        }

        .stat-card.success .stat-icon {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success);
        }

        .stat-card.warning .stat-icon {
            background: rgba(245, 158, 11, 0.1);
            color: var(--warning);
        }

        .stat-info {
            display: flex;
            flex-direction: column;
        }

        .stat-value {
            font-size: 1.6rem;
            font-weight: 700;
            color: var(--text-primary);
        }

        .stat-label {
            font-size: 0.8rem;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Filter Controls */
        .controls-card {
            background: var(--bg-card);
            backdrop-filter: blur(12px);
            border: 1px solid var(--border-color);
            padding: 1.25rem 1.5rem;
            border-radius: 16px;
            margin-bottom: 1.5rem;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            gap: 1.25rem;
            box-shadow: var(--glass-shadow);
        }

        .search-form {
            display: flex;
            gap: 10px;
            flex: 1;
            min-width: 280px;
            max-width: 500px;
        }

        .input-group {
            position: relative;
            flex: 1;
        }

        .input-group i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
        }

        .form-control {
            width: 100%;
            padding: 10px 14px 10px 40px;
            background: var(--bg-input);
            border: 1px solid var(--border-color);
            border-radius: 10px;
            color: var(--text-primary);
            font-size: 0.95rem;
            transition: all 0.2s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-glow);
        }

        .category-pills {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .pill {
            padding: 8px 16px;
            border-radius: 30px;
            background: var(--bg-input);
            color: var(--text-secondary);
            border: 1px solid var(--border-color);
            font-weight: 500;
            font-size: 0.85rem;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-block;
        }

        .pill:hover {
            color: var(--text-primary);
            border-color: var(--text-muted);
        }

        .pill.active {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
            box-shadow: 0 4px 10px var(--primary-glow);
        }

        /* Products Table Card */
        .table-card {
            background: var(--bg-card);
            backdrop-filter: blur(12px);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            box-shadow: var(--glass-shadow);
            overflow: hidden;
        }

        .table-responsive {
            width: 100%;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        th {
            background: rgba(0, 0, 0, 0.2);
            padding: 1rem 1.5rem;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid var(--border-color);
        }

        td {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--border-color);
            font-size: 0.95rem;
            vertical-align: middle;
        }

        tr:last-child td {
            border-bottom: none;
        }

        tr:hover td {
            background: rgba(255, 255, 255, 0.015);
        }

        .product-meta {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .product-avatar {
            width: 42px;
            height: 42px;
            border-radius: 8px;
            background: var(--bg-input);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            font-size: 1.2rem;
            border: 1px solid var(--border-color);
            object-fit: cover;
        }

        .product-name {
            font-weight: 600;
            color: var(--text-primary);
        }

        .product-desc {
            font-size: 0.75rem;
            color: var(--text-secondary);
            max-width: 250px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 10px;
            border-radius: 30px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .badge-category {
            background: rgba(255, 255, 255, 0.05);
            color: var(--text-secondary);
            border: 1px solid var(--border-color);
        }

        .badge-in-stock {
            background: rgba(16, 185, 129, 0.12);
            color: var(--success);
            border: 1px solid rgba(16, 185, 129, 0.25);
        }

        .badge-low-stock {
            background: rgba(245, 158, 11, 0.12);
            color: var(--warning);
            border: 1px solid rgba(245, 158, 11, 0.25);
        }

        .badge-out-of-stock {
            background: rgba(239, 68, 68, 0.12);
            color: var(--danger);
            border: 1px solid rgba(239, 68, 68, 0.25);
        }

        .star-featured {
            color: var(--warning);
            text-shadow: 0 0 8px rgba(245, 158, 11, 0.4);
        }

        .star-unfeatured {
            color: var(--text-muted);
        }

        .actions-cell {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .btn-icon {
            width: 32px;
            height: 32px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
            background: var(--bg-input);
            border: 1px solid var(--border-color);
            color: var(--text-secondary);
        }

        .btn-icon:hover {
            color: var(--text-primary);
            border-color: var(--text-secondary);
            transform: scale(1.05);
        }

        .btn-icon-edit:hover {
            color: var(--primary);
            border-color: rgba(192, 138, 88, 0.4);
            background: rgba(192, 138, 88, 0.05);
        }

        .btn-icon-delete:hover {
            color: var(--danger);
            border-color: rgba(239, 68, 68, 0.4);
            background: rgba(239, 68, 68, 0.05);
        }

        .no-data {
            padding: 4rem;
            text-align: center;
            color: var(--text-secondary);
        }

        .no-data i {
            font-size: 3rem;
            color: var(--text-muted);
            margin-bottom: 1rem;
            display: block;
        }

        /* Native Dialog / Modal styles */
        dialog::backdrop {
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
        }

        dialog {
            border: none;
            background: transparent;
            width: 100%;
            max-width: 520px;
            padding: 0;
            margin: auto;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            z-index: 10000;
        }

        /* Animate Modal Entrance */
        dialog[open] .modal-container {
            animation: zoomIn 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        @keyframes zoomIn {
            from { transform: scale(0.9); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }

        .modal-container {
            background: #1a1d24;
            border: 1px solid var(--border-color);
            border-radius: 16px;
            overflow: hidden;
            width: 100%;
        }

        .modal-header {
            padding: 1.25rem 1.5rem;
            background: rgba(0, 0, 0, 0.2);
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h3 {
            font-size: 1.15rem;
            font-weight: 600;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .modal-header h3 i {
            color: var(--primary);
        }

        .modal-close {
            background: none;
            border: none;
            color: var(--text-secondary);
            font-size: 1.2rem;
            cursor: pointer;
            transition: color 0.2s ease;
        }

        .modal-close:hover {
            color: var(--danger);
        }

        .modal-body {
            padding: 1.5rem;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .full-width {
            grid-column: span 2;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-group label {
            display: block;
            font-size: 0.85rem;
            font-weight: 500;
            color: var(--text-secondary);
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-control-modal {
            width: 100%;
            padding: 10px 12px;
            background: var(--bg-input);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            color: var(--text-primary);
            font-size: 0.9rem;
            transition: all 0.2s ease;
        }

        .form-control-modal:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-glow);
        }

        textarea.form-control-modal {
            resize: vertical;
            min-height: 80px;
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 10px;
            cursor: pointer;
        }

        .checkbox-group input {
            width: 18px;
            height: 18px;
            accent-color: var(--primary);
            cursor: pointer;
        }

        .checkbox-group span {
            font-size: 0.9rem;
            color: var(--text-primary);
            user-select: none;
        }

        .modal-footer {
            padding: 1.25rem 1.5rem;
            border-top: 1px solid var(--border-color);
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            background: rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>

<div class="container">
    
    <!-- Notifications -->
    @if(session('success'))
        <div class="toast">
            <i class="fa-solid fa-circle-check"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if ($errors->any())
        <div class="toast" style="background: rgba(239, 68, 68, 0.95);">
            <i class="fa-solid fa-circle-exclamation"></i>
            <span>Validation error occurred. Please review inputs.</span>
        </div>
    @endif

    <!-- Header Section -->
    <header>
        <div class="logo-section">
            <div class="logo-icon">
                <i class="fa-solid fa-mug-hot"></i>
            </div>
            <div class="logo-text">
                <h1>AROMAPOS COFFEE</h1>
                <span>Premium Cafe Catalog</span>
            </div>
        </div>
        <button class="btn btn-primary" onclick="document.getElementById('addProductModal').showModal()">
            <i class="fa-solid fa-plus"></i> Add Product
        </button>
    </header>

    <!-- Stats Dashboard -->
    <div class="stats-grid">
        <div class="stat-card primary">
            <div class="stat-icon">
                <i class="fa-solid fa-warehouse"></i>
            </div>
            <div class="stat-info">
                <span class="stat-value">{{ $stats['total_products'] }}</span>
                <span class="stat-label">Total Unique Products</span>
            </div>
        </div>
        <div class="stat-card success">
            <div class="stat-icon">
                <i class="fa-solid fa-dollar-sign"></i>
            </div>
            <div class="stat-info">
                <span class="stat-value">${{ number_format($stats['avg_price'], 2) }}</span>
                <span class="stat-label">Average Catalog Price</span>
            </div>
        </div>
        <div class="stat-card warning">
            <div class="stat-icon">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div class="stat-info">
                <span class="stat-value">{{ $stats['low_stock_count'] }}</span>
                <span class="stat-label">Low Stock Warnings</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(59, 130, 246, 0.1); color: #3b82f6;">
                <i class="fa-solid fa-boxes-stacked"></i>
            </div>
            <div class="stat-info">
                <span class="stat-value">{{ number_format($stats['total_inventory']) }}</span>
                <span class="stat-label">Total Stock Volume</span>
            </div>
        </div>
    </div>

    <!-- Controls Panel -->
    <div class="controls-card">
        <!-- Search -->
        <form action="{{ route('products.index') }}" method="GET" class="search-form">
            <div class="input-group">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" name="search" class="form-control" placeholder="Search product name or description..." value="{{ request('search') }}">
            </div>
            @if(request('category'))
                <input type="hidden" name="category" value="{{ request('category') }}">
            @endif
            <button type="submit" class="btn btn-secondary">Search</button>
            @if(request('search') || request('category'))
                <a href="{{ route('products.index') }}" class="btn btn-icon" title="Clear Filters">
                    <i class="fa-solid fa-xmark"></i>
                </a>
            @endif
        </form>

        <!-- Category pills -->
        <div class="category-pills">
            <a href="{{ request()->fullUrlWithQuery(['category' => 'All']) }}" class="pill {{ !request('category') || request('category') === 'All' ? 'active' : '' }}">All</a>
            @foreach($categories as $cat)
                <a href="{{ request()->fullUrlWithQuery(['category' => $cat]) }}" class="pill {{ request('category') === $cat ? 'active' : '' }}">{{ $cat }}</a>
            @endforeach
        </div>
    </div>

    <!-- Catalog Table -->
    <div class="table-card">
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th style="width: 35%">Product</th>
                        <th style="width: 15%">Category</th>
                        <th style="width: 15%">Price</th>
                        <th style="width: 15%">Stock Status</th>
                        <th style="width: 10%">Featured</th>
                        <th style="width: 10%; text-align: center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr>
                            <td>
                                <div class="product-meta">
                                    @if($product->image_url)
                                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="product-avatar">
                                    @else
                                        <div class="product-avatar">
                                            <i class="fa-solid fa-mug-hot"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <div class="product-name">{{ $product->name }}</div>
                                        <div class="product-desc" title="{{ $product->description }}">{{ $product->description ?: 'No description provided.' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-category">{{ $product->category }}</span>
                            </td>
                            <td style="font-weight: 600; color: var(--text-primary);">
                                ${{ number_format($product->price, 2) }}
                            </td>
                            <td>
                                @if($product->stock <= 0)
                                    <span class="badge badge-out-of-stock"><i class="fa-solid fa-circle-xmark"></i> Out of Stock</span>
                                @elseif($product->stock < 10)
                                    <span class="badge badge-low-stock"><i class="fa-solid fa-triangle-exclamation"></i> Only {{ $product->stock }} left</span>
                                @else
                                    <span class="badge badge-in-stock"><i class="fa-solid fa-circle-check"></i> {{ $product->stock }} In Stock</span>
                                @endif
                            </td>
                            <td>
                                @if($product->is_featured)
                                    <i class="fa-solid fa-star star-featured" title="Featured Product"></i>
                                @else
                                    <i class="fa-regular fa-star star-unfeatured"></i>
                                @endif
                            </td>
                            <td>
                                <div class="actions-cell">
                                    <!-- Edit Button -->
                                    <button class="btn-icon btn-icon-edit" title="Edit Product"
                                            onclick="openEditModal({{ json_encode($product) }})">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    
                                    <!-- Delete Button -->
                                    <form action="{{ route('products.destroy', $product) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this product?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-icon btn-icon-delete" title="Delete Product">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="no-data">
                                    <i class="fa-solid fa-cubes"></i>
                                    <p>No products found matching your search.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ================= ADD PRODUCT MODAL ================= -->
<dialog id="addProductModal">
    <div class="modal-container">
        <div class="modal-header">
            <h3><i class="fa-solid fa-circle-plus"></i> Add New Product</h3>
            <button class="modal-close" onclick="document.getElementById('addProductModal').close()">&times;</button>
        </div>
        <form action="{{ route('products.store') }}" method="POST">
            @csrf
            <div class="modal-body">
                <div class="form-group">
                    <label for="add_name">Product Name *</label>
                    <input type="text" id="add_name" name="name" class="form-control-modal" required placeholder="e.g. Signature Caramel Macchiato">
                </div>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="add_category">Category *</label>
                        <select id="add_category" name="category" class="form-control-modal" required>
                            @foreach($categories as $cat)
                                <option value="{{ $cat }}">{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="add_price">Price (USD) *</label>
                        <input type="number" id="add_price" name="price" class="form-control-modal" step="0.01" min="0.01" required placeholder="e.g. 299.99">
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label for="add_stock">Stock Quantity *</label>
                        <input type="number" id="add_stock" name="stock" class="form-control-modal" min="0" required placeholder="e.g. 50">
                    </div>
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <label class="checkbox-group">
                            <input type="checkbox" name="is_featured" value="1">
                            <span>Feature on dashboard</span>
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label for="add_image">Image URL</label>
                    <input type="url" id="add_image" name="image_url" class="form-control-modal" placeholder="e.g. https://domain.com/photo.jpg">
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label for="add_description">Description</label>
                    <textarea id="add_description" name="description" class="form-control-modal" placeholder="Provide product features or specs..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('addProductModal').close()">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Product</button>
            </div>
        </form>
    </div>
</dialog>

<!-- ================= EDIT PRODUCT MODAL ================= -->
<dialog id="editProductModal">
    <div class="modal-container">
        <div class="modal-header">
            <h3><i class="fa-solid fa-pen-to-square"></i> Edit Product</h3>
            <button class="modal-close" onclick="document.getElementById('editProductModal').close()">&times;</button>
        </div>
        <form id="editForm" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-body">
                <div class="form-group">
                    <label for="edit_name">Product Name *</label>
                    <input type="text" id="edit_name" name="name" class="form-control-modal" required>
                </div>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="edit_category">Category *</label>
                        <select id="edit_category" name="category" class="form-control-modal" required>
                            @foreach($categories as $cat)
                                <option value="{{ $cat }}">{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit_price">Price (USD) *</label>
                        <input type="number" id="edit_price" name="price" class="form-control-modal" step="0.01" min="0.01" required>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label for="edit_stock">Stock Quantity *</label>
                        <input type="number" id="edit_stock" name="stock" class="form-control-modal" min="0" required>
                    </div>
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <label class="checkbox-group">
                            <input type="checkbox" id="edit_featured" name="is_featured" value="1">
                            <span>Feature on dashboard</span>
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label for="edit_image">Image URL</label>
                    <input type="url" id="edit_image" name="image_url" class="form-control-modal">
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label for="edit_description">Description</label>
                    <textarea id="edit_description" name="description" class="form-control-modal"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('editProductModal').close()">Cancel</button>
                <button type="submit" class="btn btn-primary">Update Product</button>
            </div>
        </form>
    </div>
</dialog>

<script>
    // Javascript to handle dynamically populating and opening the native dialog modal
    function openEditModal(product) {
        const modal = document.getElementById('editProductModal');
        const form = document.getElementById('editForm');
        
        // Dynamic action URL
        form.action = `/products/${product.id}`;
        
        // Fill fields
        document.getElementById('edit_name').value = product.name;
        document.getElementById('edit_category').value = product.category;
        document.getElementById('edit_price').value = product.price;
        document.getElementById('edit_stock').value = product.stock;
        document.getElementById('edit_image').value = product.image_url || '';
        document.getElementById('edit_description').value = product.description || '';
        document.getElementById('edit_featured').checked = !!product.is_featured;
        
        // Open native modal dialog
        modal.showModal();
    }
</script>

</body>
</html>
