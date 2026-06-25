<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\productsController;
use App\Http\Controllers\Admin\authorsController;
use App\Http\Controllers\User\trangChuController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\publisherController;
use App\Http\Controllers\User\PaymentController;

/*
|--------------------------------------------------------------------------
| 1. ROUTE CÔNG KHAI (CHƯA ĐĂNG NHẬP CŨNG XEM ĐƯỢC)
|--------------------------------------------------------------------------
*/
// Trang chủ hiển thị danh sách sách cho mọi đối tượng
Route::get('/', [trangChuController::class, 'index'])->name('user.index');


/*
|--------------------------------------------------------------------------
| 2. TUYẾN ĐƯỜNG TRUNG GIAN ĐIỀU HƯỚNG TỰ ĐỘNG (Xử lý lỗi kẹt đăng nhập)
|--------------------------------------------------------------------------
| Khi bấm đăng nhập, Breeze luôn gọi trang này đầu tiên. Ta sẽ dùng nó làm
| trạm trung chuyển để phân chia User và Admin.
*/
Route::get('/dashboard', function () {
    // Giả sử bạn phân biệt Admin bằng email hoặc bằng một cột 'role' trong DB.
    // Ở đây mình ví dụ nếu email là 'admin@gmail.com' thì vào admin, còn lại về trang chủ.
    if (Auth::user()->role != 1) {
        return redirect('/');
    }

    return view('admin.dashboard');
})->middleware(['auth'])->name('dashboard');


/*
|--------------------------------------------------------------------------
| 3. ROUTE DÀNH CHO QUẢN TRỊ VIÊN (ADMIN) - BẢO MẬT
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->middleware('auth')->group(function () {

    // Trang tổng quan Admin
    Route::get('/dashboard', function () {
        if (Auth::user()->role != 1) {
            return redirect('/');
        }

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

    // Quản lý nhà xuất bản
    Route::prefix('admin')->name('admin.')->group(function(){
        Route::resource(
            'publishers',
            publisherController::class
        );
    });

    Route::get('/authors', [authorsController::class, 'index'])
        ->name('admin.authors');

    Route::get('/authors/create', [authorsController::class, 'authorCreate'])
        ->name('admin.authorAdd');

    Route::post('/authors/store', [authorsController::class, 'authorStore'])
        ->name('admin.authors.store');

    Route::get('/authors/{id}/edit', [authorsController::class, 'authorEdit'])
        ->name('admin.authors.edit');

    Route::put('/authors/{id}/update', [authorsController::class, 'authorUpdate'])
        ->name('admin.authors.update');

    Route::get('/authors/{id}/destroy', [authorsController::class, 'authorDestroy'])
        ->name('admin.authors.destroy');

    Route::post('/authors/{id}/toggleStatus', [authorsController::class, 'authorToggleStatus'])
        ->name('admin.authors.toggleStatus');

    Route::get('/authors/{id}', [authorsController::class, 'authorShow'])
        ->name('admin.authors.show');
});

/*
|--------------------------------------------------------------------------
| 4. ROUTE PROFILE (HỒ SƠ CÁ NHÂN)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
/*
|--------------------------------------------------------------------------
| 5. ROUTE THANH TOÁN (NGƯỜI DÙNG ĐÃ ĐĂNG NHẬP)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    // 1. Trang Checkout (hiển thị form điền thông tin và chọn VNPAY/COD)
    Route::get('/checkout', [PaymentController::class, 'index'])->name('checkout.index');

    // 2. Xử lý khi bấm nút "Đặt Hàng"
    Route::post('/checkout/process', [PaymentController::class, 'process'])->name('checkout.process');

    // 3. Đường link để VNPAY trả kết quả về sau khi quẹt thẻ (Đã sửa lại link cho khớp .env)
    Route::get('/checkout/vnpay-return', [PaymentController::class, 'vnpayReturn'])->name('vnpay.return');
});
// Nạp các file xử lý đăng nhập, đăng xuất tự động từ gói Breeze
require __DIR__ . '/auth.php';
