@extends('admin.layout')

@section('admin_content')

<div class="container-fluid">

    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800">Trang quản lý tin tức</h1>

    <div class="card shadow mb-4">

        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                Dữ liệu tin tức
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

            <a href="{{ route('admin.news.create') }}" class="btn btn-success mb-3">
                Thêm tin tức
            </a>

            <div class="card-body">

                <div class="table-responsive">

                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">

                        <thead>

                            <tr>
                                <th>ID</th>
                                <th>Tiêu đề</th>
                                <th>Ảnh</th>
                                <th>Mô tả</th>
                                <th>Trạng thái</th>
                                <th>Hành động</th>
                            </tr>

                        </thead>

                        <tbody>

                        @foreach($news as $item)

                        <tr>

                            <td>
                                {{ $item->id }}
                            </td>

                            <td>
                                {{ $item->title }}
                            </td>

                            <td>
                                @if($item->image)
                                    <img src="{{ asset('uploads/news/' . $item->image) }}" width="80" alt="Ảnh tin tức">
                                @else
                                    Không có ảnh
                                @endif
                            </td>

                            <td>
                                {{ Str::limit($item->summary, 60) }}
                            </td>

                            <td>
                                @if($item->status == 1)
                                    <span class="badge badge-success">Hiển thị</span>
                                @else
                                    <span class="badge badge-danger">Ẩn</span>
                                @endif
                            </td>

                            <td>
                                <a href="{{ route('admin.news.show', $item->id) }}"
                                    class="btn btn-sm btn-primary"
                                    title="Chi tiết">
                                    <i class="fas fa-eye"></i>
                                </a>

                                <a href="{{ route('admin.news.edit',$item->id) }}"
                                    class="btn btn-sm btn-success" title="Sửa">
                                    <i class="fas fa-edit"></i>
                                </a>

                                <form action="{{ route('admin.news.destroy', $item->id) }}"
                                    method="POST"
                                    style="display:inline">

                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            class="btn btn-sm btn-danger"
                                            onclick="return confirm('Bạn có chắc chắn muốn xóa tin tức này?')"
                                            title="Xóa">
                                        <i class="fas fa-trash"></i>
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
        {{ $news->appends(request()->query())->links() }}
    </div>

</div>

@endsection