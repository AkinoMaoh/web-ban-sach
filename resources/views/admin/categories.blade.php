@extends('admin.layout')

@section('admin_content')

<div class="container-fluid">

    <!-- Tiêu đề trang & Nút Thêm -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Trang quản lý danh mục</h1>
        <a href="{{ route('admin.categoryAdd') }}" class="btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50 mr-1"></i> Thêm danh mục mới
        </a>
    </div>

    <div class="card shadow mb-4 border-0 rounded-lg">

        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list mr-2"></i> Dữ liệu danh mục
            </h6>

            <form method="GET" action="{{ route('admin.categories') }}" class="form-inline">

                <div class="position-relative search-box">

                    <input
                        type="text"
                        id="admin-search"
                        name="keyword"
                        class="form-control"
                        placeholder="Nhập tên danh mục..."
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
                <table class="table table-hover align-middle mb-0" id="dataTable" width="100%" cellspacing="0">
                    <thead class="bg-light text-uppercase font-weight-bold text-dark" style="font-size: 0.85rem;">
                        <tr>
                            <th class="py-3 pl-4" width="6%">ID</th>
                            <th class="py-3" width="14%">Ảnh</th> <!-- Đưa cột Ảnh lên đầu và tăng chiều rộng -->
                            <th class="py-3" width="28%">Tên danh mục</th>
                            <th class="py-3 text-center" width="20%">Số lượng sản phẩm</th>
                            <th class="py-3 text-center pr-4" width="32%">Hành động</th>
                        </tr>
                    </thead>
                    
                    <tbody>
                        @forelse ($categories as $category)
                            <!-- Click vào dòng là nhảy thẳng vào trang Sửa -->
                            <tr class="clickable-row" data-href="{{ route('admin.categories.edit', $category->id) }}" style="cursor: pointer;" title="Nhấn để sửa">
                                
                                <td class="pl-4 font-weight-bold text-primary">#{{ $category->id }}</td>
                                
                                <!-- Ảnh danh mục kích thước lớn hơn ở cột đầu tiên -->
                                <td>
                                    @if($category->image)
                                        <img src="{{ asset('uploads/categories/' . $category->image) }}"
                                             alt="Ảnh danh mục"
                                             class="rounded shadow-sm border" 
                                             width="75" height="75" 
                                             style="object-fit: cover;">
                                    @else
                                        <span class="text-muted small font-italic">Không có ảnh</span>
                                    @endif
                                </td>

                                <td>
                                    <span class="font-weight-bold text-dark">{{ $category->name }}</span>
                                </td>
                                
                                <td class="text-center">
                                    <span class="badge badge-light border px-2 py-1 font-weight-bold">
                                        {{ $category->products_count }}
                                    </span>
                                </td>
                                
                                <td class="text-center pr-4">
                                    <div class="d-inline-flex align-items-center justify-content-center">
                                        <!-- Sửa -->
                                        <a href="{{ route('admin.categories.edit', $category->id) }}"
                                           class="btn btn-sm btn-success text-white mr-1" title="Sửa">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <!-- Ẩn / Hiện (Toggle Status) -->
                                        <form action="{{ route('admin.categories.toggleStatus', $category->id) }}" method="POST" class="d-inline mr-1" onsubmit="event.stopPropagation();">
                                            @csrf
                                            <button type="submit"
                                                class="btn btn-sm {{ $category->status ? 'btn-warning text-white' : 'btn-secondary' }}"
                                                title="{{ $category->status ? 'Ẩn' : 'Hiện' }}">
                                                <i class="fas {{ $category->status ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                                            </button>
                                        </form>

                                        <!-- Xóa -->
                                        <a href="{{ route('admin.categories.destroy', $category->id) }}"
                                           class="btn btn-sm btn-danger text-white"
                                           onclick="event.stopPropagation(); return confirm('Bạn có chắc chắn muốn xóa danh mục này?');"
                                           title="Xóa">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">Không tìm thấy danh mục nào.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Phân trang -->
            <div class="d-flex justify-content-center mt-4 pb-3">
                {{ $categories->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>

        </div>
    </div>

</div>

<!-- JS xử lý sự kiện click vào dòng -->
@push('scripts')
<script>
const searchUrl = "{{ route('admin.categories.search') }}";
const searchField = "name";
    
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

<script src="{{ asset('js/admin-search.js') }}"></script>
@endpush

@endsection