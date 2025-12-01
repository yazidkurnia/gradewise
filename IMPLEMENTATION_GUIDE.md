# Dashboard Implementation Guide

## 📋 Struktur Implementasi

Implementasi routing `/dashboard` ke `views/pages/adm_dashboard/index.blade.php` menggunakan pattern MVC (Model-View-Controller).

---

## 🗂️ File Structure

```
app/
└── Http/
    └── Controllers/
        └── DashboardController.php     # Controller untuk dashboard

routes/
└── web.php                             # File routing utama

resources/
└── views/
    ├── layouts/
    │   ├── master.blade.php            # Master layout
    │   └── partials/                   # Komponen reusable
    └── pages/
        └── adm_dashboard/
            └── index.blade.php         # Dashboard view
```

---

## 📝 Implementasi Detail

### 1. Controller (`app/Http/Controllers/DashboardController.php`)

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('pages.adm_dashboard.index');
    }
}
```

**Penjelasan:**
- Method `index()` mengembalikan view `pages.adm_dashboard.index`
- Laravel akan mencari file di `resources/views/pages/adm_dashboard/index.blade.php`
- Notasi dot (`.`) digunakan untuk folder separator

---

### 2. Routes (`routes/web.php`)

```php
use App\Http\Controllers\DashboardController;

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth'])
    ->name('dashboard');
```

**Penjelasan:**
- URL `/dashboard` akan dihandle oleh `DashboardController@index`
- Middleware `auth` memastikan hanya user yang login bisa akses
- Route name `dashboard` untuk referensi di blade: `route('dashboard')`

---

### 3. View (`resources/views/pages/adm_dashboard/index.blade.php`)

```blade
@extends('layouts.master')

@section('content')
    <!-- Dashboard content here -->
@endsection

@push('styles')
    <!-- Page specific CSS -->
@endpush

@push('scripts')
    <!-- Page specific JS -->
@endpush
```

**Struktur View:**
- Extends dari `layouts.master` untuk konsistensi layout
- `@section('content')` untuk isi halaman utama
- `@push('styles')` untuk CSS khusus halaman
- `@push('scripts')` untuk JavaScript khusus halaman

---

## 🚀 Cara Membuat Halaman Baru

### Langkah 1: Buat Controller

```bash
php artisan make:controller NamaController
```

### Langkah 2: Edit Controller

```php
public function index()
{
    return view('pages.nama_folder.index');
}
```

### Langkah 3: Tambah Route

```php
use App\Http\Controllers\NamaController;

Route::get('/url-path', [NamaController::class, 'index'])
    ->middleware(['auth'])
    ->name('route.name');
```

### Langkah 4: Buat View File

```
resources/views/pages/nama_folder/index.blade.php
```

---

## 🎯 Pattern yang Digunakan

### Naming Convention

1. **Controller**: PascalCase dengan suffix `Controller`
   - ✅ `DashboardController`
   - ✅ `UserController`
   - ❌ `dashboardController`

2. **Route Name**: snake_case atau dot notation
   - ✅ `dashboard`
   - ✅ `admin.users.index`
   - ❌ `DashboardIndex`

3. **View Path**: snake_case dengan folder structure
   - ✅ `pages.adm_dashboard.index`
   - ✅ `admin.users.create`
   - ❌ `pages/admDashboard/Index`

---

## 🔧 Advanced Implementation

### Passing Data ke View

```php
public function index()
{
    $users = User::all();
    $totalUsers = User::count();

    return view('pages.adm_dashboard.index', [
        'users' => $users,
        'totalUsers' => $totalUsers
    ]);
}
```

Atau menggunakan `compact()`:

```php
public function index()
{
    $users = User::all();
    $totalUsers = User::count();

    return view('pages.adm_dashboard.index', compact('users', 'totalUsers'));
}
```

### Menggunakan Data di View

```blade
<p>Total Users: {{ $totalUsers }}</p>

@foreach($users as $user)
    <p>{{ $user->name }}</p>
