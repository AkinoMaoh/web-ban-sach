<?php


use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('User/index');
})->name('user.index');

Route::prefix('admin')->group(function () {

    Route::get('/dashboard', function () {
        return view('admin/dashboard');
    })->name('admin.dashboard');

});
