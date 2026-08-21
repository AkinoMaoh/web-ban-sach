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
                'title' => 'Vì sao đọc sách mỗi ngày có thể thay đổi cách bạn học và làm việc?',
                'image' => 'news1.jpg',
                'summary' => 'Duy trì thói quen đọc sách mỗi ngày không chỉ giúp mở rộng kiến thức mà còn cải thiện khả năng tập trung, tư duy và tiếp nhận thông tin.',
                'content' => '
                    <h2>Đọc sách không chỉ là một hình thức giải trí</h2>

                    <p>Trong cuộc sống hiện đại, chúng ta tiếp nhận một lượng thông tin rất lớn mỗi ngày thông qua mạng xã hội, video và các nền tảng trực tuyến. Tuy nhiên, việc dành thời gian đọc một cuốn sách vẫn mang lại những giá trị đặc biệt mà các hình thức tiếp nhận thông tin nhanh khó có thể thay thế.</p>

                    <p>Khi đọc sách, người đọc cần dành thời gian theo dõi nội dung, ghi nhớ các nhân vật, sự kiện và kết nối những thông tin ở nhiều phần khác nhau. Quá trình này giúp khả năng tập trung được rèn luyện một cách tự nhiên.</p>

                    <h3>Đọc sách giúp mở rộng kiến thức</h3>

                    <p>Mỗi cuốn sách thường mang đến một góc nhìn hoặc một lượng kiến thức riêng. Một cuốn sách lịch sử có thể giúp chúng ta hiểu hơn về quá khứ, trong khi sách khoa học giúp giải thích những hiện tượng xảy ra trong cuộc sống.</p>

                    <p>Điều quan trọng không phải là đọc thật nhiều sách trong thời gian ngắn mà là lựa chọn những nội dung phù hợp và duy trì thói quen đọc đều đặn.</p>

                    <h3>Hãy bắt đầu từ những mục tiêu nhỏ</h3>

                    <p>Nếu bạn chưa có thói quen đọc sách, hãy bắt đầu với khoảng 15 đến 20 phút mỗi ngày. Sau một thời gian, việc đọc sẽ trở thành một phần quen thuộc trong lịch sinh hoạt và bạn có thể tăng dần thời lượng khi cảm thấy thoải mái.</p>

                    <p>Một vài trang sách mỗi ngày có thể không tạo ra sự khác biệt ngay lập tức, nhưng việc duy trì thói quen trong thời gian dài có thể đem lại những thay đổi đáng kể.</p>
                ',
                'status' => 1,
                'is_featured' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'title' => '5 cách lựa chọn sách phù hợp khi bạn chưa biết nên đọc gì',
                'image' => 'news2.jpg',
                'summary' => 'Không biết nên bắt đầu từ đâu khi chọn sách? Một vài tiêu chí đơn giản có thể giúp bạn tìm được những cuốn sách phù hợp hơn.',
                'content' => '
                    <h2>Lựa chọn sách đôi khi không hề đơn giản</h2>

                    <p>Với hàng nghìn đầu sách thuộc nhiều thể loại khác nhau, việc lựa chọn một cuốn sách phù hợp có thể khiến nhiều người cảm thấy bối rối. Đặc biệt với những người mới bắt đầu hình thành thói quen đọc sách, việc chọn đúng nội dung sẽ giúp trải nghiệm đọc trở nên thú vị hơn.</p>

                    <h3>1. Xác định chủ đề bạn quan tâm</h3>

                    <p>Hãy bắt đầu bằng việc suy nghĩ xem bạn đang muốn tìm hiểu điều gì. Có thể đó là kỹ năng sống, kinh doanh, lịch sử, khoa học, văn học hoặc đơn giản là một câu chuyện để thư giãn.</p>

                    <h3>2. Xem phần giới thiệu của sách</h3>

                    <p>Phần giới thiệu thường cung cấp những thông tin cơ bản về nội dung và cách tiếp cận của tác giả. Đây là cách nhanh chóng để biết một cuốn sách có phù hợp với nhu cầu của mình hay không.</p>

                    <h3>3. Tham khảo đánh giá từ độc giả</h3>

                    <p>Những đánh giá từ người đã đọc sách có thể cung cấp thêm góc nhìn trước khi quyết định mua. Tuy nhiên, mỗi người có sở thích khác nhau nên bạn không nhất thiết phải lựa chọn theo đánh giá của số đông.</p>

                    <h3>4. Quan tâm đến tác giả</h3>

                    <p>Nếu bạn từng yêu thích một cuốn sách, hãy thử tìm thêm những tác phẩm khác của cùng tác giả. Phong cách viết thường có những điểm tương đồng và khả năng bạn yêu thích những tác phẩm khác cũng khá cao.</p>

                    <h3>5. Đừng ngại thử một thể loại mới</h3>

                    <p>Đôi khi một cuốn sách nằm ngoài sở thích quen thuộc lại mang đến những trải nghiệm thú vị nhất. Vì vậy, hãy dành cơ hội cho những chủ đề mới và khám phá thêm những lĩnh vực mà trước đây bạn chưa từng quan tâm.</p>
                ',
                'status' => 1,
                'is_featured' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'title' => 'Sách giấy hay sách điện tử: Đâu là lựa chọn phù hợp với bạn?',
                'image' => 'news3.jpg',
                'summary' => 'Sách giấy và sách điện tử đều có những ưu điểm riêng. Việc lựa chọn phụ thuộc vào nhu cầu, thói quen và hoàn cảnh của mỗi người.',
                'content' => '
                    <h2>Hai hình thức đọc sách phổ biến</h2>

                    <p>Sự phát triển của công nghệ đã mang đến nhiều thay đổi trong cách chúng ta tiếp cận sách. Bên cạnh sách giấy truyền thống, sách điện tử ngày càng trở nên phổ biến nhờ sự tiện lợi và khả năng lưu trữ số lượng lớn nội dung.</p>

                    <h3>Ưu điểm của sách giấy</h3>

                    <p>Sách giấy mang lại cảm giác đọc quen thuộc và tạo cho nhiều người sự tập trung tốt hơn. Việc cầm một cuốn sách trên tay, lật từng trang và đánh dấu những đoạn yêu thích cũng tạo nên một trải nghiệm riêng mà nhiều độc giả vẫn yêu thích.</p>

                    <p>Ngoài ra, sách giấy có thể trở thành một phần của không gian sống và tạo cảm giác thú vị khi xây dựng một tủ sách cá nhân.</p>

                    <h3>Ưu điểm của sách điện tử</h3>

                    <p>Sách điện tử có lợi thế lớn về tính tiện lợi. Một thiết bị nhỏ có thể lưu trữ rất nhiều cuốn sách, giúp người đọc dễ dàng mang theo khi đi học, đi làm hoặc đi du lịch.</p>

                    <p>Nhiều ứng dụng đọc sách còn hỗ trợ thay đổi kích thước chữ, tìm kiếm từ khóa và ghi chú, giúp việc đọc trở nên linh hoạt hơn.</p>

                    <h3>Không có lựa chọn nào hoàn toàn tốt hơn</h3>

                    <p>Sách giấy hay sách điện tử đều có những ưu điểm riêng. Điều quan trọng nhất vẫn là lựa chọn hình thức giúp bạn cảm thấy thoải mái và duy trì được thói quen đọc lâu dài.</p>
                ',
                'status' => 1,
                'is_featured' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'title' => 'Những thể loại sách giúp bạn khám phá thêm nhiều góc nhìn mới',
                'image' => 'news4.jpg',
                'summary' => 'Khám phá những thể loại sách khác nhau là một cách thú vị để mở rộng kiến thức và nhìn nhận cuộc sống từ nhiều góc độ.',
                'content' => '
                    <h2>Mỗi thể loại sách mang đến một thế giới khác nhau</h2>

                    <p>Một trong những điều thú vị nhất của việc đọc sách là khả năng đưa chúng ta đến những thế giới mà bình thường khó có cơ hội trải nghiệm. Mỗi thể loại lại có cách tiếp cận và giá trị riêng.</p>

                    <h3>Văn học</h3>

                    <p>Những tác phẩm văn học giúp người đọc khám phá cảm xúc, suy nghĩ và hoàn cảnh sống của nhiều nhân vật khác nhau. Qua đó, chúng ta có thể hiểu hơn về con người và những vấn đề trong cuộc sống.</p>

                    <h3>Lịch sử</h3>

                    <p>Sách lịch sử giúp người đọc nhìn lại những sự kiện đã diễn ra trong quá khứ. Những câu chuyện lịch sử không chỉ cung cấp kiến thức mà còn giúp chúng ta hiểu nguyên nhân và tác động của nhiều sự kiện.</p>

                    <h3>Khoa học</h3>

                    <p>Sách khoa học giúp giải thích những hiện tượng quen thuộc bằng kiến thức và lập luận. Đây là lựa chọn phù hợp với những người thích tìm hiểu cách thế giới vận hành.</p>

                    <h3>Kỹ năng và phát triển bản thân</h3>

                    <p>Những cuốn sách về kỹ năng thường tập trung vào các vấn đề như quản lý thời gian, giao tiếp, học tập và xây dựng thói quen. Người đọc có thể tìm thấy nhiều ý tưởng để áp dụng vào cuộc sống hàng ngày.</p>

                    <p>Thay vì chỉ đọc một thể loại, hãy thử thay đổi chủ đề theo từng giai đoạn. Điều này giúp trải nghiệm đọc trở nên đa dạng và thú vị hơn.</p>
                ',
                'status' => 1,
                'is_featured' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'title' => 'Xây dựng thói quen đọc sách: Bắt đầu thế nào để không bỏ cuộc?',
                'image' => 'news5.jpg',
                'summary' => 'Duy trì thói quen đọc sách cần sự kiên trì. Hãy bắt đầu từ những mục tiêu nhỏ thay vì đặt áp lực phải đọc thật nhiều.',
                'content' => '
                    <h2>Đọc sách đều đặn quan trọng hơn đọc thật nhiều</h2>

                    <p>Nhiều người từng đặt mục tiêu đọc hàng chục cuốn sách mỗi năm nhưng nhanh chóng bỏ cuộc sau một thời gian ngắn. Nguyên nhân thường không nằm ở việc thiếu thời gian mà ở việc đặt mục tiêu quá lớn ngay từ đầu.</p>

                    <h3>Bắt đầu với một khoảng thời gian ngắn</h3>

                    <p>Thay vì yêu cầu bản thân phải đọc hàng trăm trang mỗi ngày, bạn có thể bắt đầu với 10 đến 15 phút. Khi việc đọc trở thành một hoạt động quen thuộc, bạn có thể tăng thời gian một cách tự nhiên.</p>

                    <h3>Chọn thời điểm cố định</h3>

                    <p>Một trong những cách đơn giản để xây dựng thói quen là lựa chọn một khoảng thời gian cố định trong ngày. Đó có thể là buổi sáng, sau giờ học hoặc trước khi đi ngủ.</p>

                    <h3>Đừng ép bản thân phải đọc một cuốn sách không phù hợp</h3>

                    <p>Nếu một cuốn sách khiến bạn mất hứng thú, việc chuyển sang một cuốn khác hoàn toàn không phải là thất bại. Mục tiêu cuối cùng là tìm được những nội dung khiến bạn muốn tiếp tục đọc.</p>

                    <h3>Kiên trì trong thời gian dài</h3>

                    <p>Một thói quen không hình thành chỉ sau vài ngày. Hãy cho bản thân thời gian và tập trung vào sự tiến bộ từng chút một. Khi đọc sách trở thành một phần tự nhiên trong cuộc sống, bạn sẽ không còn cảm thấy đó là một nhiệm vụ bắt buộc.</p>
                ',
                'status' => 1,
                'is_featured' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],

        ]);
    }
}