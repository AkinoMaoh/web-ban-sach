@extends('admin.layout')

@section('admin_content')

<div class="container-fluid">

    <!-- Tiêu đề trang & Nút hành động -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Chi tiết sản phẩm: <span class="text-primary">{{ $product->name }}</span></h1>
        <div>
            <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-sm btn-success shadow-sm mr-1">
                <i class="fas fa-edit fa-sm text-white-50 mr-1"></i> Chỉnh sửa
            </a>
            <a href="{{ route('admin.products') }}" class="btn btn-sm btn-secondary shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50 mr-1"></i> Quay lại danh sách
            </a>
        </div>
    </div>

    <div class="row">
        <!-- CỘT TRÁI: Thông tin chi tiết & Bảng biến thể -->
        <div class="col-lg-8">
            
            <!-- Thẻ Thông tin chung -->
            <div class="card shadow mb-4 border-0 rounded-lg">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-info-circle mr-2"></i> Thông tin sản phẩm</h6>
                </div>
                <div class="card-body">
                    
                    <!-- Tên sách -->
                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-uppercase small text-muted d-block mb-1">Tên sách</label>
                        <h4 class="text-dark font-weight-bold">{{ $product->name }}</h4>
                    </div>

                    <div class="row">
                        <!-- Danh mục -->
                        <div class="col-md-4 form-group mb-3">
                            <label class="font-weight-bold text-uppercase small text-muted d-block mb-1">Danh mục</label>
                            <span class="badge badge-light border px-2 py-1">{{ $product->category->name ?? 'N/A' }}</span>
                        </div>

                        <!-- Tác giả -->
                        <div class="col-md-4 form-group mb-3">
                            <label class="font-weight-bold text-uppercase small text-muted d-block mb-1">Tác giả</label>
                            <span class="badge badge-light border px-2 py-1">{{ $product->author->name ?? ($product->authors->name ?? 'N/A') }}</span>
                        </div>

                        <!-- Nhà xuất bản -->
                        <div class="col-md-4 form-group mb-3">
                            <label class="font-weight-bold text-uppercase small text-muted d-block mb-1">NXB</label>
                            <span class="badge badge-light border px-2 py-1">{{ $product->publisher->name ?? ($product->publishers->name ?? 'N/A') }}</span>
                        </div>
                    </div>

                    <!-- Mô tả -->
                    <div class="form-group mb-0">
                        <label class="font-weight-bold text-uppercase small text-muted d-block mb-1">Mô tả sản phẩm</label>
                        <div class="text-dark bg-light p-3 rounded border" style="line-height: 1.6;">{{ $product->description ?? 'Không có mô tả' }}</div>
                    </div>

                </div>
            </div>

            <!-- Thẻ Danh sách biến thể -->
<div class="card shadow mb-4 border-0 rounded-lg">
    <div class="card-header py-3 bg-white">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-layer-group mr-2"></i>
            Danh sách phiên bản & Giá
        </h6>
    </div>

    <div class="card-body">

        <div class="table-responsive">
            <table class="table table-bordered align-middle mb-0">

                <thead class="bg-light text-uppercase font-weight-bold text-dark"
                       style="font-size: 0.8rem;">

                    <tr>
                        <th width="28%">Tên biến thể</th>
                        <th width="22%">Giá gốc</th>
                        <th width="22%">Giá giảm</th>
                        <th width="14%">% Giảm</th>
                        <th width="14%" class="text-center">Số lượng</th>
                    </tr>

                </thead>

                <tbody>

                    @forelse($productVariants as $variant)

                        <tr>

                            {{-- Tên biến thể --}}
                            <td class="font-weight-bold text-dark">
                                {{ $variant->variant->name }}
                            </td>

                            {{-- Giá gốc --}}
                            <td>
                                {{ number_format($variant->price, 0, ',', '.') }} đ
                            </td>

                            {{-- Giá giảm --}}
                            <td>

                                @if($variant->sale_price > 0)

                                    <span class="text-danger font-weight-bold">
                                        {{ number_format($variant->sale_price, 0, ',', '.') }} đ
                                    </span>

                                @else

                                    <span class="text-muted small">
                                        Không giảm
                                    </span>

                                @endif

                            </td>

                            {{-- % giảm --}}
                            <td>

                                @if(
                                    isset($variant->discount_percent) &&
                                    $variant->discount_percent > 0
                                )

                                    <span class="badge badge-danger px-2 py-1">
                                        -{{ $variant->discount_percent }}%
                                    </span>

                                @else

                                    <span class="badge badge-secondary px-2 py-1">
                                        0%
                                    </span>

                                @endif

                            </td>

                            {{-- Số lượng --}}
                            <td class="text-center font-weight-bold">
                                {{ $variant->stock }}
                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="5"
                                class="text-center text-muted py-3">
                                Không có phiên bản nào.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>
        </div>

    </div>
</div>
        </div>
        <!-- CỘT PHẢI: Hình ảnh, Kho hàng & Thao tác -->
        <div class="col-lg-4">
            
            <!-- Thẻ Ảnh -->

<div class="card shadow mb-4 border-0 rounded-lg">
    <div class="card-header py-3 bg-white">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-image mr-2"></i> Ảnh sản phẩm
        </h6>
    </div>

<div class="card-body text-center">

    @php
        $sortedImages = $product->images
            ->sortBy('sort_order')
            ->values();
    @endphp

    @if($sortedImages->count())

        {{-- Ảnh lớn --}}
        <img
            id="main-image"
            src="{{ asset('uploads/products/' . $sortedImages->first()->image) }}"
            class="img-fluid rounded shadow-sm border mb-3"
            style="width:100%;height:250px;object-fit:cover;">

        {{-- Thumbnail --}}
        <div class="row">

            @foreach($sortedImages as $image)

                <div class="col-3 mb-2">

                    <img
                        src="{{ asset('uploads/products/' . $image->image) }}"
                        class="img-thumbnail thumb-image"
                        style="height:70px;width:100%;object-fit:cover;cursor:pointer;">

                </div>

            @endforeach

        </div>

    @else

        <span class="text-muted">Không có ảnh</span>

    @endif

</div>


</div>


            <!-- Thẻ Tổng số lượng & Thao tác -->
            <div class="card shadow mb-4 border-0 rounded-lg sticky-top" style="top: 20px;">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-boxes mr-2"></i> Thống kê & Thao tác</h6>
                </div>
                <div class="card-body">
                    
                    <div class="form-group mb-4">
                        <label class="font-weight-bold text-dark small text-uppercase">Tổng số lượng tồn kho</label>
                        <input type="text" class="form-control font-weight-bold text-success bg-light text-center" value="{{ $productVariants->sum('stock') }} cuốn" readonly>
                    </div>

                    <hr class="my-3">

                    <div class="d-flex flex-column">
                        <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-success btn-block py-2 font-weight-bold shadow-sm mb-2">
                            <i class="fas fa-edit mr-1"></i> Chỉnh sửa sản phẩm
                        </a>
                        <a href="{{ route('admin.products') }}" class="btn btn-light btn-block text-muted py-2 border">
                            Quay lại danh sách
                        </a>
                    </div>

                </div>
            </div>

        </div>
    </div>

</div>

@endsection
@push('scripts')
<script>

document.querySelectorAll('.thumb-image').forEach(img=>{

    img.addEventListener('click',function(){

        document.getElementById('main-image').src=this.src;

    });

});

</script>
@endpush
<style>
    .thumb-image{
    transition:.2s;
}

.thumb-image:hover{
    transform:scale(1.05);
    border:2px solid #007bff;
}
</style>