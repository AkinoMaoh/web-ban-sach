@extends('layout.user')

@section('content')
<!-- Breadcrumb -->
<div class="bg-light py-3 mb-4 border-bottom">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent px-0 mb-0 py-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('user.index') }}" class="text-muted text-decoration-none"><i class="fas fa-home"></i> Trang chủ</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('user.news') }}" class="text-muted text-decoration-none">
                        Tin tức
                    </a>
                </li>
                <li class="breadcrumb-item active text-truncate" aria-current="page" style="color: var(--primary-color); font-weight: 600; max-width: 50%;">
                    {{ $post->title ?? 'Lợi ích tuyệt vời của thói quen đọc 20 trang sách mỗi ngày' }}
                </li>
            </ol>
        </nav>
    </div>
</div>

<div class="container mb-5">
    <div class="row">
        <!-- CỘT TRÁI: NỘI DUNG BÀI VIẾT (col-lg-8) -->
        <div class="col-lg-8 mb-5 mb-lg-0">
            <div class="card border-0 shadow-sm rounded bg-white">
                <div class="card-body p-4 p-md-5"> <!-- Tăng padding để đọc thoải mái hơn -->
                    
                    <!-- Header Bài Viết -->
                    <div class="mb-4">
                        
                        <h1 class="article-title">
                            {{ $post->title }}
                        </h1>
                        
                        <div class="article-date">
                            <i class="far fa-calendar-alt"></i>
                            {{ $post->created_at->format('d/m/Y') }}
                        </div>
                    </div>

                    <!-- Ảnh đại diện bài viết -->
                    <div class="mb-5 text-center">
                        <img
                            src="{{ asset('uploads/news/' . $post->image) }}"
                            class="article-image"
                            alt="{{ $post->title }}">
                    </div>

                    <!-- Nội dung chính (Article Content) -->
                    <div class="article-content text-justify" style="color: #444; font-size: 17px; line-height: 1.8;">
                        {!! $post->content !!}
                    </div>

                </div>
            </div>
        </div>

        <!-- CỘT PHẢI: SIDEBAR (col-lg-4) -->
        <aside class="col-lg-4 pl-lg-4">
            

        </aside>
    </div>
</div>

<style>
   /* ===========================
   NEWS DETAIL
=========================== */

.article-title{
    color: inherit;
}

.article-date{
    color:#6c757d;
    font-size:15px;
    margin-bottom:30px;
}

.article-image{
    width:100%;
    max-height:500px;
    object-fit:cover;
    border-radius:10px;
    display:block;
    margin:0 auto 35px;
    box-shadow:0 4px 12px rgba(0,0,0,.08);
}

/* ===========================
   Nội dung bài viết
=========================== */

.article-content{
    color: inherit;
}

.article-content *{
    color:inherit;
}

/* Heading */

.article-content h1,
.article-content h2,
.article-content h3,
.article-content h4,
.article-content h5,
.article-content h6{
    color: inherit !important;
}

/* Paragraph */

.article-content p{
    margin-bottom:18px;
}

/* Image */

.article-content img{
    display:block;
    max-width:100%;
    height:auto;
    margin:30px auto;
    border-radius:8px;
}

/* List */

.article-content ul,
.article-content ol{
    padding-left:25px;
    margin-bottom:20px;
}

.article-content li{
    margin-bottom:10px;
}

/* Table */

.article-content table{
    width:100%;
    border-collapse:collapse;
    margin:25px 0;
}

.article-content table th,
.article-content table td{
    border:1px solid #dee2e6;
    padding:10px;
}

.article-content table th{
    background:#f8f9fa;
}

/* Quote */

.article-content blockquote{
    margin:25px 0;
    padding:15px 20px;
    border-left:4px solid #D35400;
    background:#f8f9fa;
    color:#666;
    font-style:italic;
}

/* Link */

.article-content a{
    color:#D35400;
    font-weight:600;
    text-decoration:none;
}

.article-content a:hover{
    text-decoration:underline;
}

/* Code */

.article-content pre{
    background:#f5f5f5;
    padding:15px;
    border-radius:8px;
    overflow:auto;
}

.article-content code{
    background:#f5f5f5;
    padding:2px 5px;
    border-radius:4px;
}

/* Responsive */

@media(max-width:768px){

    .article-title{
        font-size:28px;
    }

    .article-content{
        font-size:16px;
    }

    .article-image{
        max-height:320px;
    }

}
</style>
@endsection