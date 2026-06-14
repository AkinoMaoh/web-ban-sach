<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\trangChuController;
use App\Http\Controllers\Admin\productsController;

Route::get('/', [trangChuController::class, 'index'])
    ->name('user.index');
Route::prefix('admin')->group(function () {
    // Thống kê dashboard
    Route::get('/dashboard', function () {
        return view('admin/dashboard');
    })->name('admin.dashboard');

    // Quản lý sản phẩm
    Route::get('/products', [productsController::class, 'index'])
        ->name('admin.products');

    Route::get('/products/create', [productsController::class, 'create'])
        ->name('admin.productAdd');

    Route::post('/products/store', [productsController::class, 'store'])
        ->name('admin.products.store');

    Route::get('/products/{id}/edit', [productsController::class, 'edit'])
        ->name('admin.products.edit');

    Route::put('/products/{id}/update', [productsController::class, 'update'])
        ->name('admin.products.update');

    Route::post('/products/{id}/toggleStatus', [productsController::class, 'toggleStatus'])
        ->name('admin.products.toggleStatus');

    Route::get('/products/{id}', [productsController::class, 'show'])
        ->name('admin.products.show');

    Route::get('/products/{id}/destroy', [productsController::class, 'destroy'])
        ->name('admin.products.destroy');
});
