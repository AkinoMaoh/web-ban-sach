@extends('admin.layout')

@section('admin_content')

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <h1 class="h3 mb-2 text-gray-800">Trang chi tiết tác giả</h1>
                   <div class="card-body">
                            <a href="{{ route('admin.authors') }}" class="btn btn-success mb-3">Quay lại</a>
                        <div class="card-body">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Thông tin tác giả</h6>
                        </div>
                            <div class="card-body">
                                <form action="{{ route('admin.authors.update', $author->id) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                  
                                    <div class="form-group">
                                        <label for="name">Tên tác giả</label>
                                        <input type="text" class="form-control" id="name" name="name" value="{{ $author->name }}" readonly>
                                    </div>
                              
                                    <div class="form-group">
                                        <label for="bio">Tiểu sử</label>
                                        <textarea class="form-control" id="bio" name="bio" readonly>{{ $author->bio }}</textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="avatar">Ảnh đại diện</label>
                                        <br>
                                        <img src="{{ asset('uploads/authors/' . $author->avatar) }}" alt="Ảnh tác giả" width="150">
                                    </div>
                                  
                                </form>
                            </div>
                        </div>

                    </div>

                </div>

@endsection
