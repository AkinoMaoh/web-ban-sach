@extends('admin.layout')

@section('admin_content')

<div class="container-fluid">

    <!-- Tiêu đề trang & Nút Thêm -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Quản lý tin tức</h1>
        <a href="{{ route('admin.news.create') }}" class="btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50 mr-1"></i> Thêm tin tức mới
        </a>
    </div>

    <div class="card shadow mb-4 border-0 rounded-lg">

        <div class="card-header py-3 bg-white">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-newspaper mr-2"></i> Dữ liệu tin tức
            </h6>
        </div>

        <div class="card-body px-0 pb-0">

            <!-- Thông báo thành công / lỗi -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show mx-4" role="alert">
                    <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show mx-4" role="alert">
                    <i class="fas fa-exclamation-triangle mr-1"></i> {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="dataTable" width="100%" cellspacing="0">
                    <thead class="bg-light text-uppercase font-weight-bold text-dark" style="font-size: 0.85rem;">
                        <tr>
                            <th class="py-3 pl-4" width="6%">ID</th>
                            <th class="py-3" width="14%">Ảnh</th> <!-- Đưa cột Ảnh lên đầu và tăng chiều rộng -->
                            <th class="py-3" width="24%">Tiêu đề</th>
                            <th class="py-3" width="28%">Mô tả</th>
                            <th class="py-3 text-center" width="12%">Trạng thái</th>
                            <th class="py-3 text-center pr-4" width="16%">Hành động</th>
                        </tr>
                    </thead>
                    
                    <tbody>
                        @forelse($news as $item)
                            <!-- Click vào dòng là nhảy thẳng vào trang Sửa -->
                            <tr class="clickable-row" data-href="{{ route('admin.news.edit', $item->id) }}" style="cursor: pointer;" title="Nhấn để sửa">
                                
                                <td class="pl-4 font-weight-bold text-primary">#{{ $item->id }}</td>
                                
                                <!-- Ảnh tin tức kích thước lớn ở cột đầu tiên -->
                                <td>
                                    @if($item->image)
                                        <img src="{{ asset('uploads/news/' . $item->image) }}" 
                                             alt="Ảnh tin tức" 
                                             class="rounded shadow-sm border" 
                                             width="75" height="75" 
                                             style="object-fit: cover;">
                                    @else
                                        <span class="text-muted small font-italic">Không có ảnh</span>
                                    @endif
                                </td>

                                <td>
                                    <span class="font-weight-bold text-dark">{{ $item->title }}</span>
                                </td>
                                
                                <td class="text-muted small">
                                    {{ Str::limit($item->summary, 60) }}
                                </td>
                                
                                <td class="text-center">
                                    @if($item->status == 1)
                                        <span class="badge badge-success px-2 py-1">Hiển thị</span>
                                    @else
                                        <span class="badge badge-danger px-2 py-1">Ẩn</span>
                                    @endif
                                </td>
                                
                                <td class="text-center pr-4">
                                    <div class="d-inline-flex align-items-center justify-content-center">
                                        <!-- Chi tiết -->
                                        <a href="{{ route('admin.news.show', $item->id) }}"
                                           class="btn btn-sm btn-info text-white mr-1"
                                           title="Chi tiết">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        <!-- Sửa -->
                                        <a href="{{ route('admin.news.edit', $item->id) }}"
                                           class="btn btn-sm btn-success text-white mr-1" 
                                           title="Sửa">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <!-- Xóa -->
                                        <form action="{{ route('admin.news.destroy', $item->id) }}"
                                              method="POST"
                                              class="d-inline"
                                              onsubmit="event.stopPropagation(); return confirm('Bạn có chắc chắn muốn xóa tin tức này?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="btn btn-sm btn-danger text-white"
                                                    title="Xóa">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">Không tìm thấy tin tức nào.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Phân trang -->
            <div class="d-flex justify-content-center mt-4 pb-3">
                {{ $news->appends(request()->query())->links('pagination::bootstrap-5') }}
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