@endforeach
```

---

## 🛡️ Middleware Options

### Auth Middleware
```php
->middleware(['auth'])  // Hanya user login
```

### Multiple Middleware
```php
->middleware(['auth', 'verified'])  // Login + email verified
```

### Role-based Access
```php
->middleware(['auth', 'role:admin'])  // Hanya admin
->middleware(['auth', 'role:admin,manager'])  // Admin atau Manager
```

---

## 🔐 Role Middleware - Cara Penggunaan

### Setup (Sudah Dilakukan)
Role middleware sudah terdaftar di `app/Http/Kernel.php`:
```php
protected $routeMiddleware = [
    'role' => \App\Http\Middleware\RoleMiddleware::class,
];
```

### Cara Memanfaatkan

#### 1. Di Routes (Cara Paling Umum)
```php
// Single role - hanya admin
Route::get('/admin/users', [UserController::class, 'index'])
    ->middleware(['auth', 'role:admin'])
    ->name('users.index');

// Multiple roles - admin atau manager
Route::post('/reports', [ReportController::class, 'store'])
    ->middleware(['auth', 'role:admin,manager'])
    ->name('reports.store');

// Grouped routes dengan role
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);
});
```

#### 2. Di Controller
```php
class AdminController extends Controller
{
    public function __construct()
    {
        // Terapkan role middleware ke semua method
        $this->middleware('role:admin');
    }

    public function dashboard()
    {
        return view('admin.dashboard');
    }
}
```

#### 3. Di Controller untuk Method Tertentu
```php
class UserController extends Controller
{
    public function __construct()
    {
        // Hanya method 'destroy' dan 'delete' yang butuh role admin
        $this->middleware('role:admin')->only(['destroy', 'delete']);
    }

    public function index()
    {
        return view('users.index');  // Accessible tanpa role check
    }

    public function destroy($id)
    {
        User::destroy($id);  // Hanya admin bisa akses
    }
}
```

### Contoh Implementasi Lengkap

#### Routes dengan Role (routes/web.php)
```php
Route::middleware(['auth'])->group(function () {
    // Dashboard - semua user login bisa akses
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    // Admin routes - hanya role admin
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/users', [UserController::class, 'index'])->name('admin.users');
        Route::delete('/admin/users/{id}', [UserController::class, 'destroy']);
    });

    // Manager routes - admin atau manager
    Route::middleware('role:admin,manager')->group(function () {
        Route::get('/reports', [ReportController::class, 'index'])->name('reports');
        Route::post('/reports', [ReportController::class, 'store']);
    });
});
```

### Error Handling
Ketika user tidak memiliki role yang diizinkan, middleware akan return:
- **Status Code**: 403 (Forbidden)
- **Pesan**: "Unauthorized action. You do not have permission to access this page."

Jika user belum login:
- **Redirect** ke halaman login dengan pesan: "Anda belum login..."

### Notes Penting
1. Role diambil dari field `linked_type` di tabel users (case-insensitive)
2. Support multiple roles dengan separator koma: `role:admin,manager,supervisor`
3. Harus digunakan setelah middleware `auth`
4. Middleware akan stop eksekusi jika user tidak punya role

---

## 🔍 Debugging

### Cek Route Terdaftar
```bash
php artisan route:list
php artisan route:list --name=dashboard
```

### Cek Controller Exists
```bash
php artisan route:list | grep DashboardController
```

### Clear Cache
```bash
php artisan route:clear
php artisan view:clear
php artisan cache:clear
```

---

## ✅ Testing

### Manual Test
1. Login ke aplikasi
2. Akses URL: `http://localhost:8000/dashboard`
3. Verifikasi view ditampilkan dengan benar

### Route Test
```bash
php artisan route:list --name=dashboard
# Output: GET|HEAD dashboard ... DashboardController@index
```

---

## 📚 Best Practices

1. **Gunakan Controller** untuk logic, bukan closure di route
2. **Naming Consistency** untuk mudah maintenance
3. **Middleware** untuk security
4. **Resource Controllers** untuk CRUD operations
5. **View Partials** untuk reusable components
6. **Comments** di code untuk dokumentasi

---

## 🎨 Contoh Resource Controller (CRUD Lengkap)

```php
// Generate resource controller
php artisan make:controller UserController --resource

// Route resource
Route::resource('users', UserController::class)
    ->middleware(['auth']);

// Menghasilkan routes:
// GET    /users          -> index
// GET    /users/create   -> create
// POST   /users          -> store
// GET    /users/{id}     -> show
// GET    /users/{id}/edit -> edit
// PUT    /users/{id}     -> update
// DELETE /users/{id}     -> destroy
```

