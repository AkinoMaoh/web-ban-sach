@extends('admin.layout')

@section('admin_content')

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <h1 class="h3 mb-2 text-gray-800">Trang thêm danh mục</h1>
                   <div class="card-body">
                            <a href="{{ route('admin.categories') }}" class="btn btn-success mb-3">Quay lại</a>
                        <div class="card-body">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Điền thông tin danh mục muốn thêm</h6>
                        </div>
                            <div class="card-body">
                                <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                  
                                    <div class="form-group">
                                        <label for="name">Tên danh mục</label>
                                        <input type="text" class="form-control" id="name" name="name" >
                                    </div>       
                                    <div class="form-group">
                                        <label for="avatar">Ảnh danh mục</label>
                                        <input type="file" class="form-control-file" id="image" name="image"> 
                                    </div>
                                    <button type="submit" class="btn btn-primary">Thêm danh mục</button
                                  
                                </form>
                            </div>
                        </div>

                    </div>

                </div>

@endsection
