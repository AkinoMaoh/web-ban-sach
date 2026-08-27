<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BannerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('banners')->insert([

            [
                'title' => 'Khám phá thế giới sách',
                'description' => 'Tìm kiếm những cuốn sách phù hợp với bạn và bắt đầu hành trình khám phá tri thức ngay hôm nay.',
                'image' => 'banner1.jpg',
                'link' => 'http://127.0.0.1:8000/shop',
                'position' => 'home',
                'sort_order' => 1,
                'status' => 1,
                'start_date' => now(),
                'end_date' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'title' => 'Tìm cuốn sách dành riêng cho bạn',
                'description' => 'Lựa chọn từ nhiều thể loại sách và tìm thấy những tác phẩm phù hợp với sở thích của mình.',
                'image' => 'banner2.jpg',
                'link' => 'http://127.0.0.1:8000/shop',
                'position' => 'home',
                'sort_order' => 2,
                'status' => 1,
                'start_date' => now(),
                'end_date' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'title' => 'Dragon Ball – Hành Trình Phiêu Lưu Bất Tận',
                'description' => 'Cùng Son Goku và những người bạn bước vào hành trình khám phá thế giới Dragon Ball',
                'image' => 'banner3.jpg',
                'link' => 'http://127.0.0.1:8000/product/75',
                'position' => 'category',
                'sort_order' => 1,
                'status' => 1,
                'start_date' => now(),
                'end_date' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'title' => 'Doraemon – Những Cuộc Phiêu Lưu Kỳ Diệu',
                'description' => 'Khám phá những câu chuyện tuổi thơ quen thuộc qua từng trang sách! 📚✨',
                'image' => 'banner4.jpg',
                'link' => 'http://127.0.0.1:8000/shop?keyword=doraemon',
                'position' => 'category',
                'sort_order' => 2,
                'status' => 1,
                'start_date' => now(),
                'end_date' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'title' => 'One Piece – Hành Trình Tìm Kiếm Kho Báu',
                'description' => 'Cùng Luffy và băng Hải Tặc Mũ Rơm bước vào hành trình phiêu lưu trên đại dương, khám phá những vùng đất mới',
                'image' => 'banner5.jpg',
                'link' => 'http://127.0.0.1:8000/shop?keyword=one+piece',
                'position' => 'category',
                'sort_order' => 3,
                'status' => 1,
                'start_date' => now(),
                'end_date' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

        

        ]);
    }
}
