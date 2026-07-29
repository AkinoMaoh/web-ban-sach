@extends('admin.layout')

@section('admin_content')

<div class="container-fluid">

    <!-- Tiêu đề trang & Nút Thêm -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Trang quản lý sản phẩm</h1>
        <a href="{{ route('admin.productAdd') }}" class="btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50 mr-1"></i> Thêm sản phẩm mới
        </a>
    </div>

    <div class="card shadow mb-4 border-0 rounded-lg">

        <div class="card-header py-3 bg-white d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-box-open mr-2"></i> Dữ liệu sản phẩm
            </h6>

            <!-- Form lọc theo danh mục đặt ngay trên header cho gọn gàng -->
            <form method="GET" action="{{ route('admin.products') }}" class="form-inline">
                <select name="category_id" class="form-control form-control-sm border-secondary shadow-sm" onchange="this.form.submit()">
                    <option value="">-- Tất cả danh mục --</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>

        <div class="card-body px-0 pb-0">

            <!-- Thông báo thành công / lỗi nếu có -->
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
                    <thead class="bg-light text-uppercase font-weight-bold text-dark" style="font-size: 0.82rem;">
                        <tr>
                            <th class="py-3 pl-4" width="6%">ID</th>
                            <th class="py-3" width="14%">Ảnh</th> <!-- Đã ở cột đầu tiên -->
                            <th class="py-3" width="22%">Tên sách</th>
                            <th class="py-3" width="14%">Danh mục</th>
                            <th class="py-3" width="16%">Giá</th>
                            <th class="py-3 text-center" width="10%">Số lượng</th>
                            <th class="py-3 text-center pr-4" width="18%">Hành động</th>
                        </tr>
                    </thead>
                    
                    <tbody>
                        @forelse ($products as $product)
                            <!-- Click vào dòng là nhảy thẳng vào trang Sửa -->
                            <tr class="clickable-row" data-href="{{ route('admin.products.edit', $product->id) }}" style="cursor: pointer;" title="Nhấn để sửa">
                                
                                <td class="pl-4 font-weight-bold text-primary">#{{ $product->id }}</td>
                                
                                <!-- Ảnh sản phẩm kích thước lớn hơn ở cột đầu tiên -->
                                <td>
                                    @if($product->image)
                                        <img src="{{ asset('uploads/products/' . $product->image) }}" 
                                             alt="Ảnh sản phẩm" 
                                             class="rounded shadow-sm border" 
                                             width="75" height="75" 
                                             style="object-fit: cover;">
                                    @else
                                        <span class="text-muted small font-italic">Không có ảnh</span>
                                    @endif
                                </td>

                                <td>
                                    <span class="font-weight-bold text-dark">{{ $product->name }}</span>
                                </td>
                                
                                <td>
                                    <span class="badge badge-light border px-2 py-1">{{ $product->category->name ?? 'N/A' }}</span>
                                </td>
                                
                                <td>
                                    @php
                                        $variant = $product->firstVariant;
                                    @endphp

                                    @if($variant)
                                        @if($variant->sale_price > 0 && $variant->sale_price < $variant->price)
                                            <div class="d-flex flex-column">
                                                <span class="text-danger font-weight-bold" style="font-size: 14px;">
                                                    {{ number_format($variant->sale_price, 0, ',', '.') }} đ
                                                </span>
                                                <span class="text-muted small" style="text-decoration: line-through; font-size: 11px;">
                                                    {{ number_format($variant->price, 0, ',', '.') }} đ
                                                </span>
                                            </div>
                                        @else
                                            <span style="color:#D35400; font-size: 14px; font-weight: 700;">
                                                {{ number_format($variant->price, 0, ',', '.') }} đ
                                            </span>
                                        @endif
                                    @else
                                        <span class="text-muted small">Chưa có giá</span>
                                    @endif
                                </td>
                                
                                <td class="text-center font-weight-bold">
                                    <span class="badge {{ $product->variants->sum('stock') > 0 ? 'badge-info' : 'badge-danger' }} px-2 py-1">
                                        {{ $product->variants->sum('stock') }}
                                    </span>
                                </td>
                                
                                <td class="text-center pr-4">
                                    <div class="d-inline-flex align-items-center justify-content-center">
                                        <!-- Chi tiết -->
                                        <a href="{{ route('admin.products.show', $product->id) }}" 
                                           class="btn btn-sm btn-info text-white mr-1" 
                                           title="Chi tiết">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        <!-- Sửa -->
                                        <a href="{{ route('admin.products.edit', $product->id) }}" 
                                           class="btn btn-sm btn-success text-white mr-1" 
                                           title="Sửa">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <!-- Trạng thái Toggle -->
                                        <form action="{{ route('admin.products.toggleStatus', $product->id) }}" method="POST" class="d-inline mr-1" onsubmit="event.stopPropagation();">
                                            @csrf
                                            <button type="submit"
                                                class="btn btn-sm {{ $product->status ? 'btn-warning text-white' : 'btn-secondary' }}"
                                                title="{{ $product->status ? 'Ẩn sản phẩm' : 'Hiện sản phẩm' }}">
                                                <i class="fas {{ $product->status ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                                            </button>
                                        </form>

                                        <!-- Xóa -->
                                        <a href="{{ route('admin.products.destroy', $product->id) }}" 
                                           class="btn btn-sm btn-danger text-white" 
                                           onclick="event.stopPropagation(); return confirm('Bạn có chắc chắn muốn xóa sản phẩm này?');" 
                                           title="Xóa">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">Không tìm thấy sản phẩm nào.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Phân trang -->
            <div class="d-flex justify-content-center mt-4 pb-3">
                {{ $products->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>

        </div>
    </div>

</div>

<!-- JS xử lý sự kiện click vào dòng để vào trang Sửa -->
@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function() {
    const rows = document.querySelectorAll(".clickable-row");
    rows.forEach(row => {
        row.addEventListener("click", function(e) {
            // Nếu click vào các nút bấm, select hoặc form thì bỏ qua sự kiện chuyển trang của dòng
            if (e.target.closest('a') || e.target.closest('button') || e.target.closest('form') || e.target.closest('select')) {
                return;
            }
            window.location = this.dataset.href;
        });
    });
});
</script>
@endpush

@endsection