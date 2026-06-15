@extends('admin.layout')

@section('admin_content')

<div class="container-fluid">

    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800">Trang thêm nhà xuất bản</h1>


    <div class="card-body">

        <a href="{{ route('admin.publishers.index') }}" class="btn btn-success mb-3">
            Quay lại
        </a>


        <div class="card-body">

            <div class="card shadow mb-4">

                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        Điền thông tin nhà xuất bản muốn thêm
                    </h6>
                </div>


                <div class="card-body">

                    <form action="{{ route('admin.publishers.store') }}" method="POST">

                        @csrf


                        <div class="form-group">

                            <label for="name">
                                Tên nhà xuất bản
                            </label>

                            <input 
                                type="text" 
                                class="form-control" 
                                id="name" 
                                name="name"
                                required>

                        </div>



                        <div class="form-group">

                            <label for="address">
                                Địa chỉ
                            </label>

                            <input 
                                type="text" 
                                class="form-control" 
                                id="address" 
                                name="address"
                                required>

                        </div>



                        <div class="form-group">

                            <label for="website">
                                Website
                            </label>

                            <input 
                                type="text" 
                                class="form-control" 
                                id="website" 
                                name="website"
                                required>

                        </div>





                        <button type="submit" class="btn btn-primary">
                            Thêm nhà xuất bản
                        </button>


                    </form>


                </div>

            </div>

        </div>

    </div>


</div>


@endsection