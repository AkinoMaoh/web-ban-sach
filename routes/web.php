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
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| 1. ROUTE CÔNG KHAI (CHỈ DÀNH CHO KHÁCH HÀNG / USER THƯỜNG KHÔNG PHẢI ADMIN)
|--------------------------------------------------------------------------
| Sử dụng thêm middleware 'user_only' để chặn đứng tài khoản Admin vào xem/mua hàng.
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
    // 1. Nếu không phải Admin -> Cho ra trang chủ người dùng thường
    if ((int)Auth::user()->role !== 1) {
        return redirect('/');
    }

    // 2. Nếu là Admin nhưng CHƯA ĐƯỢC DUYỆT (is_active = 0) -> Đăng xuất ngay lập tức
    if ((int)Auth::user()->is_active !== 1) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->withErrors([
            'email' => 'Tài khoản Admin của bạn đang chờ phê duyệt từ Quản trị viên cấp cao. Vui lòng quay lại sau!'
        ]);
    }

    // 3. Nếu là Admin đã được phê duyệt hợp lệ -> Cho phép truy cập vào Dashboard quản trị
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
| Middleware 'admin' sẽ kích hoạt file AdminMiddleware kiểm tra quyền nghiêm ngặt của bạn.
*/
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {

    // Trang chủ quản trị
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    // Đăng xuất Admin
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

    // Hồ sơ Admin
    Route::get('/profile', [AdminAuthController::class, 'editProfile'])->name('admin.profile.edit');
    Route::put('/profile', [AdminAuthController::class, 'updateProfile'])->name('admin.profile.update');

    // --- KHU VỰC QUẢN LÝ VÀ PHÊ DUYỆT ADMIN ---
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
| Thêm middleware 'user_only' để chặn hoàn toàn tài khoản Admin vào chỉnh sửa profile tại đây.
| Admin muốn sửa hồ sơ đã có route riêng: 'admin.profile.edit' ở bên trên mục số 4.
*/
Route::middleware(['auth', 'user_only'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


// Các tuyến đường auth đăng nhập/đăng ký mặc định của hệ thống Người dùng thường
require __DIR__ . '/auth.php';