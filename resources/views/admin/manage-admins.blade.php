@extends('admin.layout')

@section('admin_content')
<style>
    .suggestion-item {
        padding: 8px 12px;
        cursor: pointer;
        border-bottom: 1px solid #f1f1f1;
    }
    .suggestion-item:last-child {
        border-bottom: none;
    }
    .suggestion-item:hover {
        background-color: #eaecf4;
    }
</style>
<div class="container-fluid">

    <!-- Tiêu đề trang -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold"><i class="fas fa-users-cog mr-2"></i> Quản Lý & Phê Duyệt nhân viên</h1>
    </div>

    <!-- Thông báo thành công -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mx-0" role="alert">
            <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    @endif

    <!-- Thông báo lỗi -->
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mx-0" role="alert">
            <i class="fas fa-exclamation-circle mr-1"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    @endif

    <!-- 1. DANH SÁCH NHÂN VIÊN CHỜ DUYỆT (is_active = 0) -->
    <div class="card shadow mb-4 border-0 rounded-lg border-left-warning">
        <div class="card-header py-3 bg-white d-flex align-items-center">
            <h6 class="m-0 font-weight-bold text-warning">
                <i class="fas fa-user-clock mr-2"></i> DANH SÁCH NHÂN VIÊN CHỜ DUYỆT ({{ $pendingAdmins->count() }})
            </h6>
        </div>

        <div class="card-body px-0 pb-0">
            @if($pendingAdmins->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-user-check fa-2x mb-2 text-gray-300"></i>
                    <p class="mb-0">Hiện tại không có tài khoản Nhân viên nào đang chờ duyệt.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-uppercase font-weight-bold text-dark" style="font-size: 0.85rem;">
                            <tr>
                                <th class="py-3 pl-4" width="8%">STT</th>
                                <th class="py-3" width="25%">Họ và Tên</th>
                                <th class="py-3" width="27%">Email</th>
                                <th class="py-3" width="18%">Ngày Đăng Ký</th>
                                <th class="py-3 text-center" width="12%">Trạng Thái</th>
                                <th class="py-3 text-center pr-4" width="10%">Hành Động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendingAdmins as $key => $admin)
                                <tr>
                                    <td class="pl-4 font-weight-bold text-warning">{{ $key + 1 }}</td>
                                    <td><span class="font-weight-bold text-dark">{{ $admin->name }}</span></td>
                                    <td class="text-muted">{{ $admin->email }}</td>
                                    <td class="text-muted small">{{ $admin->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="text-center">
                                        <span class="badge badge-warning text-white px-2 py-1">Chờ phê duyệt</span>
                                    </td>
                                    <td class="text-center pr-4">
                                        <div class="d-inline-flex align-items-center justify-content-center">
                                            <!-- Nút Duyệt -->
                                            <form action="{{ route('admin.approve', $admin->id) }}" method="POST" class="d-inline mr-1">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success text-white" onclick="return confirm('Xác nhận duyệt tài khoản này?')" title="Phê duyệt">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>

                                            <!-- Nút Từ Chối -->
                                            <form action="{{ route('admin.reject', $admin->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger text-white" onclick="return confirm('Bạn có chắc muốn từ chối và xóa tài khoản này?')" title="Từ chối">
                                                    <i class="fas fa-times"></i>
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
<!-- Khung Tìm Kiếm có Autocomplete -->
<div class="d-flex justify-content-end mb-3">
    <form action="{{ route('admin.manage') }}" method="GET" class="form-inline">
        <!-- Bổ sung position-relative để hộp gợi ý định vị chính xác -->
        <div class="input-group position-relative" style="width: 320px;">
            <!-- Đã thêm id="search-input" -->
            <input type="text" 
                   id="search-input" 
                   name="keyword" 
                   class="form-control" 
                   placeholder="Tìm theo tên, email" 
                   value="{{ request('keyword') }}"
                   autocomplete="off">

            <div class="input-group-append">
                <button class="btn btn-primary" type="submit">
                    <i class="fas fa-search"></i> Tìm kiếm
                </button>
                @if(request('keyword'))
                    <a href="{{ route('admin.manage') }}" class="btn btn-outline-secondary" title="Xóa lọc">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>

            <!-- Đã thêm khung chứa danh sách gợi ý id="suggestion-box" -->
            <div id="suggestion-box" 
                 class="dropdown-menu w-100 shadow-sm" 
                 style="display: none; position: absolute; top: 100%; left: 0; z-index: 1000; max-height: 250px; overflow-y: auto;">
            </div>
        </div>
    </form>
</div>
    <!-- 2. CÁC NHÂN VIÊN KHÁC TRÊN HỆ THỐNG (is_active = 1 hoặc 2) -->
    <div class="card shadow mb-4 border-0 rounded-lg border-left-success">
        <div class="card-header py-3 bg-white d-flex align-items-center">
            <h6 class="m-0 font-weight-bold text-success">
                <i class="fas fa-user-shield mr-2"></i> CÁC NHÂN VIÊN KHÁC TRÊN HỆ THỐNG
            </h6>
        </div>

        <div class="card-body px-0 pb-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" width="100%" cellspacing="0">
                    <thead class="bg-light text-uppercase font-weight-bold text-dark" style="font-size: 0.85rem;">
                        <tr>
                            <th class="py-3 pl-4" width="8%">STT</th>
                            <th class="py-3" width="25%">Họ và Tên</th>
                            <th class="py-3" width="27%">Email</th>
                            <th class="py-3" width="18%">Ngày Kích Hoạt</th>
                            <th class="py-3 text-center" width="12%">Trạng Thái</th>
                            <th class="py-3 text-center pr-4" width="10%">Hành Động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($activeAdmins as $key => $admin)
                            <tr>
                                <td class="pl-4 font-weight-bold text-success">{{ $key + 1 }}</td>
                                <td><span class="font-weight-bold text-dark">{{ $admin->name }}</span></td>
                                <td class="text-muted">{{ $admin->email }}</td>
                                <td class="text-muted small">{{ $admin->updated_at->format('d/m/Y H:i') }}</td>
                                
                                <!-- HIỂN THỊ BADGE TRẠNG THÁI -->
                                <td class="text-center">
                                    @if((int)$admin->is_active === 2)
                                        <span class="badge badge-danger px-2 py-1">Đã bị khóa</span>
                                    @else
                                        <span class="badge badge-success px-2 py-1">Đang hoạt động</span>
                                    @endif
                                </td>

                                <!-- HIỂN THỊ NÚT BẤM VÀ ICON -->
                                <td class="text-center pr-4">
                                    <div class="d-inline-flex align-items-center justify-content-center">
                                        @if((int)$admin->is_active === 2)
                                            <!-- Nếu ĐANG KHÓA (2) -> Nút màu XANH + Icon Ổ KHÓA MỞ -->
                                            <form action="{{ route('admin.toggle-status', $admin->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-success text-white" onclick="return confirm('Bạn có chắc muốn MỞ KHÓA tài khoản này?')" title="Mở khóa tài khoản">
                                                    <i class="fas fa-unlock"></i>
                                                </button>
                                            </form>
                                        @else
                                            <!-- Nếu ĐANG HOẠT ĐỘNG (1) -> Nút màu ĐỎ + Icon Ổ KHÓA ĐÓNG -->
                                            <form action="{{ route('admin.toggle-status', $admin->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-danger text-white" onclick="return confirm('Bạn có chắc muốn KHÓA tài khoản nhân viên này?')" title="Khóa tài khoản">
                                                    <i class="fas fa-lock"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">Hệ thống chưa có Admin phụ nào khác ngoài bạn.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
<script>
(function() {
    function initAdminAutocomplete() {
        const searchInput = document.getElementById('search-input');
        const suggestionBox = document.getElementById('suggestion-box');
        let timeout = null;

        if (!searchInput || !suggestionBox) return;

        searchInput.addEventListener('input', function () {
            clearTimeout(timeout);
            const query = this.value.trim();

            if (query.length < 1) {
                suggestionBox.style.display = 'none';
                suggestionBox.innerHTML = '';
                return;
            }

            timeout = setTimeout(() => {
                fetch(`{{ route('admin.manage-admins.autocomplete') }}?query=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        suggestionBox.innerHTML = '';
                        if (Array.isArray(data) && data.length > 0) {
                            data.forEach(admin => {
                                const item = document.createElement('div');
                                item.className = 'suggestion-item';
                                const statusBadge = admin.status == 1 
                                    ? '<span class="badge badge-success float-right">Hoạt động</span>' 
                                    : '<span class="badge badge-warning float-right">Chờ duyệt</span>';

                                item.innerHTML = `
                                    <div class="font-weight-bold text-dark small">${admin.name} ${statusBadge}</div>
                                    <div class="text-muted small">${admin.email} ${admin.phone ? ' - ' + admin.phone : ''}</div>
                                `;
                                
                                item.addEventListener('mousedown', function (e) {
                                    e.preventDefault();
                                    const searchUrl = `{{ route('admin.manage') }}?keyword=${encodeURIComponent(admin.name)}`;
                                    window.location.href = searchUrl;
                                });
                                
                                suggestionBox.appendChild(item);
                            });
                            suggestionBox.style.display = 'block';
                        } else {
                            suggestionBox.style.display = 'none';
                        }
                    })
                    .catch(error => {
                        console.error('Lỗi gợi ý:', error);
                        suggestionBox.style.display = 'none';
                    });
            }, 300);
        });

        document.addEventListener('click', function (e) {
            if (!searchInput.contains(e.target) && !suggestionBox.contains(e.target)) {
                suggestionBox.style.display = 'none';
            }
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAdminAutocomplete);
    } else {
        initAdminAutocomplete();
    }
})();
</script>
@endsection