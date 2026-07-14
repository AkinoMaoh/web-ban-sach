@extends('admin.layout')

@section('admin_content')
<div class="container-fluid py-4">
    <div class="card border-0 shadow-sm" style="border-radius: 12px; background-color: #232323;">
        <div class="card-header custom-card-header py-3 d-flex align-items-center justify-content-between">
            <h5 class="font-weight-bold text-dark mb-0">
                <i class="fas fa-users mr-2 text-primary"></i> Quản Lý Người Dùng
            </h5>
            <span class="badge badge-primary px-3 py-2" style="border-radius: 20px;">
                Tổng số: {{ $users->total() }} khách hàng
            </span>
        </div>
        
        <div class="card-body">
            {{-- Khối hiển thị thông báo phản hồi --}}
            @if(session('success'))
                <div class="alert alert-success border-0 mb-3 text-white" style="border-radius: 8px; background-color: #1cc88a;">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger border-0 mb-3 text-white" style="border-radius: 8px; background-color: #e74a3b;">{{ session('error') }}</div>
            @endif

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="text-secondary">
                        <tr>
                            <th class="border-0" style="width: 5%;">#</th>
                            <th class="border-0">Tài khoản</th>
                            <th class="border-0">Email</th>
                            <th class="border-0">Số điện thoại</th>
                            <th class="border-0">Ngày đăng ký</th>
                            <th class="border-0 text-center" style="width: 15%;">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $key => $user)
                            <tr>
                                <td>{{ $users->firstItem() + $key }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        {{-- Tạo ảnh đại diện nhanh theo tên chữ cái đầu của User --}}
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=random&color=fff&size=40" class="rounded-circle mr-2" style="width: 35px; height: 35px; object-fit: cover;">
                                        <span class="font-weight-bold">{{ $user->name }}</span>
                                    </div>
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->phone ?? 'Chưa cập nhật' }}</td>
                                <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                                <td class="text-center">
                                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa tài khoản khách hàng này? Hành động này không thể hoàn tác!')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" style="border-radius: 6px; padding: 4px 12px;">
                                            <i class="fas fa-trash-alt mr-1"></i> Xóa tài khoản
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">Hệ thống hiện tại chưa có dữ liệu người dùng thường nào đăng ký.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Đẩy liên kết chuyển trang hiển thị đẹp mắt --}}
            <div class="d-flex justify-content-end mt-3 pagination">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</div>

<style>
    .table td, .table th { vertical-align: middle; }
    /* Fix css màu hover dòng bảng khi ở chế độ Dark Mode */
    html.dark-mode .table-hover tbody tr:hover { 
        background-color: #2c2c2c !important; 
    }
</style>
@endsection