---

## 📞 Troubleshooting

### Error: View not found
- Cek path view di controller
- Pastikan file exists di `resources/views/`
- Clear view cache: `php artisan view:clear`

### Error: Route not found
- Cek `routes/web.php`
- Clear route cache: `php artisan route:clear`
- Jalankan: `php artisan route:list`

### Error: Controller not found
- Cek namespace di route
- Pastikan `use App\Http\Controllers\ControllerName;`
- Jalankan: `composer dump-autoload`

---

---

## ✅ Checklist Siap Menjalankan Aplikasi di Master Branch

Hasil verifikasi pada **2025-11-30**:

### Status Repo & Environment
- ✅ **Branch**: Master (merge dari authentication berhasil)
- ✅ **Latest Commit**: `5b8550b` - Merge pull request #1 from yazidkurnia/authentication
- ✅ **PHP Version**: 8.4.15 (memenuhi requirement ^8.2)
- ✅ **Laravel Version**: 11.0
- ✅ **Database Connection**: MySQL (DB_CONNECTION=mysql)
- ✅ **.env File**: Sudah dikonfigurasi dengan APP_KEY
- ✅ **Database**: database.sqlite tersedia

### File & Middleware
- ✅ **RoleMiddleware**: Terdaftar di `app/Http/Kernel.php`
- ✅ **RoleMiddleware file**: Tersedia di `app/Http/Middleware/RoleMiddleware.php`
- ✅ **DashboardController**: Tersedia
- ✅ **Routes**: Terdaftar dengan role middleware

### Database Migrations
- ✅ `2014_10_12_000000_create_users_table` - Ran
- ✅ `2014_10_12_100000_create_password_resets_table` - Ran
- ✅ `2019_08_19_000000_create_failed_jobs_table` - Ran
- ✅ `2019_12_14_000001_create_personal_access_tokens_table` - Ran
- ✅ `2025_11_08_161803_create_roles_table` - Ran

### Langkah Untuk Menjalankan di Master

#### 1. Update Dependencies (jika diperlukan)
```bash
composer install
npm install
```

#### 2. Generate Assets (jika menggunakan Vite/Mix)
```bash
npm run build
# atau untuk development
npm run dev
```

#### 3. Cache Configuration (opsional tapi recommended)
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

#### 4. Jalankan Server
```bash
php artisan serve
# atau gunakan Laragon built-in server
```

#### 5. Test Akses
- Buka `http://localhost:8000`
- Akses `/dashboard` - akan redirect ke login (auth middleware aktif)
- Login dengan akun yang memiliki role 'admin' (linked_type = 'admin')
- Role middleware akan memeriksa linked_type field dari users table

### ⚠️ Hal Penting yang Perlu Diperhatikan

1. **Database User**: Pastikan ada user dengan `linked_type` = 'admin' untuk mengakses dashboard
   ```php
   // Cek di database atau via tinker:
   php artisan tinker
   > User::first()->linked_type  // harus ada nilai 'admin'
   ```

2. **Migration Database**: Jika belum di-migrate, jalankan:
   ```bash
   php artisan migrate
   ```

3. **Seeding Data (opsional)**: Jika ingin test data:
   ```bash
   php artisan db:seed
   ```

4. **Environment**: Pastikan APP_ENV=local di .env untuk development

5. **Key Generation**: APP_KEY sudah di-set, jangan di-reset

### 🔍 Debugging Jika Ada Error

#### Error: "SQLSTATE[HY000]: General error: 1 database table is locked"
```bash
# Clear database lock
php artisan migrate:refresh  # WARNING: Ini akan drop semua data
```

#### Error: "Class App\Http\Middleware\RoleMiddleware not found"
```bash
composer dump-autoload
php artisan cache:clear
```

#### Error: "View not found"
```bash
php artisan view:clear
php artisan cache:clear
```

#### Error: "Unauthorized action" pada /dashboard
- Pastikan user login memiliki `linked_type = 'admin'`
- Check di database: `SELECT * FROM users WHERE email = 'your-email';`

### Kesimpulan

✅ **APLIKASI SIAP DIJALANKAN DI MASTER BRANCH!**

Semua dependencies, migrations, dan middleware sudah ter-setup dengan baik. Anda hanya perlu:
1. Pastikan database ada data user dengan role yang sesuai
2. Jalankan `php artisan serve`
3. Akses aplikasi di browser

