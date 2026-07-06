@extends('layout.user')

@section('content')
<div class="container my-5">
    <h2 class="serif-font font-weight-bold mb-4">Giỏ hàng của tôi</h2>
    
    @if(session('success'))
        <div class="alert alert-success shadow-sm mb-4">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger shadow-sm mb-4">{{ session('error') }}</div>
    @endif

    @if($cartItems->isEmpty())
        <div class="text-center py-5 shadow-sm bg-white rounded border">
            <h5>Giỏ hàng đang trống</h5>
            <a href="{{ route('user.shop') }}" class="btn btn-dark mt-3">Tiếp tục mua sắm</a>
        </div>
    @else
        <div class="row">
            <div class="col-lg-8">
                @php $grandTotal = 0; $hasOutOfStock = false; @endphp
                @foreach($cartItems as $item)
                    @php 
                        // Kiểm tra trạng thái tồn kho
                        $isOutOfStock = ($item->variant->stock <= 0 || $item->quantity > $item->variant->stock);
                        if ($isOutOfStock) $hasOutOfStock = true;
                        
                        $price = $item->variant->price ?? 0;
                        $subTotal = $price * $item->quantity;
                        $grandTotal += $subTotal;
                    @endphp
                    
                    <div class="card mb-3 shadow-sm border-0 p-3 {{ $isOutOfStock ? 'border border-danger' : '' }}" style="border-radius: 12px;">
                        <div class="row align-items-center">
                            <div class="col-2 text-center">
                                <img src="{{ asset('uploads/products/' . ($item->variant->product->image ?? 'default.jpg')) }}" class="img-fluid rounded" style="max-height: 80px;">
                            </div>
                            <div class="col-3">
                                <h6 class="font-weight-bold mb-1">{{ $item->variant->product->name ?? 'Sản phẩm đã xóa' }}</h6>
                                @if($isOutOfStock)
                                    <small class="text-danger font-weight-bold"><i class="fas fa-exclamation-triangle"></i> Hết hàng/Không đủ SL</small>
                                @endif
                            </div>
                            <div class="col-3">
                                <label class="small text-muted mb-1">Phiên bản:</label>
                                <select class="form-control form-control-sm auto-update" data-old-id="{{ $item->variant->id }}">
                                    @foreach($item->variant->product->variants as $v)
                                        <!-- Vô hiệu hóa các phiên bản hết hàng -->
                                        <option value="{{ $v->id }}" 
                                            {{ $v->id == $item->variant->id ? 'selected' : '' }} 
                                            {{ $v->stock <= 0 ? 'disabled' : '' }}>
                                            {{ $v->edition }} ({{ number_format($v->price) }} đ) {{ $v->stock <= 0 ? '- Hết hàng' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-2">
                                <label class="small text-muted mb-1">SL:</label>
                                <!-- Chặn nhập số <= 0 -->
                                <input type="number" value="{{ $item->quantity }}" min="1" 
                                       oninput="this.value = Math.max(1, this.value)"
                                       class="form-control form-control-sm auto-update" 
                                       data-old-id="{{ $item->variant->id }}">
                            </div>
                            <div class="col-2 text-center">
                                <form action="{{ route('cart.remove', $item->variant->id) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-link text-danger p-0"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm border-0 p-4" style="border-radius: 15px;">
                    <h5 class="serif-font font-weight-bold mb-3">Tóm tắt đơn hàng</h5>
                    <div class="d-flex justify-content-between mb-3">
                        <span>Tổng cộng:</span>
                        <strong class="text-primary" style="font-size: 1.3rem;">{{ number_format($grandTotal) }} đ</strong>
                    </div>
                    
                    @if($hasOutOfStock)
                        <div class="alert alert-warning small">
                            Có sản phẩm trong giỏ không khả dụng. Vui lòng chọn phiên bản khác hoặc thay đổi số lượng.
                        </div>
                        <button class="btn btn-secondary w-100 py-3 rounded-pill" disabled>
                            Không thể thanh toán
                        </button>
                    @else
                        <a href="{{ route('checkout.index') }}" class="btn btn-orange w-100 py-3 rounded-pill font-weight-bold">
                            Thanh toán ngay
                        </a>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<style>.btn-orange { background: #D35400; color: white; border: none; }</style>
<script>
    document.querySelectorAll('.auto-update').forEach(el => {
        el.addEventListener('change', function() {
            let row = this.closest('.card');
            fetch("{{ route('cart.update') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({
                    old_variant_id: this.dataset.oldId,
                    product_variant_id: row.querySelector('select').value,
                    quantity: row.querySelector('input').value
                })
            }).then(() => location.reload());
        });
    });
</script>
@endpush