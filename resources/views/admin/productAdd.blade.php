@extends('admin.layout')

@section('admin_content')
<script>
document.addEventListener('DOMContentLoaded', function () {

    let index = document.querySelectorAll('#variantTable tbody tr').length;

    // Thêm biến thể
    document.getElementById('addVariant').addEventListener('click', function () {

        let html = `
        <tr>
            <td>
                <input type="text"
                       class="form-control"
                       name="variants[${index}][edition]"
                       placeholder="Tên phiên bản"
                       required>
            </td>

            <td>
                <input type="number"
                       class="form-control"
                       name="variants[${index}][price]"
                       value="0"
                       min="0"
                       required>
            </td>

            <td>
                <input type="number"
                       class="form-control"
                       name="variants[${index}][stock]"
                       value="0"
                       min="0"
                       required>
            </td>

            <td>
                <button type="button" class="btn btn-danger removeRow">
                    X
                </button>
            </td>
        </tr>
        `;

        document.querySelector('#variantTable tbody')
            .insertAdjacentHTML('beforeend', html);

        index++;
    });

    // Xóa biến thể
    document.addEventListener('click', function (e) {

        if (!e.target.classList.contains('removeRow')) return;

        const tbody = document.querySelector('#variantTable tbody');
        const rows = tbody.querySelectorAll('tr');

        // Không cho xóa hết
        if (rows.length <= 1) {
            alert('Sản phẩm phải có ít nhất một phiên bản.');
            return;
        }

        if (confirm('Bạn có chắc muốn xóa phiên bản này?')) {
            e.target.closest('tr').remove();
        }

    });
    document.addEventListener("input", function(e){

    if(e.target.classList.contains("price") ||
       e.target.classList.contains("sale_price")){

        let row = e.target.closest("tr");

        let price = parseFloat(row.querySelector(".price").value) || 0;
        let sale = parseFloat(row.querySelector(".sale_price").value) || 0;

        let percent = 0;

        if(price > 0 && sale < price){
            percent = Math.round((price - sale) / price * 100);
        }

        row.querySelector(".discount_percent").value = percent;
    }

});

});
</script>
<div class="container-fluid">

    <h1 class="h3 mb-2 text-gray-800">Trang thêm sản phẩm</h1>

    <div class="card-body">
        <a href="{{ route('admin.products') }}" class="btn btn-success mb-3">Quay lại</a>

        <div class="card shadow mb-4">

            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Điền thông tin sản phẩm muốn thêm</h6>
            </div>

            <div class="card-body">
@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
                <form action="{{ route('admin.products.store') }}"
                      method="POST"
                      enctype="multipart/form-data">

                    @csrf

                    {{-- NAME --}}
                    <div class="form-group">
                        <label>Tên sách</label>
                        <input type="text"
                               class="form-control"
                               name="name"
                               required>
                    </div>

                  <div class="row">
                    {{-- CATEGORY --}}
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Danh mục</label>
                            <select name="category_id" class="form-control" required>
                                <option value="">Chọn danh mục</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- AUTHOR --}}
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Tác giả</label>
                            <select name="author_id" class="form-control" required>
                                <option value="">Chọn tác giả</option>
                                @foreach($authors as $author)
                                    <option value="{{ $author->id }}">
                                        {{ $author->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- PUBLISHER --}}
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>NXB</label>
                            <select name="publisher_id" class="form-control" required>
                                <option value="">Chọn NXB</option>
                                @foreach($publishers as $publisher)
                                    <option value="{{ $publisher->id }}">
                                        {{ $publisher->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>  
                    {{-- DESCRIPTION --}}
                    <div class="form-group">
                        <label>Mô tả</label>
                        <input type="text"
                               class="form-control"
                               name="description"
                               required>
                    </div>

                   <div class="form-group">
    <label>Danh sách biến thể</label>

    <table class="table table-bordered" id="variantTable">
        <thead>
            <tr>
                <th>Phiên bản</th>
                <th>Giá</th>
                <th>Giá giảm</th>
                <th>% Giảm</th>
                <th>Số lượng</th>
                <th></th>
            </tr>
        </thead>

        <tbody>
            <tr>
                <td>
                    <input type="text"
                           class="form-control"
                           name="variants[0][edition]"
                           placeholder="Ví dụ: Bản thường">
                </td>

                <td>
                    <input type="number"
                        class="form-control"
                        name="variants[0][price]"
                        min="0"
                        step="1"
                        value="0"
                        onkeydown="return event.key.match(/[0-9]|Backspace|Delete|Tab|ArrowLeft|ArrowRight/) != null"
                        oninput="this.value=this.value.replace(/\D/g,'')">
                </td>
                <td>
                    <input type="number"
       class="form-control sale_price"
       name="variants[0][sale_price]"
       value="0"
       min="0">
                </td>
                <td>
                    <input type="number"
       class="form-control discount_percent"
       value="0"
       readonly>
                </td>

                <td>
                    <input type="number"
                        class="form-control"
                        name="variants[0][stock]"
                        min="0"
                        step="1"
                        value="0"
                        onkeydown="return event.key.match(/[0-9]|Backspace|Delete|Tab|ArrowLeft|ArrowRight/) != null"
                        oninput="this.value=this.value.replace(/\D/g,'')">
                </td>

                <td>
                    <button type="button" class="btn btn-danger removeRow">X</button>
                </td>
            </tr>
        </tbody>
    </table>

    <button type="button" class="btn btn-success" id="addVariant">
        + Thêm phiên bản
    </button>
</div>

                    {{-- IMAGE --}}
                    <div class="form-group">
                        <label>Ảnh sản phẩm</label>
                        <input type="file"
                               class="form-control-file"
                               name="image"
                               required>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        Thêm sản phẩm
                    </button>

                </form>

            </div>
        </div>

    </div>
</div>

@endsection
