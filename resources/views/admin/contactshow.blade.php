@extends('admin.layout')

@section('admin_content')

<div class="container-fluid">

    <!-- Tiêu đề & Nút quay lại -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Chi tiết liên hệ #{{ $contact->id }}</h1>
        <a href="{{ route('admin.contact.index') }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50 mr-1"></i> Quay lại danh sách
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Khung hiển thị nội dung -->
            <div class="card shadow mb-4 border-0 rounded-lg">
                <div class="card-header py-3 bg-white d-flex justify-content-between align-items-center border-bottom">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-info-circle mr-2"></i> Nội dung tin nhắn
                    </h6>
                    <div>
                        @if($contact->status == 0)
                            <span class="badge badge-warning p-2"><i class="fas fa-clock mr-1"></i> Trạng thái: Chưa đọc</span>
                        @else
                            <span class="badge badge-success p-2"><i class="fas fa-check-double mr-1"></i> Trạng thái: Đã xử lý</span>
                        @endif
                    </div>
                </div>
                
                <div class="card-body p-4">
                    <div class="mb-4">
                        <label class="text-muted font-weight-bold text-uppercase small mb-1">Tiêu đề</label>
                        <h4 class="text-dark font-weight-bold">{{ $contact->subject }}</h4>
                    </div>

                    <div class="p-4 bg-light rounded" style="font-size: 16px; line-height: 1.6; color: #333;">
                        {!! nl2br(e($contact->message)) !!}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Khung thông tin khách hàng & Hành động -->
            <div class="card shadow mb-4 border-0 rounded-lg">
                <div class="card-header py-3 bg-white border-bottom">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-user mr-2"></i> Thông tin khách hàng
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush mb-4">
                        <li class="list-group-item px-0 border-bottom-0 pb-2">
                            <span class="text-muted d-block small">Họ và tên</span>
                            <span class="font-weight-bold text-dark">{{ $contact->name }}</span>
                        </li>
                        <li class="list-group-item px-0 border-bottom-0 pb-2">
                            <span class="text-muted d-block small">Email</span>
                            <a href="mailto:{{ $contact->email }}" class="font-weight-bold text-primary">{{ $contact->email }}</a>
                        </li>
                        <li class="list-group-item px-0 pb-0">
                            <span class="text-muted d-block small">Ngày gửi</span>
                            <span class="text-dark">{{ $contact->created_at->format('d/m/Y - H:i:s') }}</span>
                        </li>
                    </ul>
                    
                    <hr>

                    <!-- Các nút hành động Cập nhật / Xóa -->
                    <div class="d-flex flex-column gap-2 mt-3">
                        <a href="{{ route('admin.contact.status', $contact->id) }}" class="btn {{ $contact->status == 0 ? 'btn-success' : 'btn-outline-secondary' }} btn-block mb-2 font-weight-bold">
                            <i class="fas {{ $contact->status == 0 ? 'fa-check-circle' : 'fa-undo' }} mr-1"></i> 
                            {{ $contact->status == 0 ? 'Đánh dấu Đã xử lý' : 'Đánh dấu Chưa đọc' }}
                        </a>
                        
                        <a href="{{ route('admin.contact.destroy', $contact->id) }}" class="btn btn-outline-danger btn-block font-weight-bold" onclick="return confirm('Bạn có chắc chắn muốn xóa liên hệ này?')">
                            <i class="fas fa-trash-alt mr-1"></i> Xóa tin nhắn
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

@endsection