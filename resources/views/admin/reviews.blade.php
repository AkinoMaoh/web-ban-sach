@extends('admin.layout')

@section('admin_content')

<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Trang quản lý đánh giá & bình luận</h1>
    </div>
 
    <!-- Hiển thị thông báo thành công/lỗi -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mx-0" role="alert">
            <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card shadow mb-4 border-0 rounded-lg">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-comments mr-2"></i> Dữ liệu đánh giá từ khách hàng
            </h6>
            <form method="GET" action="{{ route('admin.reviews.index') }}" class="form-inline">

                <div class="position-relative search-box">

                    <input
                        type="text"
                        id="admin-search"
                        name="keyword"
                        class="form-control"
                        placeholder="Nhập tên khách hàng..."
                        autocomplete="off"
                        value="{{ request('keyword') }}">

                    <div id="search-order-result"></div>

                </div>

                <button class="btn btn-primary ml-2">
                    <i class="fas fa-search"></i>
                </button>

            </form>
        </div>
        
        <div class="card-body px-0 pb-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="dataTable" width="100%" cellspacing="0">
                    <thead class="bg-light text-uppercase font-weight-bold text-dark" style="font-size: 0.82rem;">
                        <tr>
                            <th class="py-3 pl-4" width="5%">ID</th>
                            <th class="py-3" width="22%">Sản phẩm</th>
                            <th class="py-3" width="15%">Khách hàng</th>
                            <th class="py-3" width="12%">Đánh giá</th>
                            <th class="py-3" width="22%">Nội dung</th>
                            <th class="py-3 text-center" width="12%">Trạng thái</th>
                            <th class="py-3 text-center pr-4" width="12%">Hành động</th>
                        </tr>
                    </thead>
                    
                    <tbody>
                        @forelse ($reviews as $review)
                        <!-- Click vào dòng (ngoại trừ link sản phẩm hoặc nút) là bật popup trả lời -->
                        <tr class="clickable-row" data-target="#replyModal-{{ $review->id }}" style="cursor: pointer;" title="Nhấn vào dòng để trả lời đánh giá">
                            
                            <td class="pl-4 font-weight-bold text-primary">#{{ $review->id }}</td>
                            
                            <td>
                                <a href="{{ route('user.productDetails', $review->product_id) }}" target="_blank" class="font-weight-bold text-dark text-decoration-none">
                                    {{ $review->product->name ?? 'Sản phẩm đã bị xóa' }}
                                </a>
                                <br>
                                <span class="badge badge-light border mt-1 font-weight-normal">Phân loại: {{ $review->variant_name ?? 'Mặc định' }}</span>
                            </td>
                            
                            <td>
                                <strong class="text-dark">{{ $review->user_name }}</strong>
                                <br>
                                <small class="text-muted">{{ $review->created_at->format('d/m/Y H:i') }}</small>
                            </td>
                            
                            <td>
                                <div class="text-warning">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="{{ $i <= $review->rating ? 'fas' : 'far' }} fa-star"></i>
                                    @endfor
                                </div>
                            </td>
                            
                            <td class="text-muted small">
                                {{ $review->comment }}
                            </td>
                            
                            <td class="text-center">
                                @if($review->admin_reply)
                                    <span class="badge badge-success px-2 py-1">Đã phản hồi</span>
                                @else
                                    <span class="badge badge-warning px-2 py-1 text-dark">Chờ phản hồi</span>
                                @endif
                            </td>
                            
                            <td class="text-center pr-4"> 
                                <div class="d-inline-flex align-items-center justify-content-center">
                                    <!-- Nút Xóa -->
                                    <form action="{{ route('admin.reviews.destroy', $review->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa đánh giá này?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger text-white" title="Xóa">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        <!-- Modal Trả lời Đánh giá cho từng item -->
                        <div class="modal fade" id="replyModal-{{ $review->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <form action="{{ route('admin.reviews.reply', $review->id) }}" method="POST" class="w-100">
                                    @csrf
                                    <div class="modal-content border-0 shadow-lg rounded-lg">
                                        <div class="modal-header bg-white py-3">
                                            <h5 class="modal-title font-weight-bold text-primary">Phản hồi khách hàng: {{ $review->user_name }}</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body bg-light">
                                            <div class="alert alert-white border mb-3 shadow-sm">
                                                <strong class="text-dark">Khách nhận xét:</strong> <br>
                                                <i class="text-muted">"{{ $review->comment }}"</i>
                                            </div>
                                            <div class="form-group mb-0">
                                                <label for="admin_reply_{{ $review->id }}" class="font-weight-bold text-dark">Nội dung trả lời từ Shop:</label>
                                                <textarea id="admin_reply_{{ $review->id }}" name="admin_reply" class="form-control" rows="4" required placeholder="Nhập phản hồi của bạn...">{{ $review->admin_reply }}</textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer bg-white py-3">
                                            <button type="button" class="btn btn-secondary px-4" data-dismiss="modal">Đóng</button>
                                            <button type="submit" class="btn btn-primary px-4 font-weight-bold shadow-sm">Lưu phản hồi</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">Chưa có đánh giá nào từ khách hàng.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Phân trang -->
            <div class="d-flex justify-content-center mt-4 pb-3">
                {{ $reviews->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>

        </div>
    </div>

</div>
<!-- /.container-fluid -->

<!-- Script xử lý click vào dòng để mở Modal trả lời -->
@push('scripts')
<script>
const searchUrl = "{{ route('admin.reviews.search') }}";
const searchField = "user_name";

document.addEventListener("DOMContentLoaded", function() {
    const rows = document.querySelectorAll(".clickable-row");
    rows.forEach(row => {
        row.addEventListener("click", function(e) {
            // Nếu người dùng click vào link xem chi tiết sản phẩm, nút bấm hoặc form xóa bên trong thì không bật Modal
            if (e.target.closest('a') || e.target.closest('button') || e.target.closest('form')) {
                return;
            }
            const targetModal = this.getAttribute("data-target");
            $(targetModal).modal('show');
        });
    });
});
</script>

<script src="{{ asset('js/admin-search.js') }}"></script>
@endpush

@endsection