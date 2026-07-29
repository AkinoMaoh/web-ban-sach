@extends('admin.layout')

@section('admin_content')

<div class="container-fluid">

    <!-- Tiêu đề trang -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold"><i class="fas fa-users-cog mr-2"></i> Quản Lý & Phê Duyệt Admin</h1>
    </div>

    <!-- Thông báo thành công -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mx-0" role="alert">
            <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    @endif

    <!-- DANH SÁCH ADMIN CHỜ DUYỆT -->
    <div class="card shadow mb-4 border-0 rounded-lg border-left-warning">
        <div class="card-header py-3 bg-white d-flex align-items-center">
            <h6 class="m-0 font-weight-bold text-warning">
                <i class="fas fa-user-clock mr-2"></i> DANH SÁCH ADMIN CHỜ DUYỆT ({{ $pendingAdmins->count() }})
            </h6>
        </div>

        <div class="card-body px-0 pb-0">
            @if($pendingAdmins->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-user-check fa-2x mb-2 text-gray-300"></i>
                    <p class="mb-0">Hiện tại không có tài khoản Admin nào đang chờ duyệt.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" width="100%" cellspacing="0">
                        <thead class="bg-light text-uppercase font-weight-bold text-dark" style="font-size: 0.85rem;">
                            <tr>
                                <th class="py-3 pl-4" width="8%">STT</th>
                                <th class="py-3" width="25%">Họ và Tên</th>
                                <th class="py-3" width="25%">Email Đăng Ký</th>
                                <th class="py-3" width="18%">Ngày Đăng Ký</th>
                                <th class="py-3 text-center" width="12%">Trạng Thái</th>
                                <th class="py-3 text-center pr-4" width="12%">Hành Động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendingAdmins as $key => $admin)
                                <tr>
                                    <td class="pl-4 font-weight-bold text-primary">{{ $key + 1 }}</td>
                                    <td><span class="font-weight-bold text-dark">{{ $admin->name }}</span></td>
                                    <td class="text-muted">{{ $admin->email }}</td>
                                    <td class="text-muted small">{{ $admin->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="text-center"><span class="badge badge-warning px-2 py-1">Chờ duyệt</span></td>
                                    <td class="text-center pr-4">
                                        <div class="d-inline-flex align-items-center justify-content-center">
                                            <!-- Nút Duyệt -->
                                            <form action="{{ route('admin.approve', $admin->id) }}" method="POST" class="d-inline mr-1">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success text-white" onclick="return confirm('Bạn có chắc muốn cấp quyền truy cập cho Admin này?')" title="Duyệt">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>

                                            <!-- Nút Từ chối -->
                                            <form action="{{ route('admin.reject', $admin->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger text-white" onclick="return confirm('Bạn có chắc muốn từ chối và xóa yêu cầu đăng ký này?')" title="Từ chối">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <!-- CÁC ADMIN KHÁC TRÊN HỆ THỐNG -->
    <div class="card shadow mb-4 border-0 rounded-lg border-left-success">
        <div class="card-header py-3 bg-white d-flex align-items-center">
            <h6 class="m-0 font-weight-bold text-success">
                <i class="fas fa-user-shield mr-2"></i> CÁC ADMIN KHÁC TRÊN HỆ THỐNG
            </h6>
        </div>

        <div class="card-body px-0 pb-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" width="100%" cellspacing="0">
                    <thead class="bg-light text-uppercase font-weight-bold text-dark" style="font-size: 0.85rem;">
                        <tr>
                            <th class="py-3 pl-4" width="8%">STT</th>
                            <th class="py-3" width="30%">Họ và Tên</th>
                            <th class="py-3" width="32%">Email</th>
                            <th class="py-3" width="20%">Ngày Kích Hoạt</th>
                            <th class="py-3 text-center pr-4" width="10%">Trạng Thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($activeAdmins as $key => $admin)
                            <tr>
                                <td class="pl-4 font-weight-bold text-success">{{ $key + 1 }}</td>
                                <td><span class="font-weight-bold text-dark">{{ $admin->name }}</span></td>
                                <td class="text-muted">{{ $admin->email }}</td>
                                <td class="text-muted small">{{ $admin->updated_at->format('d/m/Y H:i') }}</td>
                                <td class="text-center pr-4"><span class="badge badge-success px-2 py-1">Đang hoạt động</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">Hệ thống chưa có Admin phụ nào khác ngoài bạn.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

@endsection