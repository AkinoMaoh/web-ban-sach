@extends('admin.layout')

@section('admin_content')

<div class="container-fluid">

    <h1 class="h3 mb-2 text-gray-800">Trang thêm sản phẩm</h1>

    <div class="card-body">
        <a href="{{ route('admin.products') }}" class="btn btn-success mb-3">Quay lại</a>

        <div class="card shadow mb-4">

            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Điền thông tin sản phẩm muốn thêm</h6>
            </div>

            <div class="card-body">

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

                    {{-- CATEGORY --}}
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

                    {{-- AUTHOR --}}
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

                    {{-- PUBLISHER --}}
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

                    {{-- DESCRIPTION --}}
                    <div class="form-group">
                        <label>Mô tả</label>
                        <input type="text"
                               class="form-control"
                               name="description"
                               required>
                    </div>

                    {{-- VARIANTS --}}
                    @php
                        $editions = [
                            'Standard',
                            'Special',
                            'Special Signed'
                        ];
                    @endphp

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
                                @foreach($editions as $edition)
                                <tr>

                                    <td>
                                        {{ $edition == 'Standard'
                                            ? 'Bản thường'
                                            : ($edition == 'Special'
                                                ? 'Bản đặc biệt'
                                                : 'Bản đặc biệt có chữ ký') }}

                                        <input type="hidden"
                                               name="variants[{{ $edition }}][edition]"
                                               value="{{ $edition }}">
                                    </td>

                                    <td>
                                        <input type="number"
                                               class="form-control"
                                               name="variants[{{ $edition }}][price]"
                                               value="0"
                                               min="0">
                                    </td>

                                    <td>
                                        <input type="number"
                                               class="form-control"
                                               name="variants[{{ $edition }}][stock]"
                                               value="0"
                                               min="0">
                                    </td>

                                </tr>
                                @endforeach
                            </tbody>
                        </table>
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