@extends('admin.layout')

@section('admin_content')

<div class="container-fluid">

    <!-- Tiêu đề trang & Nút quay lại -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Thêm nhà xuất bản mới</h1>
        <a href="{{ route('admin.publishers.index') }}" class="btn btn-sm btn-secondary shadow-sm">
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

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4 border-0 rounded-lg">
                
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas id-card mr-2"></i> Điền thông tin nhà xuất bản muốn thêm
                    </h6>
                </div>

                <div class="card-body">
                    <form action="{{ route('admin.publishers.store') }}" method="POST">
                        @csrf

                        <!-- Tên nhà xuất bản -->
                        <div class="form-group mb-3">
                            <label for="name" class="font-weight-bold text-dark">
                                Tên nhà xuất bản <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name"
                                   value="{{ old('name') }}"
                                   placeholder="Nhập tên nhà xuất bản..."
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Địa chỉ -->
                        <div class="form-group mb-3">
                            <label for="address" class="font-weight-bold text-dark">
                                Địa chỉ <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('address') is-invalid @enderror" 
                                   id="address" 
                                   name="address"
                                   value="{{ old('address') }}"
                                   placeholder="Nhập địa chỉ..."
                                   required>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Website -->
                        <div class="form-group mb-4">
                            <label for="website" class="font-weight-bold text-dark">
                                Website <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('website') is-invalid @enderror" 
                                   id="website" 
                                   name="website"
                                   value="{{ old('website') }}"
                                   placeholder="Ví dụ: https://..."
                                   required>
                            @error('website')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="my-3">

                        <!-- Nút hành động -->
                        <div class="d-flex justify-content-end">
                            <a href="{{ route('admin.publishers.index') }}" class="btn btn-secondary mr-2 px-4">
                                Hủy bỏ
                            </a>
                            <button type="submit" class="btn btn-primary px-4 font-weight-bold shadow-sm">
                                <i class="fas fa-plus-circle mr-1"></i> Thêm nhà xuất bản
                            </button>
                        </div>

                    </form>
                </div>

            </div>
        </div>
    </div>

</div>

@endsection