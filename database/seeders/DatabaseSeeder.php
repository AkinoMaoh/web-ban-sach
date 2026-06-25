<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon; // Thêm thư viện Carbon để lấy thời gian thực (nếu cần)

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Roles
        DB::table('roles')->insert([
            ['id' => 1, 'name' => 'Admin'],
            ['id' => 2, 'name' => 'User'],
        ]);

        // 2. Seed Users
        DB::table('users')->insert([
            ['id' => 1, 'name' => 'Dương Quốc Anh PP 0 3 4 6 2', 'email' => 'ankinoto20@gmail.com', 'password' => '$2y$12$krrh6nvN4ZrGmrGnBz371.e.7KQDu3xRAl8yGbHWEltVnzNjOmUim', 'status' => 1, 'role' => 1, 'created_at' => '2026-06-14 03:26:16', 'updated_at' => '2026-06-15 11:17:19'],
            ['id' => 2, 'name' => 'akino', 'email' => 'cauvang559@gmail.com', 'password' => '$2y$12$IRxxF6VWA1Tr6uin2sWQPu4y8GDFf1R6gayQqEKDMxpgwN3KRNgXe', 'status' => 1, 'role' => 0, 'created_at' => '2026-06-14 05:10:24', 'updated_at' => '2026-06-15 04:23:07'],
            ['id' => 3, 'name' => 'Quốc anh', 'email' => 'lionmika05@gmail.com', 'password' => '$2y$12$jRcmS2Q1/6GIzXwAgunOSOf0sYSWLivBYJ6gTSnAQ2zuEEgkutwHC', 'status' => 1, 'role' => 0, 'created_at' => '2026-06-15 03:03:01', 'updated_at' => '2026-06-15 10:07:57'],
            ['id' => 4, 'name' => 'mike', 'email' => 'abc123@gmail.com', 'password' => '$2y$12$5wyA.9kYus8yTp2AKUrqSeNgA12qexBdk3PyE.2R1tXcjHM4yUvSG', 'status' => 1, 'role' => 0, 'created_at' => '2026-06-15 04:18:28', 'updated_at' => '2026-06-15 11:19:05'],
            ['id' => 5, 'name' => 'qưa', 'email' => 'aaa111@gmail.com', 'password' => '$2y$12$/LAcLjb4WSGtYzAJaruhQuVBOU1mR8BgfhNTFS92ZGbOUOZftwjCy', 'status' => 1, 'role' => 0, 'created_at' => '2026-06-15 04:21:10', 'updated_at' => '2026-06-15 04:21:10'],
        ]);

        // 3. Seed Categories
        DB::table('categories')->insert([
            ['id' => 1, 'name' => 'Kinh dị', 'image' => 'kinh_di.jpg', 'status' => 1],
            ['id' => 2, 'name' => 'Tiểu thuyết', 'image' => 'tieu_thuyet.jpg', 'status' => 1],
            ['id' => 3, 'name' => 'Trinh thám', 'image' => 'trinh_tham.jpg', 'status' => 1],
            ['id' => 4, 'name' => 'Truyện ngụ ngôn', 'image' => 'ngu_ngon.jpg', 'status' => 1],
            ['id' => 5, 'name' => 'Manga', 'image' => 'manga.jpg', 'status' => 1],
            ['id' => 6, 'name' => 'Cổ tích', 'image' => 'co_tich.jpg', 'status' => 1],
            ['id' => 7, 'name' => 'Văn học nước ngoài', 'image' => 'van_hoc_nuoc_ngoai.jpg', 'status' => 1],
            ['id' => 8, 'name' => 'Triết lý sống', 'image' => 'triet_ly_song.jpg', 'status' => 1],
        ]);

        // 4. Seed Authors
        DB::table('authors')->insert([
            ['id' => 1, 'name' => 'Stephen King', 'bio' => 'Ông hoàng kinh dị người Mỹ, tác giả của The Shining, It, Misery...', 'avatar' => 'stephen_king.jpg', 'status' => 1],
            ['id' => 2, 'name' => 'Koji Suzuki', 'bio' => 'Nhà văn kinh dị nổi tiếng Nhật Bản, cha đẻ của sê-ri Vòng tròn ác nghiệt (Ringu).', 'avatar' => 'koji_suzuki.jpg', 'status' => 1],
            ['id' => 3, 'name' => 'Haruki Murakami', 'bio' => 'Một trong những nhà văn hiện đại nổi tiếng nhất Nhật Bản với phong cách hiện thực huyền ảo.', 'avatar' => 'haruki_murakami.jpg', 'status' => 1],
            ['id' => 4, 'name' => 'Paulo Coelho', 'bio' => 'Tiểu thuyết gia người Brazil, tác giả của cuốn sách bán chạy quốc tế Nhà Giả Kim.', 'avatar' => 'paulo_coelho.jpg', 'status' => 1],
            ['id' => 5, 'name' => 'Agatha Christie', 'bio' => 'Nữ hoàng trinh thám người Anh với các nhân vật kinh điển như Hercule Poirot và Miss Marple.', 'avatar' => 'agatha_christie.jpg', 'status' => 1],
            ['id' => 6, 'name' => 'Dan Brown', 'bio' => 'Tác giả người Mỹ nổi tiếng với các tiểu thuyết trinh thám lịch sử và tôn giáo như Mật mã Da Vinci.', 'avatar' => 'dan_brown.jpg', 'status' => 1],
            ['id' => 7, 'name' => 'Higashino Keigo', 'bio' => 'Đại tá văn học trinh thám xứ sở hoa anh đào, tác giả Phía Sau Nghi Can X, Bạch Dạ Hành.', 'avatar' => 'higashino_keigo.jpg', 'status' => 1],
            ['id' => 8, 'name' => 'Eiichiro Oda', 'bio' => 'Họa sĩ manga người Nhật Bản, tác giả của bộ truyện tranh huyền thoại bán chạy nhất One Piece.', 'avatar' => 'eiichiro_oda.jpg', 'status' => 1],
            ['id' => 9, 'name' => 'Gosho Aoyama', 'bio' => 'Cha đẻ của bộ truyện tranh trinh thám đình đám Thám tử lừng danh Conan.', 'avatar' => 'gosho_aoyama.jpg', 'status' => 1],
            ['id' => 10, 'name' => 'Dale Carnegie', 'bio' => 'Nhà văn và nhà thuyết trình người Mỹ, tác giả cuốn sách nghệ thuật ứng xử Đắc Nhân Tâm.', 'avatar' => 'dale_carnegie.jpg', 'status' => 1],
            ['id' => 11, 'name' => 'iphone 16', 'bio' => 'sdrttyuio2', 'avatar' => '1781515638_anh1.jpg', 'status' => 0],
        ]);

        // 5. Seed Publishers
        DB::table('publishers')->insert([
            ['id' => 1, 'name' => 'NXB Trẻ', 'address' => '161B Lý Chính Thắng, Phường Võ Thị Sáu, Quận 3, TP. Hồ Chí Minh', 'website' => 'https://www.nxbtre.com.vn'],
            ['id' => 2, 'name' => 'NXB Kim Đồng', 'address' => '55 Quang Trung, Nguyễn Du, Hai Bà Trưng, Hà Nội', 'website' => 'https://www.nxbkimdong.com.vn'],
            ['id' => 3, 'name' => 'NXB Nhã Nam', 'address' => '59 Đỗ Quang, Trung Hòa, Cầu Giấy, Hà Nội', 'website' => 'https://nhanam.vn'],
            ['id' => 4, 'name' => 'NXB Phụ Nữ Việt Nam', 'address' => '39 Hàng Chuối, Phạm Đình Hổ, Hai Bà Trưng, Hà Nội', 'website' => 'https://nxbphunu.com.vn'],
            ['id' => 5, 'name' => 'NXB Tổng Hợp TP.HCM', 'address' => '62 Nguyễn Thị Minh Khai, Đa Kao, Quận 1, TP. Hồ Chí Minh', 'website' => 'https://nxbhcm.com.vn'],
            ['id' => 6, 'name' => 'NXB Hội Nhà Văn', 'address' => '65 Nguyễn Du, Hai Bà Trưng, Hà Nội', 'website' => 'http://nxbhoinhavan.vn'],
            ['id' => 7, 'name' => 'NXB Văn Học', 'address' => '18 Nguyễn Trường Tộ, Trúc Bạch, Ba Đình, Hà Nội', 'website' => 'http://nxbvanhoc.com.vn'],
            ['id' => 8, 'name' => 'NXB Lao Động', 'address' => '175 Tây Sơn, Trung Liệt, Đống Đa, Hà Nội', 'website' => 'http://nxblaodong.com.vn'],
        ]);

        // 6. Seed Products
        DB::table('products')->insert([
            // Thể loại: Kinh dị
            ['id' => 1, 'category_id' => 1, 'author_id' => 1, 'publisher_id' => 1, 'name' => 'Shining (Chói Sòa)', 'description' => 'Cuốn tiểu thuyết kinh dị kinh điển của Stephen King về khách sạn Overlook bị ám.', 'price' => 155000.00, 'image' => 'shining.jpg', 'status' => 1],
            ['id' => 2, 'category_id' => 1, 'author_id' => 1, 'publisher_id' => 2, 'name' => 'It (Chú Hề Ma Quái) - Tập 1', 'description' => 'Hành trình đối đầu với thế lực tà ác dưới hình dạng chú hề Pennywise tại thị trấn Derry.', 'price' => 180000.00, 'image' => 'it_1.jpg', 'status' => 1],
            ['id' => 3, 'category_id' => 1, 'author_id' => 1, 'publisher_id' => 2, 'name' => 'It (Chú Hề Ma Quái) - Tập 2', 'description' => 'Phần kết về cuộc chiến của nhóm Losers Club khi đã trưởng thành.', 'price' => 180000.00, 'image' => 'it_2.jpg', 'status' => 1],
            ['id' => 4, 'category_id' => 1, 'author_id' => 2, 'publisher_id' => 3, 'name' => 'Ringu (Vòng Tròn Ác Nghiệt)', 'description' => 'Cuốn tiểu thuyết kinh dị Nhật Bản về cuốn băng video mang lời nguyền chết chóc.', 'price' => 95000.00, 'image' => 'ringu.jpg', 'status' => 1],
            ['id' => 5, 'category_id' => 1, 'author_id' => 2, 'publisher_id' => 3, 'name' => 'Rasen (Vòng Xoáy Gien)', 'description' => 'Phần tiếp theo đầy hack não của Ringu, kết hợp yếu tố khoa học giả tưởng.', 'price' => 110000.00, 'image' => 'rasen.jpg', 'status' => 1],
            ['id' => 6, 'category_id' => 1, 'author_id' => 2, 'publisher_id' => 3, 'name' => 'Loop (Vòng Lặp Vô Hạn)', 'description' => 'Phần thứ 3 trong series Ringu của Koji Suzuki, mở ra một thế giới hoàn toàn khác.', 'price' => 115000.00, 'image' => 'loop.jpg', 'status' => 1],
            ['id' => 7, 'category_id' => 1, 'author_id' => 3, 'publisher_id' => 4, 'name' => 'Tiếng Gọi Cthulhu', 'description' => 'Tuyển tập truyện ngắn kinh dị vũ trụ cổ điển của nhà văn H.P. Lovecraft.', 'price' => 135000.00, 'image' => 'cthulhu.jpg', 'status' => 1],
            ['id' => 8, 'category_id' => 1, 'author_id' => 4, 'publisher_id' => 1, 'name' => 'Drakula (Bá Tước Ma Cà Rồng)', 'description' => 'Tác phẩm kinh điển định hình nên hình tượng ma cà rồng thế kỷ hiện đại.', 'price' => 145000.00, 'image' => 'dracula.jpg', 'status' => 1],
            ['id' => 9, 'category_id' => 1, 'author_id' => 5, 'publisher_id' => 5, 'name' => 'Phía Sau Nghi Can X', 'description' => 'Tác phẩm trinh thám kinh dị tâm lý xuất sắc nhất của Higashino Keigo.', 'price' => 120000.00, 'image' => 'nghi_can_x.jpg', 'status' => 1],
            ['id' => 10, 'category_id' => 1, 'author_id' => 5, 'publisher_id' => 5, 'name' => 'Thánh Giá Rỗng', 'description' => 'Góc nhìn trực diện và ám ảnh của Keigo về án tử hình và sự sám hối.', 'price' => 125000.00, 'image' => 'thanh_gia_rong.jpg', 'status' => 1],
            ['id' => 11, 'category_id' => 1, 'author_id' => 1, 'publisher_id' => 6, 'name' => 'Misery (Khổ Đau)', 'description' => 'Câu chuyện giam cầm kinh hoàng giữa một nhà văn và người hâm mộ cuồng nhiệt.', 'price' => 140000.00, 'image' => 'misery.jpg', 'status' => 1],
            ['id' => 12, 'category_id' => 1, 'author_id' => 1, 'publisher_id' => 6, 'name' => 'Pet Sematary (Nghĩa Địa Thú Cưng)', 'description' => 'Nỗi đau mất con đẩy một người cha vào việc hồi sinh người chết đầy ma mị.', 'price' => 160000.00, 'image' => 'pet_sematary.jpg', 'status' => 1],
            ['id' => 13, 'category_id' => 1, 'author_id' => 6, 'publisher_id' => 7, 'name' => 'Kỳ Án Ánh Trăng', 'description' => 'Cuốn tiểu thuyết kinh dị tâm linh, trinh thám Trung Quốc nổi tiếng của Quỷ Cổ Nữ.', 'price' => 130000.00, 'image' => 'ky_an_anh_trang.jpg', 'status' => 1],
            ['id' => 14, 'category_id' => 1, 'author_id' => 7, 'publisher_id' => 7, 'name' => 'Đề Thi Đẫm Máu', 'description' => 'Cuốn sách trinh thám hình sự kinh dị về tâm lý tội phạm của Lôi Mễ.', 'price' => 145000.00, 'image' => 'de_thi_dam_mau.jpg', 'status' => 1],
            ['id' => 15, 'category_id' => 1, 'author_id' => 7, 'publisher_id' => 7, 'name' => 'Cuồng Vọng Phi Nhân Tính', 'description' => 'Hành trình phá án nghẹt thở chống lại cái ác biến thái của cảnh sát Phương Mộc.', 'price' => 150000.00, 'image' => 'cuong_vong.jpg', 'status' => 1],
            ['id' => 16, 'category_id' => 1, 'author_id' => 8, 'publisher_id' => 2, 'name' => 'Frankenstein', 'description' => 'Tác phẩm kinh dị Gothic kinh điển của Mary Shelley về quái vật nhân tạo.', 'price' => 90000.00, 'image' => 'frankenstein.jpg', 'status' => 1],
            ['id' => 17, 'category_id' => 1, 'author_id' => 9, 'publisher_id' => 8, 'name' => 'Another', 'description' => 'Câu chuyện kinh dị học đường Nhật Bản xoay quanh lời nguyền của lớp 3-3.', 'price' => 135000.00, 'image' => 'another.jpg', 'status' => 1],
            ['id' => 18, 'category_id' => 1, 'author_id' => 10, 'publisher_id' => 5, 'name' => 'Búp Bê Gọi Hồn', 'description' => 'Câu chuyện rùng rợn về những con búp bê mang linh hồn thù hận.', 'price' => 98000.00, 'image' => 'bup_be_goi_hon.jpg', 'status' => 1],
            ['id' => 19, 'category_id' => 1, 'author_id' => 5, 'publisher_id' => 5, 'name' => 'Ngôi Nhà Của Người Cá Say Ngủ', 'description' => 'Bi kịch gia đình và những ranh giới mong manh giữa sự sống và cái chết.', 'price' => 138000.00, 'image' => 'nguoi_ca_say_ngu.jpg', 'status' => 1],
            ['id' => 20, 'category_id' => 1, 'author_id' => 1, 'publisher_id' => 1, 'name' => 'Carrie (Vũ Hội Đẫm Máu)', 'description' => 'Tác phẩm đầu tay của Stephen King về cô gái có siêu năng lực cuồng nộ.', 'price' => 110000.00, 'image' => 'carrie.jpg', 'status' => 1],

            // Thể loại: Tiểu thuyết
            ['id' => 21, 'category_id' => 2, 'author_id' => 2, 'publisher_id' => 2, 'name' => 'Nhà Giả Kim', 'description' => 'Cuốn tiểu thuyết biểu tượng về hành trình theo đuổi vận mệnh của Paulo Coelho.', 'price' => 79000.00,  'image' => 'nha_gia_kim.jpg', 'status' => 1],
            ['id' => 22, 'category_id' => 2, 'author_id' => 3, 'publisher_id' => 5, 'name' => 'Rừng Na Uy', 'description' => 'Tác phẩm đưa tên tuổi Murakami Haruki vươn tầm thế giới, đầy hoài niệm và cô đơn.', 'price' => 140000.00, 'image' => 'rung_na_uy.jpg', 'status' => 1],
            ['id' => 23, 'category_id' => 2, 'author_id' => 3, 'publisher_id' => 5, 'name' => 'Kafka Bên Bờ Biển', 'description' => 'Sự đan xen kỳ ảo giữa thực và mơ của hai số phận con người.', 'price' => 175000.00, 'image' => 'kafka_ben_bo_bien.jpg', 'status' => 1],
            ['id' => 24, 'category_id' => 2, 'author_id' => 4, 'publisher_id' => 1, 'name' => 'Giết Con Chim Nhại', 'description' => 'Kiệt tác văn học Mỹ về tình yêu thương, công lý và nạn phân biệt chủng tộc.', 'price' => 110000.00,  'image' => 'giet_con_chim_nhai.jpg', 'status' => 1],
            ['id' => 25, 'category_id' => 2, 'author_id' => 5, 'publisher_id' => 2, 'name' => 'Bố Già (The Godfather)', 'description' => 'Cuốn tiểu thuyết xuất sắc nhất về thế giới ngầm Mafia của Mario Puzo.', 'price' => 155000.00, 'image' => 'bo_gia.jpg', 'status' => 1],
            ['id' => 26, 'category_id' => 2, 'author_id' => 6, 'publisher_id' => 4, 'name' => 'Chiến Tranh và Hòa Bình (Trọn bộ)', 'description' => 'Đại sử thi văn học Nga phác họa xã hội Nga thời kỳ Napoléon.', 'price' => 450000.00, 'image' => 'chien_tranh_hoa_binh.jpg', 'status' => 1],
            ['id' => 27, 'category_id' => 2, 'author_id' => 7, 'publisher_id' => 6, 'name' => 'Suối Nguồn', 'description' => 'Tác phẩm triết học lồng ghép tiểu thuyết kinh điển của Ayn Rand.', 'price' => 290000.00, 'image' => 'suoi_nguon.jpg', 'status' => 1],
            ['id' => 28, 'category_id' => 2, 'author_id' => 8, 'publisher_id' => 2, 'name' => 'Không Gia Đình', 'description' => 'Hành trình vượt qua nghịch cảnh đầy cảm động của cậu bé Remi.', 'price' => 130000.00,  'image' => 'khong_gia_dinh.jpg', 'status' => 1],
            ['id' => 29, 'category_id' => 2, 'author_id' => 9, 'publisher_id' => 3, 'name' => 'Đồi Gió Hú', 'description' => 'Câu chuyện tình yêu đầy hận thù và đam mê mãnh liệt tại vùng đồng thảo.', 'price' => 105000.00, 'image' => 'doi_gio_hu.jpg', 'status' => 1],
            ['id' => 30, 'category_id' => 2, 'author_id' => 10, 'publisher_id' => 1, 'name' => 'Kiêu Hãnh Và Định Kiến', 'description' => 'Câu chuyện lãng mạn châm biếm sâu sắc về tầng lớp quý tộc Anh thế kỷ 19.', 'price' => 99000.00, 'image' => 'kieu_hanh_dinh_kien.jpg', 'status' => 1],
            ['id' => 31, 'category_id' => 2, 'author_id' => 1, 'publisher_id' => 5, 'name' => 'Tiếng Chim Hót Trong Bụi Mận Gai', 'description' => 'Mối tình đầy trái ngang giữa Meggie và cha xứ Ralph.', 'price' => 195000.00, 'image' => 'tieng_chim_hot.jpg', 'status' => 1],
            ['id' => 32, 'category_id' => 2, 'author_id' => 2, 'publisher_id' => 2, 'name' => 'Trăm Năm Cô Đơn', 'description' => 'Kiệt tác của chủ nghĩa hiện thực huyền ảo từ Gabriel García Márquez.', 'price' => 165000.00, 'image' => 'tram_nam_co_don.jpg', 'status' => 1],
            ['id' => 33, 'category_id' => 2, 'author_id' => 3, 'publisher_id' => 7, 'name' => 'Số Đỏ', 'description' => 'Tác phẩm văn học hiện thực trào phúng đỉnh cao của Vũ Trọng Phụng.', 'price' => 65000.00,  'image' => 'so_do.jpg', 'status' => 1],
            ['id' => 34, 'category_id' => 2, 'author_id' => 4, 'publisher_id' => 7, 'name' => 'Tắt Đèn', 'description' => 'Bức tranh khốn khổ của người nông dân Việt Nam dưới ách đô hộ.', 'price' => 50000.00,  'image' => 'tat_den.jpg', 'status' => 1],
            ['id' => 35, 'category_id' => 2, 'author_id' => 5, 'publisher_id' => 2, 'name' => 'Bắt Trẻ Đồng Xanh', 'description' => 'Cuốn tiểu thuyết biểu tượng cho sự nổi loạn và cô đơn của tuổi trẻ.', 'price' => 85000.00, 'image' => 'bat_tre_dong_xanh.jpg', 'status' => 1],

            // Thể loại: Trinh thám
            ['id' => 36, 'category_id' => 3, 'author_id' => 1, 'publisher_id' => 1, 'name' => 'Sherlock Holmes Toàn Tập - Tập 1', 'description' => 'Những vụ án đầu tiên của vị thám tử tài ba nhất mọi thời đại.', 'price' => 160000.00,  'image' => 'sherlock_1.jpg', 'status' => 1],
            ['id' => 37, 'category_id' => 3, 'author_id' => 1, 'publisher_id' => 1, 'name' => 'Sherlock Holmes Toàn Tập - Tập 2', 'description' => 'Các vụ án ly kỳ thử thách trí tuệ của Sherlock Holmes và Watson.', 'price' => 160000.00, 'image' => 'sherlock_2.jpg', 'status' => 1],
            ['id' => 38, 'category_id' => 3, 'author_id' => 2, 'publisher_id' => 2, 'name' => 'Án Mạng Trên Chuyến Tàu Tốc Hành Phương Đông', 'description' => 'Vụ án hóc búa nhất của thám tử lừng danh Hercule Poirot.', 'price' => 95000.00,  'image' => 'an_mang_tau_toc_hanh.jpg', 'status' => 1],
            ['id' => 39, 'category_id' => 3, 'author_id' => 2, 'publisher_id' => 2, 'name' => 'Và Rồi Ai Cũng Chết (And Then There Were None)', 'description' => 'Kiệt tác trinh thám bán chạy nhất mọi thời đại của Agatha Christie.', 'price' => 110000.00,  'image' => 'va_roi_ai_cung_chet.jpg', 'status' => 1],
            ['id' => 40, 'category_id' => 3, 'author_id' => 3, 'publisher_id' => 3, 'name' => 'Mật Mã Da Vinci', 'description' => 'Cuốn sách trinh thám tôn giáo, lịch sử nghẹt thở của Dan Brown.', 'price' => 185000.00, 'image' => 'mat_ma_da_vinci.jpg', 'status' => 1],
            ['id' => 41, 'category_id' => 3, 'author_id' => 3, 'publisher_id' => 3, 'name' => 'Thiên Thần Và Ác Quỷ', 'description' => 'Cuộc đua chống lại hội kín Illuminati tại thánh địa Vatican.', 'price' => 175000.00, 'image' => 'thien_than_ac_quy.jpg', 'status' => 1],
            ['id' => 42, 'category_id' => 3, 'author_id' => 4, 'publisher_id' => 5, 'name' => 'Sự Im Lặng Của Bầy Cừu', 'description' => 'Cuộc đấu trí đỉnh cao giữa Clarice Starling và bác sĩ ăn thịt người Hannibal Lecter.', 'price' => 115000.00, 'image' => 'su_im_lang_cua_bay_cuu.jpg', 'status' => 1],
            ['id' => 43, 'category_id' => 3, 'author_id' => 4, 'publisher_id' => 5, 'name' => 'Rồng Đỏ', 'description' => 'Tác phẩm đầu tiên giới thiệu nhân vật kinh điển Hannibal Lecter.', 'price' => 125000.00, 'image' => 'rong_do.jpg', 'status' => 1],
            ['id' => 44, 'category_id' => 3, 'author_id' => 5, 'publisher_id' => 6, 'name' => 'Cô Gái Có Hình Xăm Rồng', 'description' => 'Tác phẩm mở đầu series Millennium trinh thám Bắc Âu cực kỳ ăn khách.', 'price' => 165000.00, 'image' => 'co_gai_co_hinh_xam_rong.jpg', 'status' => 1],
            ['id' => 45, 'category_id' => 3, 'author_id' => 6, 'publisher_id' => 5, 'name' => 'Tội Ác Và Hình Phạt', 'description' => 'Tiểu thuyết trinh thám tâm lý chiều sâu kinh điển của Dostoevsky.', 'price' => 180000.00, 'image' => 'toi_ac_va_hinh_phat.jpg', 'status' => 1],
            ['id' => 46, 'category_id' => 3, 'author_id' => 7, 'publisher_id' => 7, 'name' => 'Bạch Dạ Hành', 'description' => 'Câu chuyện trinh thám đầy bi kịch và tăm tối kéo dài 20 năm của Keigo.', 'price' => 170000.00, 'image' => 'bach_da_hanh.jpg', 'status' => 1],
            ['id' => 47, 'category_id' => 3, 'author_id' => 7, 'publisher_id' => 7, 'name' => 'Tên Tội Phạm May Mắn', 'description' => 'Hành trình trốn chạy và những nút thắt bất ngờ phút cuối.', 'price' => 110000.00, 'image' => 'toi_pham_may_man.jpg', 'status' => 1],
            ['id' => 48, 'category_id' => 3, 'author_id' => 8, 'publisher_id' => 2, 'name' => 'Án Mạng Trên Sông Nile', 'description' => 'Thám tử Poirot điều tra vụ giết người vì tình trên con tàu du hành.', 'price' => 98000.00, 'image' => 'an_mang_song_nile.jpg', 'status' => 1],
            ['id' => 49, 'category_id' => 3, 'author_id' => 3, 'publisher_id' => 3, 'name' => 'Biểu Tượng Thất Truyền', 'description' => 'Giáo sư Robert Langdon giải mã các bí ẩn của hội Tam Điểm.', 'price' => 190000.00, 'image' => 'bieu_tuong_that_truyen.jpg', 'status' => 1],
            ['id' => 50, 'category_id' => 3, 'author_id' => 9, 'publisher_id' => 8, 'name' => 'Hỏa Ngục (Inferno)', 'description' => 'Hành trình giải mã bức họa của Dante để ngăn chặn thảm họa diệt chủng.', 'price' => 199000.00, 'image' => 'hoa_nguc.jpg', 'status' => 1],

            // Thể loại: Truyện ngụ ngôn
            ['id' => 51, 'category_id' => 4, 'author_id' => 1, 'publisher_id' => 1, 'name' => 'Truyện Ngụ Ngôn Aesop', 'description' => 'Tuyển tập những câu chuyện ngụ ngôn nổi tiếng nhất thế giới rút ra bài học sâu sắc.', 'price' => 85000.00,  'image' => 'ngu_ngon_aesop.jpg', 'status' => 1],
            ['id' => 52, 'category_id' => 4, 'author_id' => 2, 'publisher_id' => 1, 'name' => 'Truyện Ngụ Ngôn La Fontaine', 'description' => 'Những bài thơ ngụ ngôn đầy triết lý sống của đại văn hào Pháp.', 'price' => 95000.00,  'image' => 'la_fontaine.jpg', 'status' => 1],
            ['id' => 53, 'category_id' => 4, 'author_id' => 3, 'publisher_id' => 2, 'name' => 'Chuyện Con Mèo Dạy Hải Âu Bay', 'description' => 'Câu chuyện ngụ ngôn hiện đại ca ngợi tình yêu thương và giữ trọn lời hứa.', 'price' => 49000.00,  'image' => 'con_meo_day_hai_au_bay.jpg', 'status' => 1],
            ['id' => 54, 'category_id' => 4, 'author_id' => 3, 'publisher_id' => 2, 'name' => 'Chuyện Con Ốc Sên Muốn Biết Tại Sao Nó Chậm Chạp', 'description' => 'Hành trình đi tìm bản ngã đầy ý nghĩa của chú ốc sên nhỏ.', 'price' => 45000.00,  'image' => 'oc_sen_cham_chap.jpg', 'status' => 1],
            ['id' => 56, 'category_id' => 4, 'author_id' => 4, 'publisher_id' => 3, 'name' => 'Trại Súc Vật', 'description' => 'Tác phẩm ngụ ngôn chính trị châm biếm sâu cay của George Orwell.', 'price' => 65000.00,  'image' => 'trai_suc_vat.jpg', 'status' => 1],
            ['id' => 57, 'category_id' => 4, 'author_id' => 5, 'publisher_id' => 4, 'name' => 'Ngón Tay Mình Còn Thơm Mùi Sả', 'description' => 'Những câu chuyện ngụ ngôn mộc mạc mang hơi thở đồng quê Việt Nam.', 'price' => 70000.00, 'image' => 'ngon_tay_mui_sa.jpg', 'status' => 1],
            ['id' => 58, 'category_id' => 4, 'author_id' => 6, 'publisher_id' => 1, 'name' => 'Thỏ Và Rùa', 'description' => 'Truyện ngụ ngôn kinh điển về tính kiên trì chống lại sự kiêu ngạo.', 'price' => 30000.00,  'image' => 'tho_va_rua.jpg', 'status' => 1],
            ['id' => 59, 'category_id' => 4, 'author_id' => 6, 'publisher_id' => 1, 'name' => 'Quạ Và Công', 'description' => 'Câu chuyện giải thích màu lông các loài vật và bài học về sự tham lam.', 'price' => 30000.00,  'image' => 'qua_va_cong.jpg', 'status' => 1],
            ['id' => 60, 'category_id' => 4, 'author_id' => 6, 'publisher_id' => 1, 'name' => 'Thầy Bói Xem Voi', 'description' => 'Bài học ngụ ngôn phê phán những người có cái nhìn phiến diện.', 'price' => 35000.00,  'image' => 'thay_boi_xem_voi.jpg', 'status' => 1],
            ['id' => 61, 'category_id' => 4, 'author_id' => 6, 'publisher_id' => 1, 'name' => 'Ếch Ngồi Đáy Giếng', 'description' => 'Phê phán những kẻ hiểu biết nông cạn nhưng lại huênh hoang.', 'price' => 35000.00,  'image' => 'ech_ngoi_day_gieng.jpg', 'status' => 1],
            ['id' => 62, 'category_id' => 4, 'author_id' => 6, 'publisher_id' => 2, 'name' => 'Đeo Nhạc Cho Mèo', 'description' => 'Truyện ngụ ngôn phê phán những ý tưởng viển vông, không ai dám thực hiện.', 'price' => 30000.00,  'image' => 'deo_nhac_cho_meo.jpg', 'status' => 1],
            ['id' => 63, 'category_id' => 4, 'author_id' => 7, 'publisher_id' => 5, 'name' => 'Sói Và Cừu', 'description' => 'Bài học cảnh giác trước kẻ thù gian ác từ ngụ ngôn La Fontaine.', 'price' => 32000.00,  'image' => 'soi_va_cuu.jpg', 'status' => 1],
            ['id' => 64, 'category_id' => 4, 'author_id' => 8, 'publisher_id' => 2, 'name' => 'Kiến Và Ve Sầu', 'description' => 'Lời cảnh tỉnh về tầm quan trọng của việc lao động và lo xa cho tương lai.', 'price' => 30000.00,  'image' => 'kien_va_ve.jpg', 'status' => 1],
            ['id' => 65, 'category_id' => 4, 'author_id' => 9, 'publisher_id' => 1, 'name' => 'Trí Khôn Của Ta Đây', 'description' => 'Câu chuyện ngụ ngôn dân gian đề cao trí thông minh của con người trước sức mạnh thể xác.', 'price' => 35000.00,  'image' => 'tri_khon_cua_ta_day.jpg', 'status' => 1],

            // Thể loại: Manga
            ['id' => 66, 'category_id' => 5, 'author_id' => 1, 'publisher_id' => 4, 'name' => 'One Piece - Tập 1', 'description' => 'Khởi đầu hành trình tìm kiếm kho báu huyền thoại của Luffy.', 'price' => 30000.00,  'image' => 'one_piece_1.jpg', 'status' => 1],
            ['id' => 67, 'category_id' => 5, 'author_id' => 1, 'publisher_id' => 4, 'name' => 'One Piece - Tập 100', 'description' => 'Cột mốc lịch sử hoành tráng của bộ manga bán chạy nhất mọi thời đại.', 'price' => 35000.00,  'image' => 'one_piece_100.jpg', 'status' => 1],
            ['id' => 68, 'category_id' => 5, 'author_id' => 2, 'publisher_id' => 4, 'name' => 'Thám Tử Lừng Danh Conan - Tập 1', 'description' => 'Sự xuất hiện đầu tiên của cậu bé thám tử Edogawa Conan.', 'price' => 30000.00,  'image' => 'conan_1.jpg', 'status' => 1],
            ['id' => 69, 'category_id' => 5, 'author_id' => 2, 'publisher_id' => 4, 'name' => 'Thám Tử Lừng Danh Conan - Tập 100', 'description' => 'Trận chiến đỉnh cao dần hé lộ chân tướng Tổ chức Áo đen.', 'price' => 35000.00,  'image' => 'conan_100.jpg', 'status' => 1],
            ['id' => 70, 'category_id' => 5, 'author_id' => 3, 'publisher_id' => 4, 'name' => 'Doraemon Truyện Ngắn - Tập 1', 'description' => 'Chú mèo máy thông minh đến từ tương lai gặp gỡ Nobita vụng về.', 'price' => 25000.00, 'image' => 'doraemon_1.jpg', 'status' => 1],
            ['id' => 71, 'category_id' => 5, 'author_id' => 3, 'publisher_id' => 4, 'name' => 'Doraemon Đại Tuyển Tập - Tập 1', 'description' => 'Ấn bản cao cấp tập hợp toàn bộ các câu chuyện Doraemon.', 'price' => 125000.00,  'image' => 'doraemon_tuyen_tap.jpg', 'status' => 1],
            ['id' => 72, 'category_id' => 5, 'author_id' => 4, 'publisher_id' => 4, 'name' => 'Naruto - Tập 1', 'description' => 'Hành trình nỗ lực vươn lên để trở thành Hokage của chú nhóc Naruto.', 'price' => 30000.00,  'image' => 'naruto_1.jpg', 'status' => 1],
            ['id' => 73, 'category_id' => 5, 'author_id' => 5, 'publisher_id' => 4, 'name' => 'Thanh Gươm Diệt Quỷ - Tập 1', 'description' => 'Cuộc chiến đau thương bảo vệ em gái của Tanjirou chống lại loài quỷ.', 'price' => 35000.00,  'image' => 'demonslayer_1.jpg', 'status' => 1],
            ['id' => 74, 'category_id' => 5, 'author_id' => 6, 'publisher_id' => 4, 'name' => 'Chú Thuật Hồi Chiến - Tập 1', 'description' => 'Manga hành động giả tưởng kịch tính về các nguyền sư thời hiện đại.', 'price' => 40000.00,  'image' => 'jujutsu_1.jpg', 'status' => 1],
            ['id' => 75, 'category_id' => 5, 'author_id' => 7, 'publisher_id' => 4, 'name' => 'Dragon Ball (7 Viên Ngọc Rồng) - Tập 1', 'description' => 'Hành trình tầm ngọc huyền thoại của khỉ con Son Goku.', 'price' => 30000.00,  'image' => 'dragonball_1.jpg', 'status' => 1],
            ['id' => 76, 'category_id' => 5, 'author_id' => 8, 'publisher_id' => 4, 'name' => 'Slam Dunk - Tập 1', 'description' => 'Tác phẩm manga thể thao bóng rổ huyền thoại của Takehiko Inoue.', 'price' => 45000.00,  'image' => 'slamdunk_1.jpg', 'status' => 1],
            ['id' => 77, 'category_id' => 5, 'author_id' => 9, 'publisher_id' => 4, 'name' => 'Attack on Titan - Tập 1', 'description' => 'Cuộc chiến sinh tồn tàn khốc của nhân loại trước loài khổng lồ.', 'price' => 40000.00,  'image' => 'aot_1.jpg', 'status' => 1],
            ['id' => 78, 'category_id' => 5, 'author_id' => 10, 'publisher_id' => 4, 'name' => 'Spy x Family - Tập 1', 'description' => 'Gia đình điệp viên hư cấu siêu hài hước và đáng yêu.', 'price' => 45000.00,  'image' => 'spy_1.jpg', 'status' => 1],
            ['id' => 79, 'category_id' => 5, 'author_id' => 11, 'publisher_id' => 4, 'name' => 'Haikyu!! (Chàng Trai Bóng Chuyền) - Tập 1', 'description' => 'Ý chí và đam mê cháy bỏng trên sân đấu bóng chuyền học đường.', 'price' => 35000.00,  'image' => 'haikyu_1.jpg', 'status' => 1],
            ['id' => 80, 'category_id' => 5, 'author_id' => 12, 'publisher_id' => 4, 'name' => 'Monster (Quái Vật) - Tập 1', 'description' => 'Kiệt tác manga trinh thám tâm lý ly kỳ của Naoki Urasawa.', 'price' => 85000.00, 'image' => 'monster_1.jpg', 'status' => 1],

            // Thể loại: Cổ tích
            ['id' => 81, 'category_id' => 6, 'author_id' => 1, 'publisher_id' => 1, 'name' => 'Truyện Cổ Grimm Toàn Tập', 'description' => 'Kho tàng truyện cổ tích nổi tiếng thế giới của hai anh em nhà Grimm.', 'price' => 180000.00,  'image' => 'co_tich_grimm.jpg', 'status' => 1],
            ['id' => 82, 'category_id' => 6, 'author_id' => 2, 'publisher_id' => 1, 'name' => 'Truyện Cổ Andersen', 'description' => 'Những câu chuyện cảm động đầy tính nhân văn của đại văn hào Đan Mạch.', 'price' => 135000.00,  'image' => 'andersen.jpg', 'status' => 1],
            ['id' => 83, 'category_id' => 6, 'author_id' => 3, 'publisher_id' => 1, 'name' => 'Nghìn Lẻ Một Đêm (Trọn Bộ)', 'description' => 'Thế giới huyền ảo của xứ sở Ba Tư qua lời kể của nàng Scheherazade.', 'price' => 240000.00, 'image' => 'nghin_le_mot_dem.jpg', 'status' => 1],
            ['id' => 84, 'category_id' => 6, 'author_id' => 4, 'publisher_id' => 1, 'name' => 'Truyện Cổ Tích Việt Nam Đặc Sắc', 'description' => 'Tuyển tập các truyện cổ tích nuôi dưỡng tâm hồn trẻ em Việt Nam.', 'price' => 95000.00,  'image' => 'co_tich_vn.jpg', 'status' => 1],
            ['id' => 85, 'category_id' => 6, 'author_id' => 4, 'publisher_id' => 1, 'name' => 'Tấm Cám', 'description' => 'Truyện cổ tích về sự đấu tranh giữa cái thiện và cái ác, ở hiền gặp lành.', 'price' => 35000.00,  'image' => 'tam_cam.jpg', 'status' => 1],
            ['id' => 86, 'category_id' => 6, 'author_id' => 4, 'publisher_id' => 1, 'name' => 'Thạch Sanh', 'description' => 'Hình tượng người anh hùng dũng cảm, thật thà chống lại kẻ gian ác.', 'price' => 35000.00,  'image' => 'thach_sanh.jpg', 'status' => 1],
            ['id' => 87, 'category_id' => 6, 'author_id' => 4, 'publisher_id' => 2, 'name' => 'Sự Tích Trầu Cau', 'description' => 'Câu chuyện cảm động giải thích phong tục trầu cau và tình anh em, nghĩa vợ chồng.', 'price' => 30000.00,  'image' => 'trau_cau.jpg', 'status' => 1],
            ['id' => 88, 'category_id' => 6, 'author_id' => 4, 'publisher_id' => 2, 'name' => 'Thánh Gióng', 'description' => 'Biểu tượng của sức mạnh thần kỳ và lòng yêu nước chống giặc ngoại xâm.', 'price' => 35000.00,  'image' => 'thanh_giong.jpg', 'status' => 1],
            ['id' => 89, 'category_id' => 6, 'author_id' => 4, 'publisher_id' => 1, 'name' => 'Sơn Tinh Thủy Tinh', 'description' => 'Thần thoại cổ tích giải thích hiện tượng lũ lụt hàng năm tại Việt Nam.', 'price' => 35000.00,  'image' => 'son_tinh_thuy_tinh.jpg', 'status' => 1],
            ['id' => 90, 'category_id' => 6, 'author_id' => 4, 'publisher_id' => 2, 'name' => 'Sự Tích Hồ Gươm', 'description' => 'Truyện cổ tích lịch sử ca ngợi cuộc khởi nghĩa Lam Sơn và vua Lê Lợi.', 'price' => 40000.00,  'image' => 'ho_guom.jpg', 'status' => 1],
            ['id' => 91, 'category_id' => 6, 'author_id' => 5, 'publisher_id' => 2, 'name' => 'Cinderella (Cô Bé Lọ Lem)', 'description' => 'Truyện cổ tích phương Tây kinh điển về chiếc giày thủy tinh.', 'price' => 45000.00,  'image' => 'lo_lem.jpg', 'status' => 1],
            ['id' => 92, 'category_id' => 6, 'author_id' => 5, 'publisher_id' => 2, 'name' => 'Bạch Tuyết Và Bảy Chú Lùn', 'description' => 'Nàng công chúa xinh đẹp vượt qua sự ghen ghét của hoàng hậu độc ác.', 'price' => 45000.00,  'image' => 'bach_tuyet.jpg', 'status' => 1],
            ['id' => 93, 'category_id' => 6, 'author_id' => 5, 'publisher_id' => 1, 'name' => 'Aladdin Và Cây Đèn Thần', 'description' => 'Chàng trai nghèo dũng cảm chiêu phục thần đèn chiến thắng phù thủy.', 'price' => 50000.00,  'image' => 'aladdin.jpg', 'status' => 1],
            ['id' => 94, 'category_id' => 6, 'author_id' => 4, 'publisher_id' => 2, 'name' => 'Cây Tre Trăm Đốt', 'description' => 'Câu chuyện tôn vinh sự thật thà và câu thần chú "Khắc nhập khắc xuất".', 'price' => 30000.00,  'image' => 'cay_tre_tram_dot.jpg', 'status' => 1],
            ['id' => 95, 'category_id' => 6, 'author_id' => 4, 'publisher_id' => 1, 'name' => 'Ăn Khế Trả Vàng', 'description' => 'Bài học về sự đền ơn đáp nghĩa đối lập với lòng tham lam vô độ.', 'price' => 35000.00,  'image' => 'an_khe_tra_vang.jpg', 'status' => 1],

            // Thể loại: Văn học nước ngoài
            ['id' => 96, 'category_id' => 7, 'author_id' => 1, 'publisher_id' => 2, 'name' => 'Hoàng Tử Bé', 'description' => 'Kiệt tác văn học Pháp chứa đựng triết lý sâu sắc phù hợp với mọi lứa tuổi.', 'price' => 65000.00,  'image' => 'hoang_tu_be.jpg', 'status' => 1],
            ['id' => 97, 'category_id' => 7, 'author_id' => 2, 'publisher_id' => 1, 'name' => 'Ông Già Và Biển Cả', 'description' => 'Ý chí bất khuất của con người trước thiên nhiên của Ernest Hemingway.', 'price' => 52000.00,  'image' => 'ong_gia_va_bien_ca.jpg', 'status' => 1],
            ['id' => 98, 'category_id' => 7, 'author_id' => 3, 'publisher_id' => 2, 'name' => 'Đại Gia Gatsby', 'description' => 'Bức tranh phơi bày giấc mơ Mỹ xa hoa nhưng trống rỗng những năm 1920.', 'price' => 78000.00, 'image' => 'gatsby.jpg', 'status' => 1],
            ['id' => 99, 'category_id' => 7, 'author_id' => 4, 'publisher_id' => 5, 'name' => 'Nhà Thờ Đức Bà Paris', 'description' => 'Bản tình ca bi tráng giữa thằng gù Quasimodo và nàng Esmeralda.', 'price' => 165000.00, 'image' => 'nha_tho_duc_ba.jpg', 'status' => 1],
            ['id' => 100, 'category_id' => 7, 'author_id' => 4, 'publisher_id' => 5, 'name' => 'Những Người Khốn Khổ (Trọn Bộ)', 'description' => 'Thiên sử thi vĩ đại về tình thương và công lý xã hội của Victor Hugo.', 'price' => 380000.00, 'image' => 'nhung_nguoi_khon_kho.jpg', 'status' => 1],
            ['id' => 101, 'category_id' => 7, 'author_id' => 5, 'publisher_id' => 3, 'name' => 'Cuốn Theo Chiều Gió (Trọn Bộ)', 'description' => 'Tác phẩm văn học Mỹ kinh điển về tình yêu và nghị lực thời nội chiến.', 'price' => 280000.00, 'image' => 'cuon_theo_chieu_gio.jpg', 'status' => 1],
            ['id' => 102, 'category_id' => 7, 'author_id' => 6, 'publisher_id' => 2, 'name' => '1984', 'description' => 'Tiểu thuyết tiên tri viễn tưởng đen tối xuất sắc của George Orwell.', 'price' => 95000.00, 'image' => '1984.jpg', 'status' => 1],
            ['id' => 103, 'category_id' => 7, 'author_id' => 7, 'publisher_id' => 5, 'name' => 'Anh Em Nhà Karamazov', 'description' => 'Đỉnh cao văn học Nga mổ xẻ sâu sắc tâm lý và đức tin tôn giáo.', 'price' => 260000.00, 'image' => 'anh_em_nha_karamazov.jpg', 'status' => 1],
            ['id' => 104, 'category_id' => 7, 'author_id' => 8, 'publisher_id' => 1, 'name' => 'Don Quijote - Nhà Quý Tộc Tài Ba Xứ Mancha', 'description' => 'Tác phẩm châm biếm hiệp sĩ kinh điển của nền văn học Tây Ban Nha.', 'price' => 195000.00, 'image' => 'don_quijote.jpg', 'status' => 1],
            ['id' => 105, 'category_id' => 7, 'author_id' => 9, 'publisher_id' => 2, 'name' => 'Chúa Tể Những Chiếc Nhẫn - Tập 1', 'description' => 'Bản hùng ca giả tưởng vĩ đại đặt nền móng cho fantasy hiện đại.', 'price' => 165000.00, 'image' => 'lord_of_the_rings_1.jpg', 'status' => 1],
            ['id' => 106, 'category_id' => 7, 'author_id' => 9, 'publisher_id' => 2, 'name' => 'Anh Chàng Hobbit', 'description' => 'Hành trình phiêu lưu bất ngờ của Bilbo Baggins trước kỷ nguyên Chúa Nhẫn.', 'price' => 115000.00, 'image' => 'hobbit.jpg', 'status' => 1],
            ['id' => 107, 'category_id' => 7, 'author_id' => 10, 'publisher_id' => 6, 'name' => 'Mối Tình Đầu', 'description' => 'Tác phẩm lãng mạn nhẹ nhàng nhưng u sầu của Ivan Turgenev.', 'price' => 60000.00, 'image' => 'moi_tinh_dau.jpg', 'status' => 1],
            ['id' => 108, 'category_id' => 7, 'author_id' => 11, 'publisher_id' => 2, 'name' => 'Tiếng Gọi Nơi Hoang Dã', 'description' => 'Bản năng sinh tồn mạnh mẽ của chú chó Buck giữa vùng tuyết trắng.', 'price' => 68000.00,  'image' => 'tieng_goi_noi_hoang_da.jpg', 'status' => 1],
            ['id' => 109, 'category_id' => 7, 'author_id' => 12, 'publisher_id' => 1, 'name' => 'Trà Hoa Nữ', 'description' => 'Câu chuyện tình yêu đầy đau đớn và định kiến xã hội của Alexandre Dumas con.', 'price' => 85000.00, 'image' => 'tra_hoa_nu.jpg', 'status' => 1],
            ['id' => 110, 'category_id' => 7, 'author_id' => 13, 'publisher_id' => 2, 'name' => 'Tên Của Đóa Hồng', 'description' => 'Sự kết hợp hoàn hảo giữa trinh thám trung cổ và triết học ký hiệu học.', 'price' => 175000.00, 'image' => 'ten_cua_doa_hong.jpg', 'status' => 1],

            // Thể loại: Triết lý sống
            ['id' => 111, 'category_id' => 8, 'author_id' => 1, 'publisher_id' => 6, 'name' => 'Đắc Nhân Tâm', 'description' => 'Cuốn sách nghệ thuật ứng xử kinh điển, bán chạy nhất mọi thời đại.', 'price' => 86000.00,  'image' => 'dac_nhan_tam.jpg', 'status' => 1],
            ['id' => 112, 'category_id' => 8, 'author_id' => 1, 'publisher_id' => 6, 'name' => 'Quảy Gánh Lo Đi Và Vui Sống', 'description' => 'Bài học quý giá giúp loại bỏ lo lắng để tận hưởng cuộc sống trọn vẹn.', 'price' => 76000.00,  'image' => 'quay_ganh_lo_di.jpg', 'status' => 1],
            ['id' => 113, 'category_id' => 8, 'author_id' => 2, 'publisher_id' => 2, 'name' => 'Hiểu Về Trái Tim', 'description' => 'Cuốn sách phân tích tâm lý, tìm lại sự bình yên của Thích Minh Niệm.', 'price' => 135000.00,  'image' => 'hieu_ve_trai_tim.jpg', 'status' => 1],
            ['id' => 114, 'category_id' => 8, 'author_id' => 2, 'publisher_id' => 2, 'name' => 'Làm Như Chơi', 'description' => 'Cách thức ứng dụng thiền vào đời sống bận rộn một cách nhẹ nhàng.', 'price' => 120000.00,  'image' => 'lam_nhu_choi.jpg', 'status' => 1],
            ['id' => 115, 'category_id' => 8, 'author_id' => 3, 'publisher_id' => 3, 'name' => 'Tìm Kiếm Lẽ Sống', 'description' => 'Trải nghiệm sinh tử tại trại tập trung và triết lý sống của Viktor Frankl.', 'price' => 88000.00, 'image' => 'tim_kiem_le_song.jpg', 'status' => 1],
            ['id' => 116, 'category_id' => 8, 'author_id' => 4, 'publisher_id' => 2, 'name' => 'Muôn Kiếp Nhân Sinh - Tập 1', 'description' => 'Góc nhìn triết lý sâu sắc về luật nhân quả và luân hồi của Nguyên Phong.', 'price' => 168000.00,  'image' => 'muon_kiep_nhan_sinh_1.jpg', 'status' => 1],
            ['id' => 117, 'category_id' => 8, 'author_id' => 4, 'publisher_id' => 2, 'name' => 'Muôn Kiếp Nhân Sinh - Tập 2', 'description' => 'Tiếp nối hành trình thức tỉnh tâm linh và giải mã tương lai nhân loại.', 'price' => 188000.00,  'image' => 'muon_kiep_nhan_sinh_2.jpg', 'status' => 1],
            ['id' => 118, 'category_id' => 8, 'author_id' => 5, 'publisher_id' => 5, 'name' => 'Lối Sống Tối Giản Của Người Nhật', 'description' => 'Triết lý từ bỏ bớt vật chất để tìm kiếm hạnh phúc đích thực của Sasaki Fumio.', 'price' => 95000.00,  'image' => 'loi_song_toi_gian.jpg', 'status' => 1],
            ['id' => 119, 'category_id' => 8, 'author_id' => 6, 'publisher_id' => 6, 'name' => 'Bốn Thỏa Ước', 'description' => 'Chỉ dẫn thực tế để đạt đến tự do cá nhân từ trí tuệ cổ xưa của người Toltec.', 'price' => 72000.00, 'image' => 'bon_thoa_uoc.jpg', 'status' => 1],
            ['id' => 120, 'category_id' => 8, 'author_id' => 7, 'publisher_id' => 2, 'name' => 'Dám Bị Ghét', 'description' => 'Triết lý tâm lý học Adler giúp bạn tự do và hạnh phúc trong các mối quan hệ.', 'price' => 96000.00,  'image' => 'dam_bi_ghet.jpg', 'status' => 1],
        ]);

        // 7. Seed Orders (BẢN GHI MỚI THÊM VÀO)
        DB::table('orders')->insert([
            [
                'id' => 1,
                'user_id' => 2, // Ứng với User: akino
                'voucher_id' => null,
                'discount_id' => null,
                'total_amount' => 250000.00,
                'status' => 'pending', // Chờ xử lý
                'shipping_name' => 'akino',
                'shipping_phone' => '0987654321',
                'shipping_address' => '123 Đường A, Quận 1, TP.HCM',
                'notes' => 'Giao giờ hành chính',
                'payment_method' => 'COD',
                'created_at' => Carbon::now()->subDays(2),
            ],
            [
                'id' => 2,
                'user_id' => 3, // Ứng với User: Quốc anh
                'voucher_id' => null,
                'discount_id' => null,
                'total_amount' => 155000.00,
                'status' => 'confirmed', // Đã xác nhận
                'shipping_name' => 'Quốc anh',
                'shipping_phone' => '0912345678',
                'shipping_address' => '456 Đường B, Quận 2, TP.HCM',
                'notes' => null,
                'payment_method' => 'VNPAY',
                'created_at' => Carbon::now()->subDays(1),
            ],
            [
                'id' => 3,
                'user_id' => 4, // Ứng với User: mike
                'voucher_id' => null,
                'discount_id' => null,
                'total_amount' => 500000.00,
                'status' => 'shipping', // Đang giao
                'shipping_name' => 'mike',
                'shipping_phone' => '0933334444',
                'shipping_address' => '789 Đường C, Ba Đình, Hà Nội',
                'notes' => 'Gọi trước khi giao nhé',
                'payment_method' => 'COD',
                'created_at' => Carbon::now()->subHours(10),
            ],
            [
                'id' => 4,
                'user_id' => 5, // Ứng với User: qưa
                'voucher_id' => null,
                'discount_id' => null,
                'total_amount' => 95000.00,
                'status' => 'completed', // Hoàn thành
                'shipping_name' => 'qưa',
                'shipping_phone' => '0900111222',
                'shipping_address' => '101 Đường D, Hải Châu, Đà Nẵng',
                'notes' => null,
                'payment_method' => 'VNPAY',
                'created_at' => Carbon::now()->subDays(5),
            ],
            [
                'id' => 5,
                'user_id' => 2, // Đơn thứ 2 của User: akino
                'voucher_id' => null,
                'discount_id' => null,
                'total_amount' => 345000.00,
                'status' => 'cancelled', // Đã hủy
                'shipping_name' => 'akino',
                'shipping_phone' => '0987654321',
                'shipping_address' => '123 Đường A, Quận 1, TP.HCM',
                'notes' => 'Hủy do đổi ý',
                'payment_method' => 'COD',
                'created_at' => Carbon::now()->subHours(2),
            ]
        ]);
        $products = DB::table('products')->get();

        $data = [];

        foreach ($products as $product) {

            $standardStock = rand(50, 200);

            $data[] = [
                'product_id' => $product->id,
                'edition' => 'Standard',
                'sku' => null,
                'price' => $product->price,
                'stock' => $standardStock,
            ];

            $data[] = [
                'product_id' => $product->id,
                'edition' => 'Special',
                'sku' => null,
                'price' => $product->price + 40000,
                'stock' => floor($standardStock * 0.4),
            ];

            $data[] = [
                'product_id' => $product->id,
                'edition' => 'Special Signed',
                'sku' => null,
                'price' => $product->price + 90000,
                'stock' => floor($standardStock * 0.1),
            ];
        }

        DB::table('product_variants')->insert($data);
    }
}
