@extends('admin.layout')

@section('admin_content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>Quản lý Mã Giảm Giá</h3>
        <a href="{{ route('admin.vouchers.create') }}" class="btn btn-primary">Thêm Mã Mới</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow">
        <div class="card-body">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Mã Code</th>
                        <th>Loại</th>
                        <th>Mức giảm</th>
                        <th>Đơn tối thiểu</th>
                        <th>Đã dùng / Giới hạn</th>
                        <th>Hạn dùng</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($vouchers as $item)
                    <tr>
                        <td><strong>{{ $item->code }}</strong></td>
                        <td>
                            @if($item->type == 'percent')
                                <span class="badge bg-info">Theo %</span>
                            @else
                                <span class="badge bg-success">Tiền mặt</span>
                            @endif
                        </td>
                        <td>
                            @if($item->type == 'percent')
                                {{ $item->discount_value }} % <br> 
                                <small>(Tối đa: {{ number_format($item->max_discount_value) }}đ)</small>
                            @else
                                {{ number_format($item->discount_value) }} đ
                            @endif
                        </td>
                        <td>{{ number_format($item->min_order_value) }} đ</td>
                        <td>{{ $item->used_count }} / {{ $item->usage_limit ?? '∞' }}</td>
                        <td>
                            @if($item->end_date)
                                {{ \Carbon\Carbon::parse($item->end_date)->format('d/m/Y') }}
                            @else
                                Không giới hạn
                            @endif
                        </td>
                        <td>
                            <form action="{{ route('admin.vouchers.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger">Xóa</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $vouchers->links() }}
        </div>
    </div>
</div>
@endsection