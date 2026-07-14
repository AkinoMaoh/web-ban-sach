@extends('layout.user')

@section('content')
<!-- Breadcrumb -->
<div class="bg-light py-3 mb-4 border-bottom">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent px-0 mb-0 py-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('user.index') }}" class="text-muted"><i class="fas fa-home"></i> Trang chủ</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('user.news') ?? '#' }}" class="text-muted">Tin tức & Bài viết</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page" style="color: var(--primary-color); font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 250px;">
                    {{ $post->title ?? 'Top 10 cuốn tiểu thuyết kinh điển bạn nhất định phải đọc một lần trong đời' }}
                </li>
            </ol>
        </nav>
    </div>
</div>

<div class="container mb-5 pb-4">
    <div class="row">
        <!-- CỘT TRÁI: NỘI DUNG BÀI VIẾT (col-lg-8) -->
        <article class="col-lg-8 mb-5 mb-lg-0">
            <div class="card border-0 shadow-sm rounded bg-white">
                <div class="card-body p-4 p-md-5">
                    
                    <!-- Header Bài viết -->
                    <header class="mb-4 border-bottom pb-4">
                        <div class="mb-3">
                            <span class="badge px-3 py-2" style="background-color: #FFF6F0; color: #D35400; font-size: 13px; font-weight: 500;">
                                {{ $post->category->name ?? 'Review Sách' }}
                            </span>
                        </div>
                        <h1 class="serif-font font-weight-bold text-dark mb-3" style="line-height: 1.4; font-size: 2.2rem;">
                            {{ $post->title ?? 'Top 10 cuốn tiểu thuyết kinh điển bạn nhất định phải đọc một lần trong đời' }}
                        </h1>
                        <div class="d-flex flex-wrap align-items-center text-muted" style="font-size: 14px;">
                            <span class="me-3 mr-3 mb-2"><i class="far fa-calendar-alt me-1 mr-1"></i> {{ isset($post) ? $post->created_at->format('d/m/Y - H:i') : '10/07/2026 - 14:30' }}</span>
                            <span class="me-3 mr-3 mb-2"><i class="far fa-user me-1 mr-1"></i> Đăng bởi: <strong class="text-dark">{{ $post->author->name ?? 'Admin' }}</strong></span>
                            <span class="mb-2"><i class="far fa-eye me-1 mr-1"></i> {{ $post->views ?? '1,245' }} lượt xem</span>
                        </div>
                    </header>

                    <!-- Tóm tắt bài viết -->
                    @if(isset($post->summary) || true) {{-- Sửa 'true' thành điều kiện thực tế của bạn --}}
                    <div class="p-4 mb-4 rounded" style="background-color: #F8F9FA; border-left: 4px solid var(--primary-color);">
                        <p class="mb-0 font-weight-bold text-dark" style="line-height: 1.6; font-size: 15px;">
                            {{ $post->summary ?? 'Khám phá những tựa sách đã làm say đắm hàng triệu độc giả trên toàn thế giới với những câu chuyện sâu sắc, đầy ý nghĩa nhân văn và bài học cuộc sống thiết thực. Đọc không chỉ là để giải trí mà còn là hành trình nuôi dưỡng tâm hồn.' }}
                        </p>
                    </div>
                    @endif

                    <!-- Nội dung chính (Hiển thị dữ liệu từ CKEditor/TinyMCE) -->
                    <div class="post-content" style="line-height: 1.8; font-size: 16px; color: #333;">
                        @if(isset($post->content))
                            {!! $post->content !!}
                        @else
                            {{-- Dữ liệu ảo Mockup --}}
                            <p>Sách là kho tàng tri thức vô giá của nhân loại. Mỗi cuốn sách mở ra là một thế giới mới, mang đến cho người đọc những trải nghiệm tuyệt vời, những bài học quý báu và những cảm xúc khó quên.</p>
                            
                            <img src="https://picsum.photos/800/450?random=1" alt="Ảnh minh hoạ" class="img-fluid rounded mb-4 mt-2 w-100 shadow-sm" style="object-fit: cover;">
                            
                            <h3 class="serif-font font-weight-bold mt-4 mb-3" style="color: #2C3E50;">1. Đắc Nhân Tâm - Dale Carnegie</h3>
                            <p>Đắc Nhân Tâm của Dale Carnegie là cuốn sách nổi tiếng nhất, bán chạy nhất và có tầm ảnh hưởng nhất của mọi thời đại. Cuốn sách đã được chuyển ngữ sang hầu hết các thứ tiếng trên thế giới và có mặt ở hàng trăm quốc gia. Đây là cuốn sách duy nhất về thể loại self-help liên tục đứng đầu danh mục sách bán chạy nhất (Best-selling Books) do báo The New York Times bình chọn suốt 10 năm liền.</p>
                            
                            <h3 class="serif-font font-weight-bold mt-4 mb-3" style="color: #2C3E50;">2. Nhà Giả Kim - Paulo Coelho</h3>
                            <p>Tất cả những trải nghiệm trong chuyến phiêu du theo đuổi vận mệnh của mình đã giúp Santiago thấu hiểu được ý nghĩa sâu xa nhất của hạnh phúc, hòa hợp với vũ trụ và con người. Cuốn sách như một lời nhắc nhở nhẹ nhàng: "Khi bạn khao khát một điều gì đó, cả vũ trụ sẽ hợp lực giúp bạn đạt được điều đó".</p>

                            <blockquote class="p-3 my-4 bg-light rounded" style="border-left: 4px solid #D35400; font-style: italic; color: #555;">
                                "Người đọc sách sống ngàn cuộc đời trước khi chết. Người không bao giờ đọc chỉ sống một cuộc đời." - George R.R. Martin
                            </blockquote>
                            
                            <p>Hy vọng qua bài viết này, bạn sẽ tìm được cho mình một cuốn sách ưng ý để bổ sung vào kệ sách của mình. Hãy nhớ rằng, thời gian dành cho việc đọc sách không bao giờ là lãng phí.</p>
                        @endif
                    </div>

                    <!-- Footer Bài viết (Chia sẻ mạng xã hội) -->
                    <div class="mt-5 pt-4 border-top d-flex align-items-center justify-content-between flex-wrap">
                        <div class="mb-3 mb-md-0">
                            <span class="font-weight-bold serif-font text-dark me-2 mr-2">Tags:</span>
                            <a href="#" class="badge badge-light border text-muted p-2 me-1 mr-1 text-decoration-none">Tiểu thuyết</a>
                            <a href="#" class="badge badge-light border text-muted p-2 me-1 mr-1 text-decoration-none">Kinh điển</a>
                            <a href="#" class="badge badge-light border text-muted p-2 text-decoration-none">Review</a>
                        </div>
                        <div>
                            <span class="font-weight-bold serif-font text-dark me-3 mr-3">Chia sẻ:</span>
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ url()->current() }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-circle me-1 mr-1" style="width: 36px; height: 36px; padding-top: 6px;" title="Chia sẻ lên Facebook"><i class="fab fa-facebook-f"></i></a>
                            <a href="https://twitter.com/intent/tweet?url={{ url()->current() }}" target="_blank" class="btn btn-sm btn-outline-info rounded-circle" style="width: 36px; height: 36px; padding-top: 6px;" title="Chia sẻ lên Twitter"><i class="fab fa-twitter"></i></a>
                        </div>
                    </div>

                </div>
            </div>
        </article>

        <!-- CỘT PHẢI: SIDEBAR (col-lg-4) -->
        <aside class="col-lg-4 pl-lg-4">
            
            <!-- Box Tìm Kiếm -->
            <div class="card border-0 shadow-sm rounded mb-4">
                <div class="card-header text-white font-weight-bold serif-font rounded-top" style="background-color: #2C3E50;">
                    <i class="fas fa-search mr-2"></i> TÌM KIẾM BÀI VIẾT
                </div>
                <div class="card-body bg-light p-4">
                    <form action="#" method="GET">
                        <div class="input-group">
                            <input type="text" name="q" class="form-control border-0 shadow-none px-3" placeholder="Nhập từ khóa..." style="border-radius: 4px 0 0 4px;">
                            <div class="input-group-append">
                                <button class="btn text-white px-3" type="submit" style="background-color: var(--primary-color); border-radius: 0 4px 4px 0;">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Box Tin Bài Liên Quan (Đã xử lý sticky-top) -->
            <div class="card border-0 shadow-sm rounded mb-4 sticky-top" style="top: 100px; z-index: 1;">
                <div class="card-header text-white font-weight-bold serif-font rounded-top" style="background-color: #2C3E50;">
                    <i class="fas fa-newspaper mr-2"></i> TIN BÀI LIÊN QUAN
                </div>
                <div class="card-body bg-white p-3">
                    {{-- Ví dụ lặp $relatedPosts từ DB --}}
                    @for($i = 6; $i <= 10; $i++)
                    <div class="row align-items-center mb-3 pb-3 border-bottom mx-0 last-no-border">
                        <div class="col-4 px-1">
                            <a href="#">
                                <img src="https://picsum.photos/100/100?random={{ $i }}" class="img-fluid rounded w-100 object-fit-cover shadow-sm" style="height: 75px;" alt="News">
                            </a>
                        </div>
                        <div class="col-8 px-2">
                            <h6 class="mb-1">
                                <a href="#" class="text-dark text-decoration-none hover-primary font-weight-bold text-truncate-2" style="font-size: 14px; line-height: 1.4;">
                                    Danh sách những cuốn sách chữa lành tâm hồn ngày mưa
                                </a>
                            </h6>
                            <small class="text-muted"><i class="far fa-clock me-1 mr-1"></i> 15/07/2026</small>
                        </div>
                    </div>
                    @endfor
                </div>
            </div>

        </aside>
    </div>
