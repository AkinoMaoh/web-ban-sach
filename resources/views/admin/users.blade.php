@extends('admin.layout')

@section('admin_content')

<div class="container-fluid">

    <!-- Tiêu đề trang -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold">
            <i class="fas fa-users mr-2"></i> Quản lý người dùng
        </h1>
        <span class="badge badge-primary px-3 py-2 font-weight-bold" style="font-size: 0.9rem; border-radius: 20px;">
            Tổng số: {{ $users->total() }} khách hàng
        </span>
    </div>

    <div class="card shadow mb-4 border-0 rounded-lg">
        
        <div class="card-header py-3 bg-white d-flex align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list mr-2"></i> Dữ liệu khách hàng đăng ký
            </h6>
        </div>
        
        <div class="card-body px-0 pb-0">
            
            {{-- Khối hiển thị thông báo phản hồi --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show mx-4" role="alert">
                    <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show mx-4" role="alert">
                    <i class="fas fa-exclamation-triangle mr-1"></i> {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" width="100%" cellspacing="0">
                    <thead class="bg-light text-uppercase font-weight-bold text-dark" style="font-size: 0.82rem;">
                        <tr>
                            <th class="py-3 pl-4" width="6%">#</th>
                            <th class="py-3" width="23%">Tài khoản</th>
                            <th class="py-3" width="23%">Email</th>
                            <th class="py-3" width="15%">Số điện thoại</th>
                            <th class="py-3" width="12%">Vai trò</th>
                            <th class="py-3" width="15%">Ngày đăng ký</th>
                            <th class="py-3" width="13%">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $key => $user)
                            <tr>
                                <td class="pl-4 font-weight-bold text-primary">{{ $users->firstItem() + $key }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        {{-- Tạo ảnh đại diện nhanh theo tên chữ cái đầu của User --}}
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=random&color=fff&size=40" 
                                             class="rounded-circle mr-2 shadow-sm border" 
                                             style="width: 36px; height: 36px; object-fit: cover;">
                                        <span class="font-weight-bold text-dark">{{ $user->name }}</span>
                                    </div>
                                </td>
                                <td><span class="text-muted">{{ $user->email }}</span></td>
                                <td>
                                    @if($user->phone)
                                        <span class="font-weight-bold text-dark">{{ $user->phone }}</span>
                                    @else
                                        <span class="text-muted small font-italic">Chưa cập nhật</span>
                                    @endif
                                </td>
                       
  <td>
    @if($user->role == 1)
        <span class="badge badge-danger">Admin</span>
    @else
        <span class="badge badge-success">Khách hàng</span>
    @endif
</td>

                                <td><span class="text-muted small">{{ $user->created_at->format('d/m/Y H:i') }}</span></td>
                                <td class="text-center pr-4">
                                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa tài khoản khách hàng này? Hành động này không thể hoàn tác!')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger text-white shadow-sm" title="Xóa tài khoản">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                     <a href="{{ route('admin.users.show', $user->id) }}" 
                                           class="btn btn-sm btn-info text-white mr-1" 
                                           title="Chi tiết">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-5">
                                    <i class="fas fa-users-slash fa-2x text-gray-300 mb-2"></i>
                                    <p class="mb-0">Hệ thống hiện tại chưa có dữ liệu người dùng thường nào đăng ký.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Phân trang Bootstrap 5 chuẩn giao diện --}}
            <div class="d-flex justify-content-center mt-4 pb-3">
                {{ $users->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>

        </div>
    </div>
</div>

@endsection