---

## 🔄 Panduan Setup Ketika Clone Repository

Jika Anda akan menggunakan repository ini di project yang berbeda atau machine yang berbeda, ikuti panduan ini:

### Phase 1: Clone & Initial Setup

#### Langkah 1: Clone Repository
```bash
git clone <repository-url> nama-project
cd nama-project
```

#### Langkah 2: Copy Environment File
```bash
cp .env.example .env
```

**Atau edit .env secara manual dengan konfigurasi berikut:**
```env
APP_NAME=Laravel
APP_ENV=local
APP_KEY=        # Akan di-generate di langkah berikutnya
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database Configuration (sesuaikan dengan setup lokal)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nama_database_anda
DB_USERNAME=root
DB_PASSWORD=

# Atau jika menggunakan SQLite:
DB_CONNECTION=sqlite
# DB_DATABASE=/absolute/path/to/database.sqlite
```

#### Langkah 3: Generate Application Key
```bash
php artisan key:generate
```

**Output yang diharapkan:**
```
Application key set successfully.
```

---

### Phase 2: Install Dependencies

#### Langkah 4: Install PHP Dependencies
```bash
composer install
```

**Apa yang akan terjadi:**
- Download dan install semua packages di `composer.json`
- Membuat folder `vendor/`
- Mengupdate `composer.lock`

#### Langkah 5: Install Node Dependencies
```bash
npm install
```

**Apa yang akan terjadi:**
- Download dan install semua packages di `package.json`
- Membuat folder `node_modules/`
- Mengupdate `package-lock.json`

**Durasi:** Bisa 2-10 menit tergantung kecepatan internet

#### Langkah 6: Compile Frontend Assets
```bash
npm run production
# Atau untuk development:
npm run dev
```

**Apa yang akan terjadi:**
- Compile CSS dan JS dari `resources/`
- Generate file di `public/css/app.css` dan `public/js/app.js`

---

### Phase 3: Database Setup

#### Langkah 7: Buat Database (jika belum ada)

**Untuk MySQL:**
```bash
# Menggunakan MySQL CLI:
mysql -u root -p -e "CREATE DATABASE nama_database_anda;"

# Atau via PhpMyAdmin:
# 1. Buka http://localhost/phpmyadmin
# 2. Create new database
```

**Untuk SQLite:**
```bash
# Otomatis dibuat saat migration, tidak perlu manual
```

#### Langkah 8: Jalankan Database Migration
```bash
php artisan migrate
```

**Output yang diharapkan:**
```
  2014_10_12_000000_create_users_table ..................... 0.15s DONE
  2014_10_12_100000_create_password_resets_table .......... 0.08s DONE
  2019_08_19_000000_create_failed_jobs_table ............. 0.12s DONE
  2019_12_14_000001_create_personal_access_tokens_table .. 0.11s DONE
  2025_11_08_161803_create_roles_table .................... 0.09s DONE
```

#### Langkah 9 (Opsional): Seed Database dengan Data Dummy
```bash
php artisan db:seed
```

**atau seeding spesifik:**
```bash
php artisan db:seed --class=UserSeeder
```

---

### Phase 4: Verifikasi & Testing

#### Langkah 10: Pastikan Semua File Penting Ada

**Checklist file yang harus ada:**
```bash
# Routes
✓ routes/web.php
✓ routes/auth.php

# Controllers
✓ app/Http/Controllers/DashboardController.php

# Middleware
✓ app/Http/Middleware/RoleMiddleware.php
✓ app/Http/Kernel.php (dengan 'role' middleware registered)

# Views
✓ resources/views/layouts/master.blade.php
✓ resources/views/pages/adm_dashboard/index.blade.php

# Database
✓ database/migrations/ (semua migration files)

# Assets
✓ public/css/app.css
✓ public/js/app.js

# Config
✓ .env (sudah dikonfigurasi)
✓ composer.lock
✓ package-lock.json
```

#### Langkah 11: Clear Caches
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
```

#### Langkah 12: Regenerate Cache (Recommended)
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

#### Langkah 13: Test Server
```bash
php artisan serve
```

**Buka di browser:**
- `http://localhost:8000` - Harus menampilkan welcome page
- `http://localhost:8000/login` - Harus menampilkan login page
- `http://localhost:8000/dashboard` - Harus redirect ke login (auth middleware)

