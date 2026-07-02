@extends('admin.layout')

@section('admin_content')

<div class="container-fluid">

    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800">Trang quản lý nhà xuất bản</h1>

    <div class="card shadow mb-4">

        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                Dữ liệu nhà xuất bản
            </h6>
        </div>
@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

        <div class="card-body">

            <a href="{{ route('admin.publishers.create') }}" class="btn btn-success mb-3">
                Thêm nhà xuất bản
            </a>


            <div class="card-body">

                <div class="table-responsive">

                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">

                        <thead>

                            <tr>
                                <th>ID</th>
                                <th>Tên nhà xuất bản</th>
                                <th>Địa chỉ</th>
                                <th>Website</th>
                                <th>Hành động</th>
                            </tr>

                        </thead>


                        <tbody>


                        @foreach($publishers as $publisher)

                        <tr>

                            <td>
                                {{ $publisher->id }}
                            </td>


                            <td>
                                {{ $publisher->name }}
                            </td>


                            <td>
                                {{ $publisher->address }}
                            </td>


                            <td>
                                {{ $publisher->website }}
                            </td>


                        


                            <td>


                                <a href="{{ route('admin.publishers.edit',$publisher->id) }}"
                                    class="btn btn-sm btn-success">
                                    Sửa
                                </a>



                                <form action="{{ route('admin.publishers.destroy',$publisher->id) }}" 
                                    method="POST" 
                                    style="display:inline">

                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                        class="btn btn-sm btn-danger"
                                        onclick="return confirm('Bạn có chắc chắn muốn xóa nhà xuất bản này?')">

                                        Xóa

                                    </button>

                                </form>


                            </td>


                        </tr>


                        @endforeach


                        </tbody>


                    </table>


                </div>


            </div>


        </div>


    </div>
    <div class="d-flex justify-content-center mt-3">
                {{ $publishers->appends(request()->query())->links() }}
            </div>

</div>


@endsection