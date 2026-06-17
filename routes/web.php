<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\productsController;
use App\Http\Controllers\Admin\authorsController;
use App\Http\Controllers\Admin\ordersController; // Đã sửa tên ở đây
use App\Http\Controllers\User\trangChuController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\publisherController;
use App\Http\Controllers\User\PaymentController;

/*
|--------------------------------------------------------------------------
| 1. ROUTE CÔNG KHAI
|--------------------------------------------------------------------------
*/
Route::get('/', [trangChuController::class, 'index'])->name('user.index');
// Tìm kiếm sản phẩm
Route::get('/search', [trangChuController::class,'search'])->name('user.search');

/*
|--------------------------------------------------------------------------
| 2. TUYẾN ĐƯỜNG TRUNG GIAN ĐIỀU HƯỚNG TỰ ĐỘNG
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', function () {
    if (Auth::user()->role != 1) {
        return redirect('/');
    }
    return view('admin.dashboard');
})->middleware(['auth'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| 3. ROUTE DÀNH CHO QUẢN TRỊ VIÊN (ADMIN)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->middleware('auth')->group(function () {

    Route::get('/dashboard', function () {
        if (Auth::user()->role != 1) {
            return redirect('/');
        }
        return view('admin/dashboard');
    })->name('admin.dashboard');

    // Quản lý sản phẩm
    Route::get('/products', [productsController::class, 'index'])->name('admin.products');
    Route::get('/products/create', [productsController::class, 'create'])->name('admin.productAdd');
    Route::post('/products/store', [productsController::class, 'store'])->name('admin.products.store');
    Route::get('/products/{id}/edit', [productsController::class, 'edit'])->name('admin.products.edit');
    Route::put('/products/{id}/update', [productsController::class, 'update'])->name('admin.products.update');
    Route::post('/products/{id}/toggleStatus', [productsController::class, 'toggleStatus'])->name('admin.products.toggleStatus');
    Route::get('/products/{id}', [productsController::class, 'show'])->name('admin.products.show');
    Route::get('/products/{id}/destroy', [productsController::class, 'destroy'])->name('admin.products.destroy');

    Route::get('/products/{id}/variants', [productsController::class, 'variants'])
        ->name('admin.products.variants');

    Route::get('/products/{id}/variantsCreate', [productsController::class, 'variantsCreate'])
        ->name('admin.products.variants.create');

    Route::post('/products/{id}/variants/store', [productsController::class, 'variantsStore'])
        ->name('admin.products.variants.store');

    Route::put('/products/{id}/variants/{variantId}/update', [productsController::class, 'variantsUpdate'])
        ->name('admin.products.variants.update');

    Route::get('/products/{id}/variants/{variantId}/destroy', [productsController::class, 'variantsDestroy'])
        ->name('admin.products.variants.destroy');

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

    // Quản lý đơn hàng (Đã đồng bộ dùng ordersController và admin.orders)
    Route::get('/orders', [ordersController::class, 'index'])->name('admin.orders');
    Route::get('/orders/{id}', [ordersController::class, 'show'])->name('admin.orders.show');
    Route::get('/orders/{id}/edit', [ordersController::class, 'edit'])->name('admin.orders.edit');
    Route::put('/orders/{id}/update', [ordersController::class, 'update'])->name('admin.orders.update');
    Route::delete('/orders/{id}', [ordersController::class, 'destroy'])->name('admin.orders.destroy');
    Route::post('/orders/{id}/toggleStatus', [ordersController::class, 'toggleStatus'])->name('admin.orders.toggleStatus');
});

/*
|--------------------------------------------------------------------------
| 4. ROUTE PROFILE
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| 5. ROUTE THANH TOÁN
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/checkout', [PaymentController::class, 'index'])->name('checkout.index');
    Route::post('/checkout/process', [PaymentController::class, 'process'])->name('checkout.process');
    Route::get('/vnpay/return', [PaymentController::class, 'vnpayReturn'])->name('vnpay.return');
});

require __DIR__ . '/auth.php';