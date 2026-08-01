@extends('admin.layout')

@section('admin_content')

<div class="container-fluid">

    <!-- Tiêu đề trang & Nút hành động -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Chi tiết Banner: <span class="text-primary">{{ $banner->title }}</span></h1>
        <div>
            @if(Route::has('admin.banners.edit'))
                <a href="{{ route('admin.banners.edit', $banner->id) }}" class="btn btn-sm btn-success shadow-sm mr-1">
                    <i class="fas fa-edit fa-sm text-white-50 mr-1"></i> Chỉnh sửa
                </a>
            @endif
            <a href="{{ route('admin.banners.index') }}" class="btn btn-sm btn-secondary shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50 mr-1"></i> Quay lại danh sách
            </a>
        </div>
    </div>

    <div class="row">
        <!-- CỘT TRÁI: Hình ảnh Banner -->
        <div class="col-lg-4">
            <div class="card shadow mb-4 border-0 rounded-lg">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-image mr-2"></i> Hình ảnh hiển thị
                    </h6>
                </div>
                <div class="card-body text-center">
                    @if($banner->image && file_exists(public_path('uploads/banners/' . $banner->image)))
                        <img src="{{ asset('uploads/banners/' . $banner->image) }}"
                             alt="{{ $banner->title }}"
                             class="img-fluid rounded shadow-sm border"
                             style="max-height: 250px; width: 100%; object-fit: cover;">
                    @else
                        <img src="https://placehold.co/350x200?text=No+Image"
                             alt="Default Banner"
                             class="img-fluid rounded shadow-sm border"
                             style="max-height: 250px; width: 100%; object-fit: cover;">
                    @endif
                </div>
            </div>

            <!-- Thẻ Thao tác nhanh -->
            <div class="card shadow mb-4 border-0 rounded-lg sticky-top" style="top: 20px;">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-cog mr-2"></i> Thao tác
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-column">
                        @if(Route::has('admin.banners.edit'))
                            <a href="{{ route('admin.banners.edit', $banner->id) }}" class="btn btn-success btn-block py-2 font-weight-bold shadow-sm mb-2">
                                <i class="fas fa-edit mr-1"></i> Chỉnh sửa banner
                            </a>
                        @endif
                        <a href="{{ route('admin.banners.index') }}" class="btn btn-light btn-block text-muted py-2 border">
                            Quay lại danh sách
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- CỘT PHẢI: Bảng thông tin chi tiết Banner -->
        <div class="col-lg-8">
            <div class="card shadow mb-4 border-0 rounded-lg">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-info-circle mr-2"></i> Thông tin cấu hình Banner
                    </h6>
                </div>
                <div class="card-body px-0 pb-0">
                    
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle mb-0">
                            <tbody>
                                <tr>
                                    <th class="bg-light" width="25%">Tiêu đề</th>
                                    <td class="font-weight-bold text-dark">{{ $banner->title }}</td>
                                </tr>

                                <tr>
                                    <th class="bg-light">Mô tả</th>
                                    <td class="text-muted">{{ $banner->description ?? 'Không có mô tả' }}</td>
                                </tr>

                                <tr>
                                    <th class="bg-light">Đường dẫn (Link)</th>
                                    <td>
                                        @if($banner->link)
                                            <a href="{{ $banner->link }}" target="_blank" class="text-primary font-weight-bold text-decoration-none">
                                                <i class="fas fa-external-link-alt mr-1"></i> {{ $banner->link }}
                                            </a>
                                        @else
                                            <span class="text-muted font-italic">Không có</span>
                                        @endif
                                    </td>
                                </tr>

                                <tr>
                                    <th class="bg-light">Vị trí</th>
                                    <td>
                                        <span class="badge badge-info px-2 py-1">{{ ucfirst($banner->position) }}</span>
                                    </td>
                                </tr>

                                <tr>
                                    <th class="bg-light">Thứ tự sắp xếp</th>
                                    <td class="font-weight-bold">{{ $banner->sort_order }}</td>
                                </tr>

                                <tr>
                                    <th class="bg-light">Trạng thái hệ thống</th>
                                    <td>
                                        @if($banner->status)
                                            <span class="badge badge-success px-2 py-1 font-weight-bold">Hiển thị</span>
                                        @else
                                            <span class="badge badge-danger px-2 py-1 font-weight-bold">Ẩn</span>
                                        @endif
                                    </td>
                                </tr>

                                <tr>
                                    <th class="bg-light">Trạng thái thời gian</th>
                                    <td>
                                        @php
                                            $now = now();
                                        @endphp

                                        @if($banner->start_date && $now->lt($banner->start_date))
                                            <span class="badge badge-primary px-2 py-1">
                                                <i class="fas fa-clock mr-1"></i> Chưa tới lịch
                                            </span>
                                        @elseif($banner->end_date && $now->gt($banner->end_date))
                                            <span class="badge badge-danger px-2 py-1">
                                                <i class="fas fa-times-circle mr-1"></i> Hết hạn
                                            </span>
                                        @else
                                            <span class="badge badge-success px-2 py-1">
                                                <i class="fas fa-check-circle mr-1"></i> Đang diễn ra
                                            </span>
                                        @endif
                                    </td>
                                </tr>

                                <tr>
                                    <th class="bg-light">Ngày bắt đầu</th>
                                    <td>{{ $banner->start_date ? \Carbon\Carbon::parse($banner->start_date)->format('d/m/Y H:i') : 'Không giới hạn' }}</td>
                                </tr>

                                <tr>
                                    <th class="bg-light">Ngày kết thúc</th>
                                    <td>{{ $banner->end_date ? \Carbon\Carbon::parse($banner->end_date)->format('d/m/Y H:i') : 'Không giới hạn' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>

</div>

@endsection