@extends('admin.layout')

@section('admin_content')

<div class="container-fluid">

    <!-- Tiêu đề trang & Nút quay lại -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Chỉnh sửa sản phẩm: <span class="text-primary">{{ $product->name }}</span></h1>
        <a href="{{ route('admin.products') }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50 mr-1"></i> Quay lại danh sách
        </a>
    </div>

    <!-- Hiển thị lỗi Validate nếu có -->
    @if ($errors->any())
        <div class="alert alert-danger shadow-sm border-0 mb-4">
            <h6 class="font-weight-bold mb-2"><i class="fas fa-exclamation-circle mr-1"></i> Vui lòng kiểm tra lại:</h6>
            <ul class="mb-0 pl-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row">
            <!-- CỘT TRÁI: Thông tin chính và Quản lý Biến thể -->
            <div class="col-lg-8">
                
                <!-- Thẻ thông tin cơ bản -->
                <div class="card shadow mb-4 border-0 rounded-lg">
                    <div class="card-header py-3 bg-white">
                        <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-info-circle mr-2"></i> Thông tin sản phẩm</h6>
                    </div>
                    <div class="card-body">
                        
                        <!-- Tên sách -->
                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-dark">Tên sách <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-lg @error('name') is-invalid @enderror" name="name" value="{{ old('name', $product->name) }}" required placeholder="Nhập tên sách...">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <!-- Danh mục -->
                            <div class="col-md-4 form-group mb-3">
                                <label class="font-weight-bold text-dark">Danh mục</label>
                                <select name="category_id" class="form-control">
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>
                                            {{ $cat->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Tác giả -->
                            <div class="col-md-4 form-group mb-3">
                                <label class="font-weight-bold text-dark">Tác giả</label>
                                <select name="author_id" class="form-control">
                                    @foreach($authors as $author)
                                        <option value="{{ $author->id }}" {{ old('author_id', $product->author_id) == $author->id ? 'selected' : '' }}>
                                            {{ $author->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Nhà xuất bản -->
                            <div class="col-md-4 form-group mb-3">
                                <label class="font-weight-bold text-dark">NXB</label>
                                <select name="publisher_id" class="form-control">
                                    @foreach($publishers as $publisher)
                                        <option value="{{ $publisher->id }}" {{ old('publisher_id', $product->publisher_id) == $publisher->id ? 'selected' : '' }}>
                                            {{ $publisher->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Mô tả -->
                        <div class="form-group mb-0">
                            <label class="font-weight-bold text-dark">Mô tả sản phẩm</label>
                            <textarea class="form-control" name="description" rows="4" placeholder="Nhập mô tả sản phẩm...">{{ old('description', $product->description) }}</textarea>
                        </div>

                    </div>
                </div>

                <!-- Thẻ quản lý Biến thể -->
                <div class="card shadow mb-4 border-0 rounded-lg">
                    <div class="card-header py-3 bg-white d-flex align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-layer-group mr-2"></i> Danh sách phiên bản & Giá</h6>
                        <button type="button" class="btn btn-sm btn-success shadow-sm font-weight-bold" id="addVariant">
                            <i class="fas fa-plus mr-1"></i> Thêm phiên bản
                        </button>
                    </div>
                    <div class="card-body">
                        
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle" id="variantTable">
                                <thead class="bg-light text-uppercase font-weight-bold text-dark" style="font-size: 0.8rem;">
                                    <tr>
                                        <th width="30%">Phiên bản</th>
                                        <th width="22%">Giá gốc</th>
                                        <th width="22%">Giá giảm</th>
                                        <th width="18%">Số lượng</th>
                                        <th width="8%" class="text-center">Xóa</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($productVariants as $index => $variant)
                                    <tr>
                                        <td>
                                            <input type="text" class="form-control" name="variants[{{ $index }}][edition]" value="{{ $variant->edition }}" placeholder="Tên phiên bản" required>
                                        </td>
                                        <td>
                                            <input type="number" class="form-control" name="variants[{{ $index }}][price]" value="{{ $variant->price }}" min="0" required>
                                        </td>
                                        <td>
                                            <input type="number" class="form-control" name="variants[{{ $index }}][sale_price]" value="{{ old("variants.$index.sale_price", $variant->sale_price) }}" min="0" placeholder="Để trống nếu không giảm">
                                        </td>
                                        <td>
                                            <input type="number" class="form-control" name="variants[{{ $index }}][stock]" value="{{ $variant->stock }}" min="0" required>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-danger removeRow" title="Xóa dòng"><i class="fas fa-times"></i></button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>

            </div>

            <!-- CỘT PHẢI: Hình ảnh, Thống kê kho & Nút lưu -->
            <div class="col-lg-4">
                
                <!-- Thẻ hình ảnh -->
                <div class="card shadow mb-4 border-0 rounded-lg">
                    <div class="card-header py-3 bg-white">
                        <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-image mr-2"></i> Ảnh sản phẩm</h6>
                    </div>
                    <div class="card-body text-center">
                        <div class="mb-3">

                            @if($product->images->count())

                                <img id="preview"
                                    src="{{ asset('uploads/products/'.$product->images->first()->image) }}"
                                    class="img-fluid rounded shadow-sm border"
                                    style="max-height:220px;width:100%;object-fit:cover;">

                            @else

                                <img id="preview"
                                    src="https://placehold.co/350x200?text=No+Image"
                                    class="img-fluid rounded shadow-sm border"
                                    style="max-height:220px;width:100%;object-fit:cover;">

                            @endif

                        </div>

                 <div class="row">

   <div id="preview-list" class="preview-list">

    @foreach($product->images as $image)
        <img
            src="{{ asset('uploads/products/'.$image->image) }}"
            class="thumb-image"
            alt=""
        >
    @endforeach

</div>

<div id="new-preview" class="preview-list"></div>

</div>
                        <div class="form-group text-left mb-0">
                            <input
                                type="file"
                                class="form-control"
                                id="images"
                                name="images[]"
                                accept="image/*"
                                multiple>
                            <small class="text-muted d-block mt-2">Chọn ảnh mới nếu muốn thêm</small>
                        </div>
                    </div>
                </div>

                <!-- Thẻ Tổng số lượng & Lưu -->
                <div class="card shadow mb-4 border-0 rounded-lg sticky-top" style="top: 20px;">
                    <div class="card-header py-3 bg-white">
                        <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-boxes mr-2"></i> Kho hàng & Thao tác</h6>
                    </div>
                    <div class="card-body">
                        
                        <div class="form-group mb-4">
                            <label class="font-weight-bold text-dark small text-uppercase">Tổng số lượng tồn kho</label>
                            <input type="text" class="form-control font-weight-bold text-success bg-light" value="{{ $productVariants->sum('stock') }} cuốn" readonly>
                        </div>

                        <hr class="my-3">

                        <!-- Nút Lưu & Hủy -->
                        <div class="d-flex flex-column">
                            <button type="submit" class="btn btn-success btn-block py-2 font-weight-bold shadow-sm mb-2">
                                <i class="fas fa-save mr-1"></i> Cập nhật sản phẩm
                            </button>
                            <a href="{{ route('admin.products') }}" class="btn btn-light btn-block text-muted py-2 border">
                                Hủy bỏ
                            </a>
                        </div>

                    </div>
                </div>

            </div>
        </div>

    </form>

</div>

<!-- JavaScript xử lý thêm/xóa biến thể động và xem trước ảnh -->
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    let index = {{ $productVariants->count() }};

    // Thêm biến thể
    document.getElementById('addVariant').addEventListener('click', function () {
        let html = `
        <tr>
            <td>
                <input type="text" class="form-control" name="variants[${index}][edition]" placeholder="Tên phiên bản" required>
            </td>
            <td>
                <input type="number" class="form-control" name="variants[${index}][price]" value="0" min="0" required>
            </td>
            <td>
                <input type="number" class="form-control" name="variants[${index}][sale_price]" value="" min="0" placeholder="Để trống nếu không giảm">
            </td>
            <td>
                <input type="number" class="form-control" name="variants[${index}][stock]" value="0" min="0" required>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-danger removeRow" title="Xóa dòng"><i class="fas fa-times"></i></button>
            </td>
        </tr>
        `;

        document.querySelector('#variantTable tbody').insertAdjacentHTML('beforeend', html);
        index++;
    });

    // Xóa biến thể
    document.addEventListener('click', function (e) {
        if (!e.target.closest('.removeRow')) return;

        const tbody = document.querySelector('#variantTable tbody');
        const rows = tbody.querySelectorAll('tr');

        if (rows.length <= 1) {
            alert('Sản phẩm phải có ít nhất một phiên bản.');
            return;
        }

        if (confirm('Bạn có chắc muốn xóa phiên bản này?')) {
            e.target.closest('tr').remove();
        }
    });

    // Xem trước ảnh mới khi chọn

const imageInput = document.getElementById('images');
const preview = document.getElementById('preview');
const container = document.getElementById('new-preview');

if (imageInput) {

    imageInput.addEventListener('change', function (e) {

        container.innerHTML = '';

        Array.from(e.target.files).forEach((file, index) => {

            const url = URL.createObjectURL(file);

            if (index === 0) {
                preview.src = url;
            }

           const img = document.createElement('img');

img.src = url;
img.className = 'thumb-image new-thumb';

img.onclick = function () {
    preview.src = this.src;
};

container.appendChild(img);

        });

        document.querySelectorAll('.new-thumb').forEach(img => {

            img.onclick = function () {

                preview.src = this.src;

            };

        });

    });

}

// Click ảnh cũ
document.querySelectorAll('.thumb-image').forEach(img => {

    img.onclick = function () {

        preview.src = this.src;

    };

});

});
</script>
<style>
.preview-list{
    display:flex;
    flex-wrap:wrap;
    gap:10px;
    margin-top:15px;
}

.thumb-image{
    width:70px;
    height:70px;
    object-fit:cover;
    border:2px solid #ddd;
    border-radius:6px;
    cursor:pointer;
    transition:.2s;
}

.thumb-image:hover{
    border-color:#007bff;
}
</style>
@endpush

@endsection