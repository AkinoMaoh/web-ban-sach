@extends('admin.layout')

@section('admin_content')

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <h1 class="h3 mb-2 text-gray-800">Trang thêm biến thể sản phẩm</h1>
                   <div class="card-body">
                            <a href="{{ route('admin.products') }}" class="btn btn-success mb-3">Quay lại</a>
                        <div class="card-body">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Điền thông tin biến thể của sản phẩm</h6>
                        </div>
                            <div class="card-body">
                                <form action="{{ route('admin.products.variants.store', $product->id) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                  <div class="form-group">
                                        <label for="name">Tên sách</label>
                                        <input type="text" class="form-control" id="name" name="name" value="{{ $product->name }}" readonly>
                                    </div>
                                  <div class="form-group">
                                    <label for="edition">Phiên bản</label>
                                    <select class="form-control" name="edition" id="edition" required>
                                        <option value="Standard">Bản thường</option>
                                        <option value="Special">Bản đặc biệt</option>
                                    </select>
                                </div>
                                    <div class="form-group">
                                        <label for="price">Giá</label>
                                        <input type="number"
                                            class="form-control"
                                            id="price"
                                            name="price">
                                    </div>
                                    <div class="form-group">
                                        <label for="stock">Số lượng</label>
                                        <input type="number"
                                            class="form-control"
                                            id="stock"
                                            name="stock"
                                            value="{{ old('stock') }}"
                                            required>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary">Thêm biến thể sản phẩm</button>
                                </form>
                            </div>
                        </div>

                    </div>

                </div>
                <script>
document.addEventListener('DOMContentLoaded', function () {

    const edition = document.getElementById('edition');
    const priceGroup = document.getElementById('price-group');
    const priceInput = document.getElementById('price');

    function togglePrice() {

        if (edition.value === 'Special') {

            priceGroup.style.display = 'none';
            priceInput.value = '';
            priceInput.removeAttribute('required');

        } else {

            priceGroup.style.display = 'block';
            priceInput.setAttribute('required', 'required');

        }
    }

    edition.addEventListener('change', togglePrice);

    togglePrice();
});
</script>

@endsection