@extends('admin.layout')

@section('admin_content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-gray-800">Chi tiết Banner</h1>

        <a href="{{ route('admin.banners.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Quay lại
        </a>
    </div>

    <div class="card shadow">

        <div class="card-header">
            <h5 class="mb-0">Thông tin Banner</h5>
        </div>

        <div class="card-body">

            <div class="row">

                <div class="col-md-4 text-center">

                    <img src="{{ asset('uploads/banners/'.$banner->image) }}"
                         class="img-fluid rounded shadow"
                         style="max-height:300px; object-fit:cover;">

                </div>

                <div class="col-md-8">

                    <table class="table table-bordered">

                        <tr>
                            <th width="180">Tiêu đề</th>
                            <td>{{ $banner->title }}</td>
                        </tr>

                        <tr>
                            <th>Mô tả</th>
                            <td>{{ $banner->description ?? 'Không có' }}</td>
                        </tr>

                        <tr>
                            <th>Link</th>
                            <td>
                                @if($banner->link)
                                    <a href="{{ $banner->link }}" target="_blank">
                                        {{ $banner->link }}
                                    </a>
                                @else
                                    Không có
                                @endif
                            </td>
                        </tr>

                        <tr>
                            <th>Vị trí</th>
                            <td>{{ ucfirst($banner->position) }}</td>
                        </tr>

                        <tr>
                            <th>Thứ tự</th>
                            <td>{{ $banner->sort_order }}</td>
                        </tr>

                        <tr>
                            <th>Trạng thái</th>
                            <td>
                                @if($banner->status)
                                    <span class="badge badge-success">
                                        Hiển thị
                                    </span>
                                @else
                                    <span class="badge badge-danger">
                                        Ẩn
                                    </span>
                                @endif
                            </td>
                        </tr>

                        <tr>
                            <th>Ngày bắt đầu</th>
                            <td>{{ $banner->start_date ?? 'Không có' }}</td>
                        </tr>

                        <tr>
                            <th>Ngày kết thúc</th>
                            <td>{{ $banner->end_date ?? 'Không có' }}</td>
                        </tr>

                        
                    </table>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection