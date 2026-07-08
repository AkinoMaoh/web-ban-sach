<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\productsController;
use App\Http\Controllers\Admin\categoriesController;
use App\Http\Controllers\Admin\authorsController;
use App\Http\Controllers\Admin\ordersController;
use App\Http\Controllers\User\trangChuController;
use App\Http\Controllers\Admin\publisherController;
use App\Http\Controllers\User\PaymentController;
use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\User\shopDetailsController;
use App\Http\Controllers\User\ShopController;
use App\Http\Controllers\User\CartController;
use App\Http\Controllers\User\ContactController;
use App\Http\Controllers\User\OrderHistoryController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\PhoneLoginController;
use App\Http\Controllers\Auth\PasswordResetController; 
use App\Models\Notification;


/*
|--------------------------------------------------------------------------
| 1. ROUTE CÔNG KHAI (CHỈ DÀNH CHO KHÁCH HÀNG / USER THƯỜNG KHÔNG PHẢI ADMIN)
|--------------------------------------------------------------------------
*/
Route::middleware(['user_only'])->group(function () {
    Route::get('/', [trangChuController::class, 'index'])->name('user.index');
    Route::get('/shop', [ShopController::class, 'index'])->name('user.shop');
    Route::get('/shop/category/{id}', [ShopController::class, 'category'])->name('user.category');

    // Tìm kiếm 
    Route::get('/search', [trangChuController::class, 'search'])->name('user.search');
    Route::get('/search-product', [trangChuController::class, 'searchProduct'])->name('search.product');

    Route::get('/product/{id}', [shopDetailsController::class, 'index'])->name('user.productDetails');
    
    // Liên hệ
    Route::get('/contact', [ContactController::class, 'index'])->name('user.contact');
    Route::post('/contact', [ContactController::class, 'send'])->name('user.contact.send');
    
    // Giỏ hàng
    Route::prefix('cart')->group(function () {
        Route::get('/', [CartController::class, 'index'])->name('cart.index');
        Route::post('/', [CartController::class, 'updateCart'])->name('cart.update');
        Route::post('/add', [CartController::class, 'addToCart'])->name('cart.add');
        Route::delete('/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
    });

    // Thanh toán
    Route::get('/checkout', [PaymentController::class, 'index'])->name('checkout.index');
    Route::post('/checkout/process', [PaymentController::class, 'process'])->name('checkout.process');
    Route::get('/checkout/vnpay-return', [PaymentController::class, 'vnpayReturn'])->name('vnpay.return');
});


/*
|--------------------------------------------------------------------------
| 2. TUYẾN ĐƯỜNG TRUNG GIAN ĐIỀU HƯỚNG TỰ ĐỘNG
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', function (Request $request) {
    if ((int)Auth::user()->role !== 1) {
        return redirect('/');
    }

    if ((int)Auth::user()->is_active !== 1) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->withErrors([
            'email' => 'Tài khoản Admin của bạn đang chờ phê duyệt từ Quản trị viên cấp cao. Vui lòng quay lại sau!'
        ]);
    }

    return redirect()->route('admin.dashboard');
})->middleware(['auth'])->name('dashboard');


/*
|--------------------------------------------------------------------------
| 3. ROUTE VÀO CỔNG ĐĂNG KÝ / ĐĂNG NHẬP CỦA ADMIN (CHƯA ĐĂNG NHẬP)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->prefix('admin')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');

    Route::get('/register', [AdminAuthController::class, 'showRegisterForm'])->name('admin.register');
    Route::post('/register', [AdminAuthController::class, 'register'])->name('admin.register.submit');
});


/*
|--------------------------------------------------------------------------
| 4. KHU VỰC QUẢN TRỊ VIÊN (ADMIN PANEL) - ĐÃ QUA PHÊ DUYỆT BẢO MẬT
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

    Route::get('/profile', [AdminAuthController::class, 'editProfile'])->name('admin.profile.edit');
    Route::put('/profile', [AdminAuthController::class, 'updateProfile'])->name('admin.profile.update');

    Route::get('/manage-admins', [AdminAuthController::class, 'manageAdmins'])->name('admin.manage');
    Route::post('/manage-admins/{id}/approve', [AdminAuthController::class, 'approveAdmin'])->name('admin.approve');
    Route::delete('/manage-admins/{id}/reject', [AdminAuthController::class, 'rejectAdmin'])->name('admin.reject');

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

    // Quản lý danh mục
    Route::get('/categories', [categoriesController::class, 'index'])->name('admin.categories');
    Route::get('/categories/create', [categoriesController::class, 'create'])->name('admin.categoryAdd');
    Route::post('/categories/store', [categoriesController::class, 'store'])->name('admin.categories.store');
    Route::get('/categories/{id}/edit', [categoriesController::class, 'edit'])->name('admin.categories.edit');
    Route::put('/categories/{id}/update', [categoriesController::class, 'update'])->name('admin.categories.update');
    Route::post('/categories/{id}/toggleStatus', [categoriesController::class, 'toggleStatus'])->name('admin.categories.toggleStatus');
    Route::get('/categories/{id}/destroy', [categoriesController::class, 'destroy'])->name('admin.categories.destroy');
});


/*
|--------------------------------------------------------------------------
| 5. ROUTE PROFILE (CHỈ DÀNH RIÊNG CHO USER THƯỜNG)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'user_only'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/order-history', [OrderHistoryController::class, 'index'])->name('user.orderHistory');
    Route::get('/order-history/{id}', [OrderHistoryController::class, 'show'])->name('user.orderHistory.show');
    Route::post('/history/cancel/{id}', [OrderHistoryController::class, 'cancel'])->name('user.history.cancel');

    Route::get('/wishlist', [App\Http\Controllers\User\WishlistController::class, 'index'])->name('user.wishlist');
    Route::post('/wishlist/toggle', [App\Http\Controllers\User\WishlistController::class, 'toggle'])->name('user.wishlist.toggle');
    Route::get('/wishlist/remove/{id}', [App\Http\Controllers\User\WishlistController::class, 'remove'])->name('user.wishlist.remove');

    // --- CẤU HÌNH MỚI: QUY TRÌNH ĐỔI MẬT KHẨU QUA MÃ OTP EMAIL ---
    // Giao diện bước 1: Yêu cầu bấm gửi và nhập mã OTP
    Route::get('/password/verify', [PasswordResetController::class, 'showVerifyForm'])->name('password.verify.form');
    // API xử lý gửi mã xác thực ngẫu nhiên vào hòm thư
    Route::post('/password/send-otp', [PasswordResetController::class, 'sendOtp'])->name('password.verify.send');
    // API kiểm tra mã OTP khớp hay sai để mở khóa bước kế tiếp
    Route::post('/password/verify-otp', [PasswordResetController::class, 'verifyOtp'])->name('password.verify.match');
    
    // Giao diện bước 2: Trang điền hai trường mật khẩu mới (Chỉ cho xem khi OTP đúng)
    Route::get('/password/reset-fields', [PasswordResetController::class, 'showResetFieldsForm'])->name('password.reset.fields');
    // Xử lý cập nhật chính thức mật khẩu đã băm vào Database
    Route::post('/password/update-new', [PasswordResetController::class, 'updatePassword'])->name('password.reset.update');
});


/*
|--------------------------------------------------------------------------
| 6. ROUTE LOGIN SOCIAL & OTP (Đã được chuẩn hóa phương thức POST)
|--------------------------------------------------------------------------
*/
// Đăng nhập Google
Route::post('auth/google', [GoogleController::class, 'redirectToGoogle'])->name('google.login');
Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);

