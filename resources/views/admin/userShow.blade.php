@extends('admin.layout')

@section('admin_content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>
            <i class="fas fa-user-circle mr-2"></i>
            Chi tiết người dùng
        </h3>

        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Quay lại
        </a>
    </div>

    <div class="card shadow">
        <div class="card-body">

            <div class="text-center mb-4">
                <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=random&color=fff&size=120"
                     class="rounded-circle border shadow">

                <h4 class="mt-3">{{ $user->name }}</h4>

                @if($user->role == 1)
                    <span class="badge badge-danger">Admin</span>
                @else
                    <span class="badge badge-success">Khách hàng</span>
                @endif
            </div>

            <table class="table table-bordered">
                <tr>
                    <th width="25%">ID</th>
                    <td>{{ $user->id }}</td>
                </tr>

                <tr>
                    <th>Họ và tên</th>
                    <td>{{ $user->name }}</td>
                </tr>

                <tr>
                    <th>Email</th>
                    <td>{{ $user->email }}</td>
                </tr>

                <tr>
                    <th>Số điện thoại</th>
                    <td>{{ $user->phone ?? 'Chưa cập nhật' }}</td>
                </tr>
                <tr>
                    <th>Địa chỉ</th>
                    <td>{{ $user->address ?? 'Chưa cập nhật' }}</td>
                </tr>
                <tr>
                    <th>Giới tính</th>
                    <td>
                        {{ $user->gender == 1 ? 'Nam' : ($user->gender == 0 ? 'Nữ' : 'Chưa cập nhật') }}
                    </td>
                </tr>
                <tr>
                    <th>Vai trò</th>
                    <td>
                        {{ $user->role == 1 ? 'Admin' : 'Khách hàng' }}
                    </td>
                </tr>

                <tr>
                    <th>Ngày tạo</th>
                    <td>{{ $user->created_at->format('d/m/Y H:i:s') }}</td>
                </tr>

                <tr>
                    <th>Cập nhật lần cuối</th>
                    <td>{{ $user->updated_at->format('d/m/Y H:i:s') }}</td>
                </tr>
            </table>

        </div>
    </div>

</div>

@endsection