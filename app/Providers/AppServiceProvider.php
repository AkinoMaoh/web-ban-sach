<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);
        Paginator::useBootstrapFive();
        View::composer('layouts.header', function ($view) {
            
            // 1. Lấy danh mục nổi bật
            $featuredCategories = Category::where('is_featured', 1)->take(4)->get();
            
            // 2. Lấy từ khóa hot
            $hotKeywords = Keyword::orderBy('search_count', 'desc')->take(4)->get();
            
            // 3. Lấy lịch sử tìm kiếm (Nếu user đã đăng nhập)
            $searchHistory = [];
            if (auth()->check()) {
                // Giả sử bạn có bảng SearchHistory
                // $searchHistory = SearchHistory::where('user_id', auth()->id())->latest()->take(5)->get();
            }

            // Truyền dữ liệu sang view header
            $view->with(compact('featuredCategories', 'hotKeywords', 'searchHistory'));
        });
    }
}
