<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\productsController;
use App\Http\Controllers\Admin\authorsController;
use App\Http\Controllers\Admin\ordersController;
use App\Http\Controllers\User\trangChuController;
use App\Http\Controllers\Admin\publisherController;
use App\Http\Controllers\User\PaymentController;
use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\User\shopDetailsController;
use App\Http\Controllers\User\CartController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| 1. ROUTE CÔNG KHAI (DÀNH CHO KHÁCH HÀNG)
|--------------------------------------------------------------------------
*/
Route::get('/', [trangChuController::class, 'index'])->name('user.index');
Route::get('/search', [trangChuController::class, 'search'])->name('user.search');
Route::get('/product/{id}', [shopDetailsController::class, 'index'])->name('user.productDetails');
Route::post('/cart/add', [App\Http\Controllers\User\CartController::class, 'addToCart'])->name('cart.add');
Route::prefix('cart')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('cart.index');
    Route::post('/', [CartController::class, 'updateCart'])->name('cart.update');
    Route::post('/add', [CartController::class, 'addToCart'])->name('cart.add');
    Route::delete('/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
});
/*
|--------------------------------------------------------------------------
| 2. TUYẾN ĐƯỜNG TRUNG GIAN ĐIỀU HƯỚNG TỰ ĐỘNG
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', function () {
    if ((int)Auth::user()->role !== 1) {
        return redirect('/');
    }
    return redirect()->route('admin.dashboard');
})->middleware(['auth'])->name('dashboard');


/*
|--------------------------------------------------------------------------
| 3. ROUTE VÀO CỔNG ĐĂNG KÝ / ĐĂNG NHẬP CỦA ADMIN (CHƯA ĐĂNG NHẬP)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->prefix('admin')->group(function () {
    // Đăng nhập Admin
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');

    // Đăng ký Admin
    Route::get('/register', [AdminAuthController::class, 'showRegisterForm'])->name('admin.register');
    Route::post('/register', [AdminAuthController::class, 'register'])->name('admin.register.submit');
});


/*
|--------------------------------------------------------------------------
| 4. KHU VỰC QUẢN TRỊ VIÊN (ADMIN PANEL) - ĐÃ QUA PHÊ DUYỆT BẢO MẬT
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {

    // Trang chủ quản trị (Đã trỏ chuẩn về file view cấu trúc admin/dashboard.blade.php của bạn)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    // Đăng xuất Admin
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

    // Quản lý sản phẩm
    Route::get('/products', [productsController::class, 'index'])->name('admin.products');
    Route::get('/products/create', [productsController::class, 'create'])->name('admin.productAdd');
    Route::post('/products/store', [productsController::class, 'store'])->name('admin.products.store');
    Route::get('/products/{id}/edit', [productsController::class, 'edit'])->name('admin.products.edit');
    Route::put('/products/{id}/update', [productsController::class, 'update'])->name('admin.products.update');
    Route::post('/products/{id}/toggleStatus', [productsController::class, 'toggleStatus'])->name('admin.products.toggleStatus');
    Route::get('/products/{id}', [productsController::class, 'show'])->name('admin.products.show');
    Route::get('/products/{id}/destroy', [productsController::class, 'destroy'])->name('admin.products.destroy');

    // Quản lý nhà xuất bản
    Route::resource('publishers', publisherController::class)->names('admin.publishers');

    // Quản lý tác giả
    Route::get('/authors', [authorsController::class, 'index'])->name('admin.authors');
    Route::get('/authors/create', [authorsController::class, 'authorCreate'])->name('admin.authorAdd');
    Route::post('/authors/store', [authorsController::class, 'authorStore'])->name('admin.authors.store');
    Route::get('/authors/{id}/edit', [authorsController::class, 'authorEdit'])->name('admin.authors.edit');
    Route::put('/authors/{id}/update', [authorsController::class, 'authorUpdate'])->name('admin.authors.update');
    Route::get('/authors/{id}/destroy', [authorsController::class, 'authorDestroy'])->name('admin.authors.destroy');
    Route::post('/authors/{id}/toggleStatus', [authorsController::class, 'authorToggleStatus'])->name('admin.authors.toggleStatus');
    Route::get('/authors/{id}', [authorsController::class, 'authorShow'])->name('admin.authors.show');

    // Quản lý đơn hàng
    Route::get('/orders', [ordersController::class, 'index'])->name('admin.orders');
    Route::get('/orders/{id}', [ordersController::class, 'show'])->name('admin.orders.show');
    Route::get('/orders/{id}/edit', [ordersController::class, 'edit'])->name('admin.orders.edit');
    Route::put('/orders/{id}/update', [ordersController::class, 'update'])->name('admin.orders.update');
    Route::delete('/orders/{id}', [ordersController::class, 'destroy'])->name('admin.orders.destroy');
    Route::post('/orders/{id}/toggleStatus', [ordersController::class, 'toggleStatus'])->name('admin.orders.toggleStatus');
});


/*
|--------------------------------------------------------------------------
| 5. ROUTE PROFILE (USER THƯỜNG / ADMIN CHUNG)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


/*
|--------------------------------------------------------------------------
| 6. ROUTE THANH TOÁN (DÀNH CHO KHÁCH HÀNG)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/checkout', [PaymentController::class, 'index'])->name('checkout.index');
    Route::post('/checkout/process', [PaymentController::class, 'process'])->name('checkout.process');
    Route::get('/vnpay/return', [PaymentController::class, 'vnpayReturn'])->name('vnpay.return');
});

// Các tuyến đường auth mặc định của hệ thống
require __DIR__ . '/auth.php';