</div>

<style>
    /* Tuỳ chỉnh Hover và Transition cho Sidebar */
    .hover-primary {
        transition: color 0.2s ease-in-out;
    }
    .hover-primary:hover {
        color: var(--primary-color, #D35400) !important;
    }
    
    .object-fit-cover {
        object-fit: cover;
    }
    
    /* Cắt chữ thành dấu 3 chấm */
    .text-truncate-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    /* Bỏ gạch chân list cuối cùng ở Sidebar */
    .last-no-border:last-child {
        border-bottom: none !important;
        margin-bottom: 0 !important;
        padding-bottom: 0 !important;
    }

    /* CSS Định dạng nội dung bài viết do Trình soạn thảo sinh ra */
    .post-content img {
        max-width: 100%;
        height: auto !important;
        border-radius: 8px;
        margin: 15px 0;
    }
    .post-content h2, 
    .post-content h3, 
    .post-content h4 {
        font-family: serif;
        font-weight: bold;
        color: #2C3E50;
        margin-top: 30px;
        margin-bottom: 15px;
    }
    .post-content p {
        margin-bottom: 18px;
        text-align: justify;
    }
    .post-content ul, 
    .post-content ol {
        margin-bottom: 20px;
        padding-left: 20px;
    }
    .post-content li {
        margin-bottom: 10px;
    }
    .post-content iframe {
        max-width: 100%;
        border-radius: 8px;
    }
    .post-content a {
        color: var(--primary-color);
        text-decoration: none;
    }
    .post-content a:hover {
        text-decoration: underline;
    }
</style>
@endsection