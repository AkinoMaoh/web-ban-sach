<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NewsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('news')->insert([

            [
                'title' => 'Top 10 cuốn sách nên đọc năm 2026',
                'slug' => 'top-10-cuon-sach-nen-doc-nam-2026',
                'image' => 'news1.jpg',
                'summary' => 'Danh sách những cuốn sách bán chạy và được yêu thích nhất năm 2026.',
                'content' => 'Đây là nội dung chi tiết của bài viết Top 10 cuốn sách nên đọc năm 2026.',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'title' => '5 kỹ năng đọc sách hiệu quả',
                'slug' => '5-ky-nang-doc-sach-hieu-qua',
                'image' => 'news2.jpg',
                'summary' => 'Hướng dẫn cách đọc sách nhanh và ghi nhớ lâu.',
                'content' => 'Đây là nội dung chi tiết của bài viết về kỹ năng đọc sách.',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'title' => 'Khuyến mãi mùa hè giảm đến 50%',
                'slug' => 'khuyen-mai-mua-he-giam-den-50',
                'image' => 'news3.jpg',
                'summary' => 'Chương trình khuyến mãi dành cho tất cả khách hàng.',
                'content' => 'Đây là nội dung chi tiết của chương trình khuyến mãi mùa hè.',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'title' => 'Những tác giả nổi tiếng bạn nên biết',
                'slug' => 'nhung-tac-gia-noi-tieng-ban-nen-biet',
                'image' => 'news4.jpg',
                'summary' => 'Giới thiệu các tác giả nổi tiếng trong và ngoài nước.',
                'content' => 'Đây là nội dung chi tiết của bài viết về các tác giả nổi tiếng.',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'title' => 'Sách mới cập nhật tháng 6',
                'slug' => 'sach-moi-cap-nhat-thang-6',
                'image' => 'news5.jpg',
                'summary' => 'Danh sách các đầu sách mới được cập nhật trong tháng.',
                'content' => 'Đây là nội dung chi tiết của bài viết sách mới tháng 6.',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],

        ]);
    }
}