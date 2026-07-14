@extends('admin.layout')

@section('admin_content')

<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800">Trang quản lý đánh giá & bình luận</h1>
  
    <!-- Hiển thị thông báo thành công/lỗi -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Dữ liệu đánh giá từ khách hàng</h6>
        </div>
        
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th width="5%">ID</th>
                            <th width="20%">Sản phẩm</th>
                            <th width="15%">Khách hàng</th>
                            <th width="12%">Đánh giá</th>
                            <th width="23%">Nội dung</th>
                            <th width="15%">Trạng thái</th>
                            <th width="10%">Hành động</th>
                        </tr>
                    </thead>
                    
                    <tbody>
                        @forelse ($reviews as $review)
                        <tr>
                            <td>{{ $review->id }}</td>
                            <td>
                                <a href="{{ route('user.productDetails', $review->product_id) }}" target="_blank" class="font-weight-bold text-decoration-none">
                                    {{ $review->product->name ?? 'Sản phẩm đã bị xóa' }}
                                </a>
                                <br>
                                <small class="text-muted">Phân loại: {{ $review->variant_name ?? 'Mặc định' }}</small>
                            </td>
                            <td>
                                <strong>{{ $review->user_name }}</strong>
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
                            <td>{{ $review->comment }}</td>
                            <td>
                                @if($review->admin_reply)
                                    <span class="badge badge-success px-2 py-1">Đã phản hồi</span>
                                @else
                                    <span class="badge badge-warning px-2 py-1 text-dark">Chờ phản hồi</span>
                                @endif
                            </td>
                            <td> 
                                <!-- Nút Trả lời -->
                                <button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#replyModal-{{ $review->id }}" title="Trả lời">
                                    <i class="fas fa-reply"></i>
                                </button>

                                <!-- Nút Xóa -->
                                <form action="{{ route('admin.reviews.destroy', $review->id) }}" method="POST" style="display:inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Xóa" onclick="return confirm('Bạn có chắc chắn muốn xóa đánh giá này?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>

                        <!-- Modal Trả lời Đánh giá cho từng item -->
                        <div class="modal fade" id="replyModal-{{ $review->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <form action="{{ route('admin.reviews.reply', $review->id) }}" method="POST" class="w-100">
                                    @csrf
                                    <div class="modal-content">
                                        <div class="modal-header bg-light">
                                            <h5 class="modal-title font-weight-bold text-primary">Phản hồi khách hàng: {{ $review->user_name }}</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="alert alert-secondary mb-3">
                                                <strong>Khách nhận xét:</strong> <br>
                                                <i>"{{ $review->comment }}"</i>
                                            </div>
                                            <div class="form-group">
                                                <label for="admin_reply_{{ $review->id }}" class="font-weight-bold">Nội dung trả lời từ Shop:</label>
                                                <textarea id="admin_reply_{{ $review->id }}" name="admin_reply" class="form-control" rows="4" required placeholder="Nhập phản hồi của bạn...">{{ $review->admin_reply }}</textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                                            <button type="submit" class="btn btn-primary">Lưu phản hồi</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">Chưa có đánh giá nào từ khách hàng.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Phân trang -->
    <div class="d-flex justify-content-center mt-3">
        {{ $reviews->appends(request()->query())->links() }}
    </div>

</div>
<!-- /.container-fluid -->

@endsection