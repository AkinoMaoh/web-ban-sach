@extends('admin.layout')

@section('admin_content')

<div class="container-fluid">

    <!-- Tiêu đề trang -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Quản lý liên hệ</h1>
        <!-- Quản lý liên hệ không cần nút "Thêm mới" nên ta bỏ đi -->
    </div>

    <div class="card shadow mb-4 border-0 rounded-lg">

        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-envelope-open-text mr-2"></i> Danh sách tin nhắn
            </h6>
            <!-- Form tìm kiếm (tìm theo tên/email khách hàng) -->
            <form method="GET" action="{{ route('admin.contact.index') }}" class="form-inline">
                <div class="position-relative search-box">
                    <input type="text" id="admin-search" name="keyword" class="form-control" 
                           placeholder="Tên, email khách hàng..." autocomplete="off" 
                           value="{{ request('keyword') }}">
                </div>
                <button class="btn btn-primary ml-2">
                    <i class="fas fa-search"></i>
                </button>
            </form>
        </div>

        <div class="card-body px-0 pb-0">

            <!-- Thông báo lỗi / thành công -->
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show mx-4" role="alert">
                    <i class="fas fa-exclamation-triangle mr-1"></i> {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                </div>
            @endif

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show mx-4" role="alert">
                    <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" width="100%" cellspacing="0">
                    <thead class="bg-light text-uppercase font-weight-bold text-dark" style="font-size: 0.85rem;">
                        <tr>
                            <th class="py-3 pl-4" width="5%">ID</th>
                            <th class="py-3" width="20%">Khách hàng</th>
                            <th class="py-3" width="30%">Tiêu đề</th>
                            <th class="py-3" width="15%">Ngày gửi</th>
                            <th class="py-3 text-center" width="15%">Trạng thái</th>
                            <th class="py-3 text-center pr-4" width="15%">Hành động</th>
                        </tr>
                    </thead>
                    
                    <tbody>
                        @forelse ($contacts as $contact)
                            <!-- Click vào dòng là vào trang xem chi tiết -->
                            <tr class="clickable-row" data-href="{{ route('admin.contact.show', $contact->id) }}" style="cursor: pointer;" title="Nhấn để xem chi tiết">
                                
                                <td class="pl-4 font-weight-bold text-primary">#{{ $contact->id }}</td>
                                
                                <td>
                                    <span class="font-weight-bold text-dark d-block">{{ $contact->name }}</span>
                                    <small class="text-muted">{{ $contact->email }}</small>
                                </td>
                                
                                <td>
                                    <span class="text-dark">{{ Str::limit($contact->subject, 40) }}</span>
                                </td>

                                <td>
                                    {{ $contact->created_at->format('d/m/Y H:i') }}
                                </td>

                                <td class="text-center">
                                    @if($contact->status == 0)
                                        <span class="badge badge-warning p-2"><i class="fas fa-clock mr-1"></i> Chưa đọc</span>
                                    @else
                                        <span class="badge badge-success p-2"><i class="fas fa-check-double mr-1"></i> Đã xử lý</span>
                                    @endif
                                </td>
                                
                                <td class="text-center pr-4">
                                    <div class="d-inline-flex align-items-center justify-content-center">
                                        <!-- Nút Đổi trạng thái ngay ngoài danh sách -->
                                        <form action="{{ route('admin.contact.status', $contact->id) }}" method="GET" class="d-inline mr-1" onsubmit="event.stopPropagation();">
                                            <button type="submit" 
                                                class="btn btn-sm {{ $contact->status == 0 ? 'btn-success' : 'btn-secondary' }}" 
                                                title="{{ $contact->status == 0 ? 'Đánh dấu đã xử lý' : 'Đánh dấu chưa xử lý' }}">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>

                                        <!-- Nút Xóa -->
                                        <a href="{{ route('admin.contact.destroy', $contact->id) }}" 
                                           class="btn btn-sm btn-danger text-white" 
                                           onclick="event.stopPropagation(); return confirm('Bạn có chắc chắn muốn xóa liên hệ này?')" 
                                           title="Xóa">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">Không tìm thấy tin nhắn liên hệ nào.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Phân trang -->
            <div class="d-flex justify-content-center mt-4 pb-3">
                {{ $contacts->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>

        </div>
    </div>
</div>

<!-- JS xử lý sự kiện click vào dòng -->
@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function() {
    const rows = document.querySelectorAll(".clickable-row");
    rows.forEach(row => {
        row.addEventListener("click", function(e) {
            if (e.target.closest('a') || e.target.closest('button') || e.target.closest('form')) {
                return;
            }
            window.location = this.dataset.href;
        });
    });
});
</script>
@endpush

@endsection