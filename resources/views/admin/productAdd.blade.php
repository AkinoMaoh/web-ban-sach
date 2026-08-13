@extends('admin.layout')

@section('admin_content')
<style>
#thumbnail-list img{
    width:70px;
    height:70px;
    object-fit:cover;
    border:2px solid #ddd;
    border-radius:6px;
    cursor:pointer;
    margin:5px;
    transition:.2s;
}

#thumbnail-list img:hover{
    border-color:#0d6efd;
    transform:scale(1.05);
}

#thumbnail-list img.active{
    border:3px solid #0d6efd;
}
</style>
<div class="container-fluid">

    <!-- Tiêu đề trang & Nút quay lại -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Thêm sản phẩm mới</h1>
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

    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="row">
            <!-- CỘT TRÁI: Thông tin cơ bản và Quản lý Biến thể -->
            <div class="col-lg-8">
                
                <!-- Thẻ Thông tin sản phẩm -->
                <div class="card shadow mb-4 border-0 rounded-lg">
                    <div class="card-header py-3 bg-white">
                        <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-info-circle mr-2"></i> Thông tin sản phẩm</h6>
                    </div>
                    <div class="card-body">
                        
                        <!-- Tên sách -->
                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-dark">Tên sách <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-lg @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required placeholder="Nhập tên sách...">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <!-- Danh mục -->
                            <div class="col-md-4 form-group mb-3">
                                <label class="font-weight-bold text-dark">Danh mục <span class="text-danger">*</span></label>
                                <select name="category_id" class="form-control" required>
                                    <option value="">-- Chọn danh mục --</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                            {{ $cat->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Tác giả -->
                            <div class="col-md-4 form-group mb-3">
                                <label class="font-weight-bold text-dark">Tác giả <span class="text-danger">*</span></label>
                                <select name="author_id" class="form-control" required>
                                    <option value="">-- Chọn tác giả --</option>
                                    @foreach($authors as $author)
                                        <option value="{{ $author->id }}" {{ old('author_id') == $author->id ? 'selected' : '' }}>
                                            {{ $author->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Nhà xuất bản -->
                            <div class="col-md-4 form-group mb-3">
                                <label class="font-weight-bold text-dark">NXB <span class="text-danger">*</span></label>
                                <select name="publisher_id" class="form-control" required>
                                    <option value="">-- Chọn NXB --</option>
                                    @foreach($publishers as $publisher)
                                        <option value="{{ $publisher->id }}" {{ old('publisher_id') == $publisher->id ? 'selected' : '' }}>
                                            {{ $publisher->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Mô tả -->
                        <div class="form-group mb-0">
                            <label class="font-weight-bold text-dark">Mô tả sản phẩm <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="description" rows="4" placeholder="Nhập mô tả sản phẩm..." required>{{ old('description') }}</textarea>
                        </div>

                    </div>
                </div>

                <!-- Thẻ Biến thể sản phẩm -->
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
                                        <th width="26%">Phiên bản</th>
                                        <th width="20%">Giá gốc</th>
                                        <th width="20%">Giá giảm</th>
                                        <th width="14%">% Giảm</th>
                                        <th width="14%">Số lượng</th>
                                        <th width="6%" class="text-center">Xóa</th>
                                    </tr>
                                </thead>
                                <tbody>
    <tr>
        <td>
            <select
                class="form-control variant-select"
                name="variants[0][variant_id]"
                required
            >
                <option value="">-- Chọn phiên bản --</option>

                @foreach($variants as $v)
                    <option
                        value="{{ $v->id }}"
                        {{ old('variants.0.variant_id') == $v->id ? 'selected' : '' }}
                    >
                        {{ $v->name }}
                    </option>
                @endforeach

            </select>
        </td>

        <td>
            <input
                type="number"
                class="form-control price"
                name="variants[0][price]"
                min="0"
                step="1"
                value="{{ old('variants.0.price', 0) }}"
                onkeydown="return event.key.match(/[0-9]|Backspace|Delete|Tab|ArrowLeft|ArrowRight/) != null"
                oninput="this.value=this.value.replace(/\D/g,'')"
                required
            >
        </td>

        <td>
            <input
                type="number"
                class="form-control sale_price"
                name="variants[0][sale_price]"
                value="{{ old('variants.0.sale_price', 0) }}"
                min="0"
            >
        </td>

        <td>
            <input
                type="number"
                class="form-control discount_percent bg-light"
                value="0"
                readonly
            >
        </td>

        <td>
            <input
                type="number"
                class="form-control"
                name="variants[0][stock]"
                min="0"
                step="1"
                value="{{ old('variants.0.stock', 0) }}"
                onkeydown="return event.key.match(/[0-9]|Backspace|Delete|Tab|ArrowLeft|ArrowRight/) != null"
                oninput="this.value=this.value.replace(/\D/g,'')"
                required
            >
        </td>

        <td class="text-center">
            <button
                type="button"
                class="btn btn-sm btn-danger removeRow"
                title="Xóa dòng"
            >
                <i class="fas fa-times"></i>
            </button>
        </td>
    </tr>
</tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
            <!-- CỘT PHẢI: Hình ảnh và Nút thao tác -->
            <div class="col-lg-4">
                
                        <!-- Thẻ Hình ảnh -->
         <div class="card shadow mb-4 border-0 rounded-lg">
    <div class="card-header py-3 bg-white">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-image mr-2"></i>
            Ảnh sản phẩm <span class="text-danger">*</span>
        </h6>
    </div>

    <div class="card-body text-center">

        <!-- Ảnh lớn -->
        <img id="main-preview"
             src="https://placehold.co/350x200?text=Product+Preview"
             class="img-fluid rounded border shadow-sm mb-3"
             style="height:220px;width:100%;object-fit:cover;">

        <!-- Thumbnail -->
<div id="thumbnail-list" class="d-flex flex-wrap">

</div>


        <input
    type="file"
    id="images"
    name="images[]"
    class="form-control"
    accept="image/*"
    multiple>
<small class="text-muted">
    Tối đa 7 ảnh sản phẩm
</small>

    </div>
</div>

                <!-- Thẻ Lưu thao tác -->
                <div class="card shadow mb-4 border-0 rounded-lg sticky-top" style="top: 20px;">
                    <div class="card-header py-3 bg-white">
                        <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-save mr-2"></i> Hoàn tất</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-column">
                            <button type="submit" class="btn btn-primary btn-block py-2 font-weight-bold shadow-sm mb-2">
                                <i class="fas fa-plus-circle mr-1"></i> Thêm sản phẩm
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

<!-- JavaScript xử lý động biến thể, tính phần trăm giảm giá và xem trước ảnh -->
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    let index = document.querySelectorAll('#variantTable tbody tr').length;

    // Thêm biến thể
    document.getElementById('addVariant').addEventListener('click', function () {
        let html = `
        <tr>
            <td>
                <input type="text" class="form-control" name="variants[${index}][edition]" placeholder="Ví dụ: Bản đặc biệt" required>
            </td>
            <td>
                <input type="number" class="form-control price" name="variants[${index}][price]" value="0" min="0" step="1" required onkeydown="return event.key.match(/[0-9]|Backspace|Delete|Tab|ArrowLeft|ArrowRight/) != null" oninput="this.value=this.value.replace(/\\D/g,'')">
            </td>
            <td>
                <input type="number" class="form-control sale_price" name="variants[${index}][sale_price]" value="0" min="0">
            </td>
            <td>
                <input type="number" class="form-control discount_percent bg-light" value="0" readonly>
            </td>
            <td>
                <input type="number" class="form-control" name="variants[${index}][stock]" value="0" min="0" step="1" required onkeydown="return event.key.match(/[0-9]|Backspace|Delete|Tab|ArrowLeft|ArrowRight/) != null" oninput="this.value=this.value.replace(/\\D/g,'')">
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

    // Tự động tính phần trăm giảm giá khi nhập giá gốc hoặc giá giảm
    document.addEventListener("input", function(e){
        if(e.target.classList.contains("price") || e.target.classList.contains("sale_price")){
            let row = e.target.closest("tr");
            let priceInput = row.querySelector(".price");
            let saleInput = row.querySelector(".sale_price");
            let percentInput = row.querySelector(".discount_percent");

            if(priceInput && saleInput && percentInput){
                let price = parseFloat(priceInput.value) || 0;
                let sale = parseFloat(saleInput.value) || 0;
                let percent = 0;

                if(price > 0 && sale > 0 && sale < price){
                    percent = Math.round((price - sale) / price * 100);
                }
                percentInput.value = percent;
            }
        }
    });

    // Xem trước ảnh khi chọn file
    const imageInput = document.getElementById('image');
    if(imageInput) {
        imageInput.addEventListener('change', function(e){
            if(e.target.files.length){
                document.getElementById('preview').src = URL.createObjectURL(e.target.files[0]);
            }
        });
    }
});
const input = document.getElementById('images');
const mainPreview = document.getElementById('main-preview');
const thumbnailList = document.getElementById('thumbnail-list');

if(input && thumbnailList){

    input.addEventListener('change', function () {

        thumbnailList.innerHTML = "";

        const files = Array.from(this.files);

        files.forEach((file,index)=>{

            const reader = new FileReader();

            reader.onload = function(e){

                const img = document.createElement('img');

                img.src = e.target.result;

                if(index === 0){
                    mainPreview.src = e.target.result;
                    img.classList.add("active");
                }


                img.onclick=function(){

                    mainPreview.src=this.src;

                    document.querySelectorAll('#thumbnail-list img')
                    .forEach(i=>i.classList.remove('active'));

                    this.classList.add('active');

                };


                thumbnailList.appendChild(img);

            }


            reader.readAsDataURL(file);

        });

    });

}
const imageList = document.getElementById('image-list');

if(imageList){

    new Sortable(imageList,{

        animation:200,

        ghostClass:'sortable-ghost',

        onEnd:function(){

            let images=[];

            document.querySelectorAll('#image-list .image-item')
                .forEach(item=>{

                    images.push(item.dataset.id);

                });

            fetch("{{ route('admin.products.image.sort') }}",{

                method:"POST",

                headers:{
                    "Content-Type":"application/json",
                    "X-CSRF-TOKEN":"{{ csrf_token() }}"
                },

                body:JSON.stringify({
                    images:images
                })

            });

        }

    });
const imageInput = document.getElementById('images');

if(imageInput){

    imageInput.addEventListener('change', function(){

        if(this.files.length > 7){

            alert('Chỉ được phép thêm tối đa 7 ảnh!');

            this.value = '';

            return;
        }

    });

}
}

</script>

@endpush

@endsection