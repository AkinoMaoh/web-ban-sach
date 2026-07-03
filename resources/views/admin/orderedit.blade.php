@extends('admin.layout')

@section('admin_content')
<style>
    #status option:disabled {
        color: #adb5bd;
        background-color: #f1f1f1;
        font-style: italic;
    }
</style>
<div class="container-fluid">

    <h1 class="h3 mb-4 text-gray-800">Cập nhật trạng thái đơn hàng</h1>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Đơn hàng #{{ $order->id }}</h6>
                </div>

                <div class="card-body">
                    
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.orders.update', $order->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label>Khách hàng đặt:</label>
                            <input type="text" class="form-control" value="{{ $order->shipping_name ?? $order->user->name ?? 'Null' }}" readonly disabled>
                        </div>
                        <div class="form-group">
                            <label>Email:</label>
                            <input type="text" class="form-control" value="{{$order->billing_email ?? $order->user->email ?? 'Null'  }}" readonly disabled>
                        </div>
                        <div class="form-group">
                            <label>Số điện thoại:</label>
                            <input type="text" class="form-control" value="{{  $order->shipping_phone ?? 'Null' }}" readonly disabled>
                        </div>
                        <div class="form-group">
                            <label>Địa chỉ giao hàng:</label>
                            <input type="text" class="form-control" value="{{  $order->shipping_address ?? 'Null' }}" readonly disabled>
                        </div>

                        <div class="form-group">
                            <label>Tổng tiền:</label>
                            <input type="text" class="form-control font-weight-bold text-danger" value="{{ number_format($order->total_amount, 0, ',', '.') }} VNĐ" readonly disabled>
                        </div>

                        @php
                            $statusOrder = ['pending', 'confirmed', 'shipping', 'completed'];
                            $currentIndex = array_search($order->status, $statusOrder);
                        @endphp

                        <div class="form-group">
                            <label for="status">Trạng thái đơn hàng <span class="text-danger">*</span></label>
                            <select class="form-control" name="status" id="status" required>
                                <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }} {{ $currentIndex !== false && $currentIndex > array_search('pending', $statusOrder) ? 'disabled' : '' }}>
                                    Chờ xử lý{{ $currentIndex !== false && $currentIndex > array_search('pending', $statusOrder) ? ' (không thể chọn)' : '' }}
                                </option>
                                <option value="confirmed" {{ $order->status == 'confirmed' ? 'selected' : '' }} {{ $currentIndex !== false && $currentIndex > array_search('confirmed', $statusOrder) ? 'disabled' : '' }}>
                                    Đã xác nhận{{ $currentIndex !== false && $currentIndex > array_search('confirmed', $statusOrder) ? ' (không thể chọn)' : '' }}
                                </option>
                                <option value="shipping" {{ $order->status == 'shipping' ? 'selected' : '' }} {{ $currentIndex !== false && $currentIndex > array_search('shipping', $statusOrder) ? 'disabled' : '' }}>
                                    Đang giao{{ $currentIndex !== false && $currentIndex > array_search('shipping', $statusOrder) ? ' (không thể chọn)' : '' }}
                                </option>
                                <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                                <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                            </select>
                        </div>

                        <hr>
                        
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Lưu cập nhật
                        </button>
                        
                        <a href="{{ route('admin.orders') }}" class="btn btn-secondary">
                            Hủy bỏ
                        </a>

                    </form>

                </div>
            </div>
        </div>
    </div>

</div>

@endsection