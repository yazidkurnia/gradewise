# Laravel Blade Layout Structure

## 📁 Struktur File

```
resources/views/
├── layouts/
│   ├── master.blade.php          # Main layout file
│   ├── partials/
│   │   ├── head.blade.php         # HTML head section
│   │   ├── navbar.blade.php       # Top navigation bar
│   │   ├── navbar-search.blade.php # Search component
│   │   ├── navbar-menu.blade.php   # User menu & notifications
│   │   ├── sidebar.blade.php       # Sidebar container
│   │   ├── sidebar-menu.blade.php  # Sidebar menu items
│   │   ├── footer.blade.php        # Footer section
│   │   └── scripts.blade.php       # JavaScript files
│   ├── app.blade.php (OLD)         # Old monolithic layout
│   └── guest.blade.php             # Guest layout
└── pages/
    ├── dashboard.blade.php         # Example dashboard
    └── example.blade.php           # Example page
```

## 🎯 Cara Menggunakan

### 1. Extend Master Layout

```blade
@extends('layouts.master')

@section('content')
    <!-- Your content here -->
@endsection
```

### 2. Menambahkan CSS Khusus

```blade
@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">
@endpush
```

### 3. Menambahkan JavaScript Khusus

```blade
@push('scripts')
    <script src="{{ asset('assets/js/custom.js') }}"></script>
    <script>
        console.log('Custom script');
    </script>
@endpush
```

### 4. Customize Page Title

```blade
@extends('layouts.master', ['title' => 'Dashboard'])
```

### 5. Menambahkan Menu Sidebar

Edit file `resources/views/layouts/partials/sidebar-menu.blade.php`:

```blade
<li class="{{ Request::is('users*') ? 'active' : '' }}">
    <a href="{{ route('users.index') }}" class="nav-link">
        <i class="fas fa-users"></i>
        <span>Users</span>
    </a>
</li>
```

### 6. Menu dengan Dropdown

```blade
<li class="dropdown {{ Request::is('products*') ? 'active' : '' }}">
    <a href="#" class="nav-link has-dropdown">
        <i class="fas fa-box"></i>
        <span>Products</span>
    </a>
    <ul class="dropdown-menu">
        <li><a class="nav-link" href="{{ route('products.index') }}">All Products</a></li>
        <li><a class="nav-link" href="{{ route('products.create') }}">Add Product</a></li>
    </ul>
</li>
```

## 📝 Contoh Halaman Lengkap

```blade
@extends('layouts.master')

@section('content')
    <div class="section-header">
        <h1>Page Title</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
            <div class="breadcrumb-item">Page Title</div>
        </div>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Card Title</h4>
                    </div>
                    <div class="card-body">
                        <!-- Your content -->
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <!-- Page specific CSS -->
@endpush

@push('scripts')
    <!-- Page specific JS -->
@endpush
```

## 🎨 Keuntungan Struktur Baru

1. ✅ **Modular** - Setiap komponen terpisah dan mudah di-maintain
2. ✅ **Reusable** - Bisa digunakan ulang di berbagai halaman
3. ✅ **Clean Code** - Kode lebih bersih dan mudah dibaca
4. ✅ **Easy to Customize** - Mudah untuk customize tanpa merusak layout lain
5. ✅ **DRY Principle** - Don't Repeat Yourself
6. ✅ **Scalable** - Mudah untuk dikembangkan

## 🔧 Tips & Tricks

### Conditional Menu
```blade
@if(auth()->user()->hasRole('admin'))
    <li><a href="{{ route('admin.panel') }}">Admin Panel</a></li>
@endif
```

### Active Menu Helper
```blade
<li class="{{ Request::routeIs('dashboard') ? 'active' : '' }}">
```

### Custom Footer Content
```blade
@extends('layouts.master', ['footerRight' => 'Version 1.0.0'])
```
