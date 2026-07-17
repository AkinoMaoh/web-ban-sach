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
                <li class="breadcrumb-item active" aria-current="page" style="color: var(--primary-color); font-weight: 600;">
                    Tin tức & Bài viết
                </li>
            </ol>
        </nav>
    </div>
</div>

<div class="container mb-5">
    <div class="row">
        <!-- CỘT TRÁI: DANH SÁCH BÀI VIẾT (col-lg-8) -->
        <div class="col-lg-8 mb-5 mb-lg-0">
            <div class="d-flex justify-content-between align-items-end border-bottom pb-2 mb-4">
                <h2 class="serif-font font-weight-bold mb-0" style="color: #2C3E50;">Tin Tức Mới Nhất</h2>
            </div>

            {{-- Nếu có dữ liệu từ Controller ($posts) --}}
            @if(isset($posts) && $posts->count() > 0)
                @foreach($posts as $post)
                <div class="card border-0 shadow-sm rounded mb-4 overflow-hidden news-card bg-white">
                    <div class="row no-gutters g-0">
                        <div class="col-md-5">
                            <a href="#">
                                <img src="{{ asset('uploads/news/' . ($post->image ?? 'default.jpg')) }}" 
                                     class="img-fluid w-100 h-100 object-fit-cover" 
                                     alt="{{ $post->title }}" style="min-height: 220px;">
                            </a>
                        </div>
                        <div class="col-md-7">
                            <div class="card-body p-4 d-flex flex-column h-100 justify-content-center">
                                <div class="mb-2">
                                    <span class="badge px-2 py-1" style="background-color: #FFF6F0; color: #D35400; font-weight: 500;">Tin Sách</span>
                                    <small class="text-muted ms-2 ml-2"><i class="far fa-calendar-alt me-1 mr-1"></i> {{ $post->created_at->format('d/m/Y') }}</small>
                                    <span class="mx-2 text-black-50">|</span>
                                    <small class="text-muted"><i class="far fa-user me-1 mr-1"></i> {{ $post->author->name ?? 'Admin' }}</small>
                                </div>
                                <h4 class="font-weight-bold mb-3 serif-font" style="line-height: 1.4;">
                                    <a href="#" class="text-dark text-decoration-none hover-primary text-truncate-2">
                                        {{ $post->title }}
                                    </a>
                                </h4>
                                <p class="card-text text-muted mb-4 text-truncate-3" style="line-height: 1.6;">
                                    {{ Str::limit($post->excerpt ?? 'Nội dung bài viết đang được cập nhật...', 150) }}
                                </p>
                                <div class="mt-auto">
                                    <a href="#" class="font-weight-bold text-decoration-none hover-primary" style="color: var(--primary-color);">
                                        Đọc tiếp <i class="fas fa-arrow-right ms-1 ml-1" style="font-size: 12px;"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach

                <!-- Phân trang -->
                <div class="d-flex justify-content-center mt-5 custom-pagination">
                    {{ $posts->links('pagination::bootstrap-5') }}
                </div>
            @else
                <!-- DỮ LIỆU ẢO (MOCKUP) -->
                @for($i = 1; $i <= 5; $i++)
                <div class="card border-0 shadow-sm rounded mb-4 overflow-hidden news-card bg-white">
                    <div class="row no-gutters g-0">
                        <div class="col-md-5">
                            <a href="#">
                                <img src="https://picsum.photos/400/300?random={{ $i }}" class="img-fluid w-100 h-100 object-fit-cover" alt="Ảnh bài viết" style="min-height: 230px;">
                            </a>
                        </div>
                        <div class="col-md-7">
                            <div class="card-body p-4 d-flex flex-column h-100 justify-content-center">
                                <div class="mb-2">
                                    <span class="badge px-2 py-1" style="background-color: #FFF6F0; color: #D35400; font-weight: 500;">Review Sách</span>
                                    <small class="text-muted ms-2 ml-2"><i class="far fa-calendar-alt me-1 mr-1"></i> 10/07/2026</small>
                                </div>
                                <h4 class="font-weight-bold mb-3 serif-font" style="line-height: 1.4;">
                                    <a href="#" class="text-dark text-decoration-none hover-primary text-truncate-2">
                                        Top 10 cuốn tiểu thuyết kinh điển bạn nhất định phải đọc một lần trong đời
                                    </a>
                                </h4>
                                <p class="card-text text-muted mb-4 text-truncate-3" style="line-height: 1.6;">
                                    Khám phá những tựa sách đã làm say đắm hàng triệu độc giả trên toàn thế giới với những câu chuyện sâu sắc, đầy ý nghĩa nhân văn và bài học cuộc sống thiết thực...
                                </p>
                                <div class="mt-auto">
                                    <a href="#" class="font-weight-bold text-decoration-none hover-primary" style="color: var(--primary-color);">
                                        Đọc tiếp <i class="fas fa-arrow-right ms-1 ml-1" style="font-size: 12px;"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endfor
                
                <!-- Phân trang ảo -->
                <div class="mt-5 d-flex justify-content-center custom-pagination">
                    <ul class="pagination">
                        <li class="page-item disabled"><a class="page-link" href="#" tabindex="-1">Trước</a></li>
                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                        <li class="page-item"><a class="page-link" href="#">2</a></li>
                        <li class="page-item"><a class="page-link" href="#">3</a></li>
                        <li class="page-item"><a class="page-link" href="#">Sau</a></li>
                    </ul>
                </div>
            @endif
        </div>

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

            <!-- Box Bài Viết Đọc Nhiều Nhất (Đã chỉnh lại top: 100px và z-index: 1) -->
            <div class="card border-0 shadow-sm rounded mb-4 sticky-top" style="top: 100px; z-index: 1;">
                <div class="card-header text-white font-weight-bold serif-font rounded-top" style="background-color: #2C3E50;">
                    <i class="fas fa-fire mr-2" style="color: #D35400;"></i> ĐỌC NHIỀU NHẤT
                </div>
                <div class="card-body bg-white p-3">
                    @for($i = 5; $i <= 9; $i++)
                    <div class="row align-items-center mb-3 pb-3 border-bottom mx-0 last-no-border">
                        <div class="col-4 px-1">
                            <a href="#">
                                <img src="https://picsum.photos/100/100?random={{ $i }}" class="img-fluid rounded w-100 object-fit-cover shadow-sm" style="height: 75px;" alt="News">
                            </a>
                        </div>
                        <div class="col-8 px-2">
                            <h6 class="mb-1">
                                <a href="#" class="text-dark text-decoration-none hover-primary font-weight-bold text-truncate-2" style="font-size: 14px; line-height: 1.4;">
                                    Lợi ích tuyệt vời của thói quen đọc 20 trang sách mỗi ngày
                                </a>
                            </h6>
                            <small class="text-muted"><i class="far fa-clock me-1 mr-1"></i> 05/07/2026</small>
                        </div>
                    </div>
                    @endfor
                </div>
            </div>

        </aside>
    </div>
</div>

<style>
    /* Tuỳ chỉnh Hover và Transition */
    .hover-primary {
        transition: color 0.2s ease-in-out;
    }
    .hover-primary:hover,
    .card:hover h4 a {
        color: var(--primary-color, #D35400) !important;
    }
    
    .news-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .news-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.1) !important;
    }
    
    .object-fit-cover {
        object-fit: cover;
    }
    
    /* Truncate text (Cắt chữ thêm dấu 3 chấm) */
    .text-truncate-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .text-truncate-3 {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    /* Bỏ gạch chân của item cuối cùng trong list sidebar */
    .last-no-border:last-child {
        border-bottom: none !important;
        margin-bottom: 0 !important;
        padding-bottom: 0 !important;
    }

    /* Phân trang (Pagination) đồng bộ phong cách */
    .custom-pagination .page-item.active .page-link {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
        color: white;
    }
    .custom-pagination .page-link {
        color: #2C3E50;
    }
</style>
@endsection