---

### Phase 5: Post-Setup Configuration

#### Update .env sesuai kebutuhan:

```env
# Application
APP_NAME="Nama Aplikasi Anda"
APP_ENV=local  # Ubah ke 'production' untuk live

# Database
DB_DATABASE=nama_db_sesuai_project
DB_USERNAME=user_db
DB_PASSWORD=password_db

# Email (jika diperlukan)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password

# Cache & Session
CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync
```

---

## 📋 Checklist Setup untuk Project Baru

Ceklist ini untuk memastikan semuanya siap sebelum development:

```
Clone & Environment
  ☐ git clone repository
  ☐ cp .env.example .env
  ☐ php artisan key:generate
  ☐ Update DB_* di .env sesuai local setup

Install Dependencies
  ☐ composer install
  ☐ npm install
  ☐ npm run production (atau npm run dev)

Database
  ☐ Create database (MySQL/SQLite)
  ☐ php artisan migrate
  ☐ php artisan db:seed (optional)
  ☐ Verify: php artisan migrate:status

Verification
  ☐ php artisan cache:clear
  ☐ php artisan config:cache
  ☐ php artisan serve
  ☐ Test di browser: http://localhost:8000

Optional
  ☐ php artisan tinker (untuk test query)
  ☐ Create test user dengan role 'admin'
  ☐ composer dump-autoload (jika ada error)
```

---

## 🆘 Troubleshooting Saat Setup

### Error: "No application key has been generated"
```bash
php artisan key:generate
```

### Error: "Could not find package"
```bash
composer install --no-cache
# atau update composer
composer self-update
```

### Error: "permission denied" saat npm install
```bash
# Windows (gunakan Command Prompt/PowerShell sebagai Administrator)
npm install

# Linux/Mac
sudo npm install -g npm
npm install
```

### Error: "Class not found" setelah composer install
```bash
composer dump-autoload
```

### Error: "Database does not exist"
```bash
# MySQL - Create database
mysql -u root -p -e "CREATE DATABASE nama_database;"

# Atau ubah .env ke SQLite (lebih mudah untuk development)
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database.sqlite
```

### Error: "Migration already exists"
```bash
# Rollback semua migration
php artisan migrate:reset

# Atau fresh migrate (WARNING: drop semua data)
php artisan migrate:fresh
```

### Error: "View not found"
```bash
# Clear view cache
php artisan view:clear

# Verify views ada di resources/views/
ls resources/views/
```

---

## 📊 Ringkasan Waktu Setup

| Tahap | Durasi | Dependencies |
|---|---|---|
| Clone & .env | 1 menit | Git |
| App Key Generate | 1 detik | PHP |
| Composer Install | 2-5 menit | Internet speed |
| NPM Install | 3-10 menit | Internet speed |
| Compile Assets | 1-2 menit | Node/npm |
| Database Setup | 1-2 menit | Database server |
| Migration | 1-2 detik | Database |
| Seed Data | 1-5 menit | Database |
| **Total** | **10-30 menit** | Bergantung internet & hardware |

---

## ⚡ Command Quick Reference

```bash
# Setup awal (first time)
git clone <url> && cd nama-project
cp .env.example .env
php artisan key:generate
composer install && npm install
npm run production
php artisan migrate
php artisan db:seed
php artisan serve

# Restart (jika ada error)
php artisan cache:clear && php artisan config:clear
php artisan migrate:refresh --seed
php artisan serve

# Development mode
npm run watch    # Otomatis compile saat ada perubahan CSS/JS

# Production ready
npm run production
php artisan config:cache
php artisan route:cache
```

---

## 🎯 Yang Paling Penting Diingat

1. **Jangan commit .env** - Setiap developer punya .env sendiri
2. **composer.lock dan package-lock.json** - WAJIB di-commit ke git
3. **vendor/ dan node_modules/** - JANGAN di-commit (di .gitignore)
4. **Jalankan migration** - Setiap ada file migration baru
5. **Clear cache** - Jika ada error/perubahan config
6. **Compile assets** - Jika ada perubahan CSS/JS
7. **Test di browser** - Sebelum commit ke git

**Dokumentasi dibuat pada:** 2025-11-30
