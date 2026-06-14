<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\ProductController; 
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| 1. ROUTE CÔNG KHAI (CHƯA ĐĂNG NHẬP CŨNG XEM ĐƯỢC)
|--------------------------------------------------------------------------
*/
// Trang chủ hiển thị danh sách sách cho mọi đối tượng
Route::get('/', function () {
    return view('User/index');
})->name('user.index');


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
    if (Auth::user()->email === 'admin@gmail.com') { 
        return redirect()->route('admin.dashboard');
    }

    // Nếu là người dùng mua sách bình thường, trả họ về trang chủ User
    return redirect()->route('user.index');
})->middleware(['auth'])->name('dashboard');


/*
|--------------------------------------------------------------------------
| 3. ROUTE DÀNH CHO QUẢN TRỊ VIÊN (ADMIN) - BẢO MẬT
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->middleware('auth')->group(function () {
    
    // Trang tổng quan Admin
    Route::get('/dashboard', function () {
        return view('admin/dashboard');
    })->name('admin.dashboard');

    // Trang quản lý danh sách sách
    Route::get('/products', [ProductController::class, 'index'])->name('admin.products.index');

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

// Nạp các file xử lý đăng nhập, đăng xuất tự động từ gói Breeze
require __DIR__.'/auth.php';