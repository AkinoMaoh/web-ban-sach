@extends('admin.layout')

@section('admin_content')

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <h1 class="h3 mb-2 text-gray-800">Trang cập nhật sản phẩm</h1>
                   <div class="card-body">
                            <a href="{{ route('admin.products') }}" class="btn btn-success mb-3">Quay lại</a>
                        <div class="card-body">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Thông tin sản phẩm</h6>
                        </div>
                            <div class="card-body">
                                <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                  @method('PUT')
                                    <div class="form-group">
                                        <label for="name">Tên sách</label>
                                        <input type="text" class="form-control" id="name" name="name" value="{{ $product->name }}" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="category_id">Danh mục</label>
                                        <select name="category_id" class="form-control">
                                                @foreach($categories as $cat)
                                                    <option value="{{ $cat->id }}"
                                                        {{ $product->category_id == $cat->id ? 'selected' : '' }}>
                                                        {{ $cat->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="author_id">Tác giả</label>
                                        <select name="author_id" class="form-control">
                                            @foreach($authors as $author)
                                                <option value="{{ $author->id }}"
                                                    {{ $product->author_id == $author->id ? 'selected' : '' }}>
                                                    {{ $author->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="publisher_id">NXB</label>
                                        <select name="publisher_id" class="form-control">
                                            @foreach($publishers as $publisher)
                                                <option value="{{ $publisher->id }}"
                                                    {{ $product->publisher_id == $publisher->id ? 'selected' : '' }}>
                                                    {{ $publisher->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="description">Đánh giá</label>
                                        <input type="text" class="form-control" id="description" name="description" value="{{ $product->description }}" required>
                                    </div>
                                  <div class="form-group">
                                        <label>Danh sách biến thể</label>

                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Phiên bản</th>
                                                    <th>Giá</th>
                                                    <th>Số lượng</th>
                                                </tr>
                                            </thead>

                                           <tbody>
                                                @foreach($productVariants as $variant)
                                                    <tr>
                                                        <td>
                                                            {{ $variant->edition == 'Standard' ? 'Bản thường' : 'Bản đặc biệt' }}
                                                            <input type="hidden" name="variants[{{ $variant->id }}][edition]" value="{{ $variant->edition }}">
                                                        </td>

                                                        {{-- GIÁ --}}
                                                        <td>
                                                            @if($variant->edition == 'Standard')
                                                                <input type="number"
                                                                    class="form-control"
                                                                    name="variants[{{ $variant->id }}][price]"
                                                                    value="{{ $variant->price }}"
                                                                    required>
                                                            @else
                                                                <input type="number"
                                                                    class="form-control"
                                                                    name="variants[{{ $variant->id }}][price]"
                                                                    value="{{ $variant->price }}"
                                                                    readonly>
                                                            @endif
                                                        </td>

                                                        {{-- SỐ LƯỢNG --}}
                                                        <td>
                                                            <input type="number"
                                                                class="form-control"
                                                                name="variants[{{ $variant->id }}][stock]"
                                                                value="{{ $variant->stock }}">
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="form-group">
                                        <label>Tổng giá gốc</label>
                                        <input type="text"
                                            class="form-control"
                                            value="{{ number_format($product->price, 0, ',', '.') }} đ"
                                            readonly>
                                    </div>

                                    <div class="form-group">
                                        <label>Tổng số lượng</label>
                                        <input type="text"
                                            class="form-control"
                                            value="{{ $productVariants->sum('stock') }}"
                                            readonly>
                                    </div>  
                                 <div class="form-group">
                                        <label for="image">Ảnh sản phẩm</label>
                                        <input type="file" class="form-control-file" id="image" name="image"> 
                                    </div>
                                  <button type="submit" class="btn btn-primary">Cập nhật sản phẩm</button>
                                </form>
                            </div>
                        </div>

                    </div>

                </div>
<script>
document.querySelectorAll('tr').forEach(row => {
    const edition = row.querySelector('input[type="hidden"]');
    const priceInput = row.querySelector('input[name*="[price]"]');

    if (edition && edition.value === 'Special') {
        priceInput.readOnly = true;
    }
});
</script>
@endsection
