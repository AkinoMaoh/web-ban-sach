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
                @foreach($cartItems as $item)
                    @php 
                        $isOutOfStock = ($item->variant->stock <= 0 || $item->quantity > $item->variant->stock);
                        $unitPrice = $item->variant->price;
                        $subTotal = $unitPrice * $item->quantity;
                    @endphp
                    
                    <div class="card mb-3 shadow-sm border-0 p-3 {{ $isOutOfStock ? 'border border-danger' : '' }}" style="border-radius: 12px;">
                        <div class="row align-items-center">
                            <!-- Checkbox gửi $item->id (khớp với PaymentController) -->
                            <div class="col-1 text-center">
                                <input type="checkbox" class="cart-checkbox" value="{{ $item->id }}" 
                                       data-subtotal="{{ $subTotal }}"
                                       {{ $isOutOfStock ? 'disabled' : 'checked' }} 
                                       onchange="updateTotal()">
                            </div>
                            
                            <div class="col-2 text-center">
                                <img src="{{ asset('uploads/products/' . ($item->variant->product->image ?? 'default.jpg')) }}" class="img-fluid rounded" style="max-height: 80px;">
                            </div>
                            
                            <div class="col-3">
                                <h6 class="font-weight-bold mb-1">{{ $item->variant->product->name }}</h6>
                            </div>
                            
                            <div class="col-2 text-center">
                                <small class="text-muted d-block">{{ number_format($unitPrice) }} đ x {{ $item->quantity }}</small>
                                <strong class="text-dark">{{ number_format($subTotal) }} đ</strong>
                            </div>
                            
                            <div class="col-3">
                                <!-- Truyền product_variant_id để Update đúng -->
                                <select class="form-control form-control-sm auto-update" data-old-id="{{ $item->product_variant_id }}">
                                    @foreach($item->variant->product->variants as $v)
                                        <option value="{{ $v->id }}" {{ $v->id == $item->product_variant_id ? 'selected' : '' }} {{ $v->stock <= 0 ? 'disabled' : '' }}>
                                            {{ $v->edition }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-1 text-center">
                                <!-- Truyền product_variant_id để Xóa đúng -->
                                <form action="{{ route('cart.remove', $item->product_variant_id) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-link text-danger p-0" title="Xóa sản phẩm"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Tóm tắt đơn hàng -->
            <div class="col-lg-4">
                <div class="card shadow-sm border-0 p-4" style="border-radius: 15px;">
                    <h5 class="serif-font font-weight-bold mb-3">Tóm tắt đơn hàng</h5>
                    <div class="d-flex justify-content-between mb-3">
                        <span>Tổng cộng:</span>
                        <strong id="grand-total" class="text-primary" style="font-size: 1.3rem;">0 đ</strong>
                    </div>
                    
                    <form id="checkout-form" action="{{ route('checkout.index') }}" method="GET">
                        <!-- name="items" khớp hoàn toàn với Request của PaymentController -->
                        <input type="hidden" name="items" id="selected_ids">
                        <button type="submit" id="btn-checkout" class="btn btn-orange w-100 py-3 rounded-pill font-weight-bold shadow-sm">
                            Thanh toán ngay
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<style>.btn-orange { background: #D35400; color: white; border: none; }</style>
<script>
    function updateTotal() {
        let total = 0;
        let selectedIds = [];
        document.querySelectorAll('.cart-checkbox:checked').forEach(cb => {
            total += parseFloat(cb.dataset.subtotal);
            selectedIds.push(cb.value);
        });
        document.getElementById('grand-total').innerText = total.toLocaleString('vi-VN') + ' đ';
        document.getElementById('selected_ids').value = selectedIds.join(',');
        
        const btnCheckout = document.getElementById('btn-checkout');
        if (btnCheckout) btnCheckout.disabled = (selectedIds.length === 0);
    }

    document.querySelectorAll('.auto-update').forEach(el => {
        el.addEventListener('change', function() {
            let row = this.closest('.card');
            fetch("{{ route('cart.update') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({
                    old_variant_id: this.dataset.oldId,
                    product_variant_id: row.querySelector('select').value,
                    quantity: 1
                })
            }).then(() => location.reload());
        });
    });

    window.onload = updateTotal;
</script>
@endpush