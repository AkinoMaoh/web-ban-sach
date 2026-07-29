@extends('admin.layout')

@section('admin_content')

<div class="container-fluid">

    <!-- Tiêu đề trang & Nút Thêm -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Quản lý Banner</h1>
        <a href="{{ route('admin.banners.create') }}" class="btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50 mr-1"></i> Thêm Banner mới
        </a>
    </div>

    <div class="card shadow mb-4 border-0 rounded-lg">

        <div class="card-header py-3 bg-white">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-images mr-2"></i> Dữ liệu banner
            </h6>
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
                    <thead class="bg-light text-uppercase font-weight-bold text-dark" style="font-size: 0.82rem;">
                        <tr>
                            <th class="py-3 pl-4" width="6%">ID</th>
                            <th class="py-3" width="18%">Ảnh</th> <!-- Đưa cột Ảnh lên đầu và tăng độ rộng -->
                            <th class="py-3" width="20%">Tiêu đề</th>
                            <th class="py-3" width="12%">Vị trí</th>
                            <th class="py-3 text-center" width="8%">Thứ tự</th>
                            <th class="py-3 text-center" width="16%">Trạng thái</th>
                            <th class="py-3 text-center pr-4" width="20%">Hành động</th>
                        </tr>
                    </thead>
                    
                    <tbody>
                        @forelse ($banners as $banner)
                            <!-- Click vào dòng là nhảy thẳng vào trang Sửa -->
                            <tr class="clickable-row" data-href="{{ route('admin.banners.edit', $banner->id) }}" style="cursor: pointer;" title="Nhấn để sửa">
                                
                                <td class="pl-4 font-weight-bold text-primary">#{{ $banner->id }}</td>
                                
                                <!-- Ảnh Banner kích thước to hơn ở cột đầu tiên -->
                                <td>
                                    @if($banner->image)
                                        <img src="{{ asset('uploads/banners/' . $banner->image) }}" 
                                             alt="Banner" 
                                             class="rounded shadow-sm border" 
                                             width="110" height="60" 
                                             style="object-fit: cover;">
                                    @else
                                        <span class="text-muted small font-italic">Không có ảnh</span>
                                    @endif
                                </td>

                                <td>
                                    <span class="font-weight-bold text-dark">{{ $banner->title ?? 'Không có tiêu đề' }}</span>
                                </td>
                                
                                <td>
                                    <span class="badge badge-light border px-2 py-1">{{ $banner->position }}</span>
                                </td>
                                
                                <td class="text-center font-weight-bold">{{ $banner->sort_order }}</td>
                                
                                <td class="text-center">
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <!-- Trạng thái Hiển thị / Ẩn -->
                                        <div class="mb-1">
                                            @if($banner->status)
                                                <span class="badge badge-success px-2 py-1">Hiển thị</span>
                                            @else
                                                <span class="badge badge-danger px-2 py-1">Ẩn</span>
                                            @endif
                                        </div>

                                        <!-- Thời hạn Banner -->
                                        @php
                                            $now = now();
                                        @endphp

                                        @if($banner->start_date && $now->lt($banner->start_date))
                                            <span class="text-primary small font-weight-bold" style="font-size: 0.75rem;">
                                                <i class="fas fa-clock"></i> Chưa tới
                                            </span>
                                        @elseif($banner->end_date && $now->gt($banner->end_date))
                                            <span class="text-danger small font-weight-bold" style="font-size: 0.75rem;">
                                                <i class="fas fa-times-circle"></i> Hết hạn
                                            </span>
                                        @else
                                            <span class="text-success small font-weight-bold" style="font-size: 0.75rem;">
                                                <i class="fas fa-check-circle"></i> Đang diễn ra
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                
                                <td class="text-center pr-4">
                                    <div class="d-inline-flex align-items-center justify-content-center">
                                        <!-- Xem chi tiết -->
                                        <a href="{{ route('admin.banners.show', $banner->id) }}" 
                                           class="btn btn-sm btn-info text-white mr-1" title="Chi tiết">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        <!-- Sửa -->
                                        <a href="{{ route('admin.banners.edit', $banner->id) }}" 
                                           class="btn btn-sm btn-success text-white mr-1" title="Sửa">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <!-- Ẩn / Hiện nhanh -->
                                        <form action="{{ route('admin.banners.toggleStatus', $banner->id) }}" method="POST" class="d-inline mr-1" onsubmit="event.stopPropagation();">
                                            @csrf
                                            <button type="submit"
                                                class="btn btn-sm {{ $banner->status ? 'btn-warning text-white' : 'btn-secondary' }}"
                                                title="{{ $banner->status ? 'Ẩn banner' : 'Hiện banner' }}">
                                                <i class="fas {{ $banner->status ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                                            </button>
                                        </form>

                                        <!-- Xóa -->
                                        <form action="{{ route('admin.banners.destroy', $banner->id) }}" 
                                              method="POST" 
                                              class="d-inline" 
                                              onsubmit="event.stopPropagation(); return confirm('Bạn có chắc chắn muốn xóa banner này?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger text-white" title="Xóa">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">Không tìm thấy banner nào.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Phân trang -->
            <div class="d-flex justify-content-center mt-4 pb-3">
                {{ $banners->appends(request()->query())->links('pagination::bootstrap-5') }}
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