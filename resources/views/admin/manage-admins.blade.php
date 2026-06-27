@extends('Admin.layout')

@section('admin_content')
<div class="container-fluid mt-4">
    <h1 class="h3 mb-4 text-gray-800"><i class="fas fa-users-cog"></i> Quản Lý & Phê Duyệt Admin</h1>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card shadow mb-4 border-left-warning">
        <div class="card-header py-3 bg-warning text-white">
            <h6 class="m-0 font-weight-bold"><i class="fas fa-user-clock"></i> DANH SÁCH ADMIN CHỜ DUYỆT ({{ $pendingAdmins->count() }})</h6>
        </div>
        <div class="card-body">
            @if($pendingAdmins->isEmpty())
                <div class="text-center py-3 text-muted">
                    <i class="fas fa-user-check fa-2x mb-2"></i>
                    <p class="mb-0">Hiện tại không có tài khoản Admin nào đang chờ duyệt.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>STT</th>
                                <th>Họ và Tên</th>
                                <th>Email Đăng Ký</th>
                                <th>Ngày Đăng Ký</th>
                                <th>Trạng Thái</th>
                                <th class="text-center">Hành Động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendingAdmins as $key => $admin)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td class="font-weight-bold text-gray-800">{{ $admin->name }}</td>
                                    <td>{{ $admin->email }}</td>
                                    <td>{{ $admin->created_at->format('d/m/Y H:i') }}</td>
                                    <td><span class="badge badge-warning p-2">Chờ duyệt</span></td>
                                    <td class="text-center">
                                        <form action="{{ route('admin.approve', $admin->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm font-weight-bold" onclick="return confirm('Bạn có chắc muốn cấp quyền truy cập cho Admin này?')">
                                                <i class="fas fa-check"></i> Duyệt
                                            </button>
                                        </form>

                                        <form action="{{ route('admin.reject', $admin->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm font-weight-bold" onclick="return confirm('Bạn có chắc muốn từ chối và xóa yêu cầu đăng ký này?')">
                                                <i class="fas fa-trash-alt"></i> Từ Chối
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <div class="card shadow mb-4 border-left-success">
        <div class="card-header py-3 bg-success text-white">
            <h6 class="m-0 font-weight-bold"><i class="fas fa-user-shield"></i> CÁC ADMIN KHÁC TRÊN HỆ THỐNG</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered text-muted" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>STT</th>
                            <th>Họ và Tên</th>
                            <th>Email</th>
                            <th>Ngày Kích Hoạt</th>
                            <th>Trạng Thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($activeAdmins as $key => $admin)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td class="font-weight-bold text-success">{{ $admin->name }}</td>
                                <td>{{ $admin->email }}</td>
                                <td>{{ $admin->updated_at->format('d/m/Y H:i') }}</td>
                                <td><span class="badge badge-success p-2">Đang hoạt động</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-3">Hệ thống chưa có Admin phụ nào khác ngoài bạn.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection