@extends('admin.layout')

@section('admin_content')

<style>
    /* CSS cho hộp gợi ý Autocomplete */
    .search-container {
        position: relative;
    }
    .suggestion-box {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        z-index: 1050;
        background: #fff;
        border: 1px solid #e3e6f0;
        border-radius: 0 0 8px 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        display: none;
        max-height: 280px;
        overflow-y: auto;
    }
    .suggestion-item {
        padding: 10px 15px;
        cursor: pointer;
        border-bottom: 1px solid #f8f9fc;
        transition: background 0.2s;
    }
    .suggestion-item:last-child {
        border-bottom: none;
    }
    .suggestion-item:hover {
        background-color: #f1f4f9;
    }
</style>

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
        
        <div class="card-header py-3 bg-white d-flex align-items-center justify-content-between flex-wrap">
            <h6 class="m-0 font-weight-bold text-primary mb-2 mb-md-0">
                <i class="fas fa-list mr-2"></i> Dữ liệu khách hàng đăng ký
            </h6>

            <!-- Form tìm kiếm -->
            <div class="search-container ml-auto" style="width: 320px;">
                <form action="{{ route('admin.users.index') }}" method="GET" class="form-inline" id="search-form">
                    <div class="input-group w-100">
                        <input type="text" name="keyword" id="search-input" class="form-control" 
                               placeholder="Tìm theo tên, email, SĐT..." 
                               value="{{ request('keyword') }}" autocomplete="off">
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="submit" title="Tìm kiếm">
                                <i class="fas fa-search"></i>
                            </button>
                            @if(request('keyword'))
                                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary" title="Xóa tìm kiếm">
                                    <i class="fas fa-times"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </form>

                <!-- Hộp chứa danh sách gợi ý -->
                <div id="suggestion-box" class="suggestion-box"></div>
            </div>
        </div>
        
        <div class="card-body px-0 pb-0">
            
            {{-- Thông báo --}}
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
                            <th class="py-3 text-center pr-4" width="13%">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $key => $user)
                            <tr>
                                <td class="pl-4 font-weight-bold text-primary">{{ $users->firstItem() + $key }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
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
                                <td class="text-muted small">
                                    <a href="{{ route('admin.users.show', $user->id) }}" 
                                       class="btn btn-sm btn-info text-white mr-1" 
                                       title="Chi tiết">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa tài khoản khách hàng này?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger text-white shadow-sm" title="Xóa tài khoản">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-5">
                                    <i class="fas fa-users-slash fa-2x text-gray-300 mb-2"></i>
                                    @if(request('keyword'))
                                        <p class="mb-0">Không tìm thấy khách hàng nào với từ khóa "<strong>{{ request('keyword') }}</strong>".</p>
                                    @else
                                        <p class="mb-0">Hệ thống hiện tại chưa có dữ liệu người dùng thường nào đăng ký.</p>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Phân trang --}}
            <div class="d-flex justify-content-center mt-4 pb-3">
                {{ $users->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>

        </div>
    </div>
</div>

<!-- JavaScript xử lý Gợi ý Autocomplete -->
<script>
    (function() {
        function initAutocomplete() {
            const searchInput = document.getElementById('search-input');
            const suggestionBox = document.getElementById('suggestion-box');
            let timeout = null;

            if (!searchInput || !suggestionBox) return;

            // 1. Khi nhập từ khóa
            searchInput.addEventListener('input', function () {
                clearTimeout(timeout);
                const query = this.value.trim();

                if (query.length < 1) {
                    suggestionBox.style.display = 'none';
                    suggestionBox.innerHTML = '';
                    return;
                }

                timeout = setTimeout(() => {
                    fetch(`{{ route('admin.users.autocomplete') }}?query=${encodeURIComponent(query)}`)
                        .then(response => response.json())
                        .then(data => {
                            suggestionBox.innerHTML = '';
                            if (Array.isArray(data) && data.length > 0) {
                                data.forEach(user => {
                                    const item = document.createElement('div');
                                    item.className = 'suggestion-item';
                                    item.innerHTML = `
                                        <div class="font-weight-bold text-dark small">${user.name}</div>
                                        <div class="text-muted small">${user.email} ${user.phone ? ' - ' + user.phone : ''}</div>
                                    `;
                                    
                                    // Khi bấm vào item -> Chuyển thẳng đến đường dẫn tìm kiếm
                                    item.addEventListener('mousedown', function (e) {
                                        e.preventDefault();
                                        const searchUrl = `{{ route('admin.users.index') }}?keyword=${encodeURIComponent(user.name)}`;
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

            // 2. Click ra ngoài thì đóng hộp gợi ý
            document.addEventListener('click', function (e) {
                if (!searchInput.contains(e.target) && !suggestionBox.contains(e.target)) {
                    suggestionBox.style.display = 'none';
                }
            });
        }

        // Đảm bảo chạy cả khi trang nạp xong hoặc khi xài Turbolinks/PJAX
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initAutocomplete);
        } else {
            initAutocomplete();
        }
    })();
</script>

@endsection