// Đăng nhập Số điện thoại OTP qua Log file
Route::post('phone/send-otp', [PhoneLoginController::class, 'sendOtp'])->name('phone.sendOtp');
Route::post('login-phone-verify', [PhoneLoginController::class, 'verifyLogin'])->name('login.phone.verify');

// Chuyển hướng khi click vào thông báo
Route::get('/notifications/redirect/{id}', function ($id) {
    $n = \App\Models\Notification::findOrFail($id);
    $n->update(['is_read' => true]); // Đánh dấu đã đọc
    
    // Chuyển sang trang chi tiết đơn hàng
    return $n->order_id ? redirect('/order-history/' . $n->order_id) : back();
})->name('notifications.redirect')->middleware('auth');

// Xóa thông báo
Route::post('/notifications/delete/{id}', function ($id) {
    \App\Models\Notification::where('id', $id)->where('user_id', Auth::id())->delete();
    return back();
})->name('notifications.delete')->middleware('auth');

// Đánh dấu đọc tất cả
Route::get('/notifications/read-all', function () {
    \App\Models\Notification::where('user_id', Auth::id())->update(['is_read' => true]);
    return back();
})->name('notifications.read.all')->middleware('auth');
// Các tuyến đường auth đăng nhập/đăng ký mặc định của hệ thống Người dùng thường
require __DIR__ . '/auth.php';