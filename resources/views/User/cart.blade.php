@extends('layout.user')

@section('content')
<div class="bg-white py-3 mb-4 shadow-sm border-bottom">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent px-0 mb-0 py-0">
                <li class="breadcrumb-item"><a href="{{ route('user.index') }}" class="text-muted"><i class="fas fa-home"></i> Trang chủ</a></li>
                <li class="breadcrumb-item active" aria-current="page" style="color: var(--primary-color); font-weight: 600;">Giỏ hàng</li>
            </ol>
        </nav>
    </div>
</div>
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
                    <!-- KIỂM TRA SẢN PHẨM HOẶC BIẾN THỂ CÓ BỊ XÓA KHÔNG -->
                    @if(!$item->variant || !$item->variant->product)
                        <!-- BOX SẢN PHẨM BỊ LỖI / XÓA -->
                        <div class="card mb-3 shadow-sm border-0 p-3" style="border-radius: 12px; background-color: #fcfcfc; border: 1px dashed #e0e0e0 !important;">
                            <div class="row align-items-center">
                                <div class="col-1 text-center" style="opacity: 0.5;">
                                    <input type="checkbox" class="cart-checkbox" disabled>
                                </div>
                                <div class="col-2 text-center" style="opacity: 0.5;">
                                    <div class="bg-light d-flex align-items-center justify-content-center rounded mx-auto" style="height: 80px; width: 80px; border: 1px solid #ccc;">
                                        <i class="fas fa-image text-muted fa-2x"></i>
                                    </div>
                                </div>
                                <div class="col-3" style="opacity: 0.5;">
                                    <h6 class="font-weight-bold mb-1 text-muted"><del>Sản phẩm không tồn tại</del></h6>
                                    <small class="text-danger font-weight-bold">Sản phẩm đã bị xóa hoặc ngừng bán</small>
                                </div>
                                <div class="col-2 text-center" style="opacity: 0.5;">
                                    <strong class="text-muted">- đ</strong>
                                </div>
                                <div class="col-3" style="opacity: 0.5;">
                                    <select class="form-control form-control-sm" disabled>
                                        <option>Không khả dụng</option>
                                    </select>
                                    <input type="number" value="{{ $item->quantity }}" class="form-control form-control-sm mt-2" disabled>
                                </div>
                                <div class="col-1 text-center">
                                    <form action="{{ route('cart.remove', $item->product_variant_id) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-link text-danger p-0" title="Xóa sản phẩm">
                                            <i class="fas fa-trash fa-lg"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- SẢN PHẨM BÌNH THƯỜNG -->
                        @php 
                            $isOutOfStock = ($item->variant->stock <= 0 || $item->quantity > $item->variant->stock);
                            
                            $variant = $item->variant;
                            $unitPrice = ($variant->sale_price > 0 && $variant->sale_price < $variant->price) 
                                ? $variant->sale_price 
                                : $variant->price;

                            $subTotal = $unitPrice * $item->quantity;
                        @endphp
                        
                        <div class="card mb-3 shadow-sm border-0 p-3 {{ $isOutOfStock ? 'border border-danger' : '' }}" style="border-radius: 12px;">
                            <div class="row align-items-center">
                                <!-- Checkbox -->
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
                                    <small class="text-muted d-block">Phân loại: {{ $item->variant->edition }}</small>
                                    
                                    <!-- HIỂN THỊ CẢNH BÁO NẾU SỐ LƯỢNG TRONG GIỎ VƯỢT QUÁ KHO -->
                                    @if($item->quantity > $item->variant->stock)
                                        <small class="text-danger font-weight-bold d-block mt-1">
                                            <i class="fas fa-exclamation-triangle"></i> Vượt quá kho (Còn {{ $item->variant->stock }})!
                                        </small>
                                    @endif
                                </div>
                                
                                <div class="col-2 text-center">
                                    @if($variant->sale_price > 0 && $variant->sale_price < $variant->price)
                                        <small class="text-muted d-block"><del>{{ number_format($variant->price) }} đ</del></small>
                                        <small class="text-danger font-weight-bold d-block">{{ number_format($unitPrice) }} đ x {{ $item->quantity }}</small>
                                    @else
                                        <small class="text-muted d-block">{{ number_format($unitPrice) }} đ x {{ $item->quantity }}</small>
                                    @endif
                                    <strong class="text-dark">{{ number_format($subTotal) }} đ</strong>
                                </div>
                                
                                <!-- Phần Chọn biến thể & Nhập số lượng -->
                                <div class="col-3">
                                    <select class="form-control form-control-sm auto-update"
                                            data-old-id="{{ $item->product_variant_id }}">
                                        @foreach($item->variant->product->variants as $v)
                                            <option
                                                value="{{ $v->id }}"
                                                {{ $v->id == $item->product_variant_id ? 'selected' : '' }}
                                                {{ $v->stock <= 0 ? 'disabled' : '' }}
                                            >
                                                {{ $v->variant->name }}
                                            </option>
                                        @endforeach
                                    </select>

                                    <!-- Ô nhập số lượng -->
                                    <input
                                        type="number"
                                        value="{{ $item->quantity }}"
                                        min="1"
                                        max="{{ $item->variant->stock }}"
                                        class="form-control form-control-sm auto-update mt-2 {{ $item->quantity > $item->variant->stock ? 'border-danger text-danger' : '' }}"
                                        data-old-id="{{ $item->product_variant_id }}"
                                    >
                                </div>

                                <div class="col-1 text-center">
                                    <form action="{{ route('cart.remove', $item->product_variant_id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-link text-danger p-0" title="Xóa sản phẩm">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endif
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
<!-- Thư viện Toastr Notification -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<style>.btn-orange { background: #D35400; color: white; border: none; }</style>
<script>
    toastr.options = { 
        "positionClass": "toast-bottom-right", 
        "timeOut": "5000",
        "closeButton": true 
    };

    function updateTotal() {
        let total = 0;
        let selectedIds = [];
        document.querySelectorAll('.cart-checkbox:checked').forEach(cb => {
            if (!cb.disabled) {
                total += parseFloat(cb.dataset.subtotal);
                selectedIds.push(cb.value);
            }
        });
        document.getElementById('grand-total').innerText = total.toLocaleString('vi-VN') + ' đ';
        document.getElementById('selected_ids').value = selectedIds.join(',');
        
        const btnCheckout = document.getElementById('btn-checkout');
        if (btnCheckout) btnCheckout.disabled = (selectedIds.length === 0);
    }

    // Tự động bật thông báo cảnh báo ngay khi load trang nếu có sản phẩm vượt quá tồn kho
    window.onload = function() {
        updateTotal();

        @foreach($cartItems as $item)
            @if($item->variant && $item->variant->product && $item->quantity > $item->variant->stock)
                toastr.warning("Sản phẩm '{{ $item->variant->product->name }}' trong giỏ hàng ({{ $item->quantity }}) đang vượt quá số lượng tồn kho thực tế (Còn {{ $item->variant->stock }}). Vui lòng giảm số lượng!");
            @endif
        @endforeach
    };

    document.querySelectorAll('.auto-update').forEach(el => {
        el.addEventListener('change', function() {
            let row = this.closest('.card');
            
            let variantSelect = row.querySelector('select');
            let qtyInput = row.querySelector('input[type="number"]');
            
            let variantVal = variantSelect.value;
            let qtyVal = parseInt(qtyInput.value) || 1;
            let maxStock = parseInt(qtyInput.getAttribute('max')) || 1;
            
            let isOverStock = false;

            if (this.tagName.toLowerCase() === 'input') {
                if (qtyVal > maxStock) {
                    qtyVal = maxStock;
                    qtyInput.value = maxStock;
                    toastr.warning(`Sản phẩm này hiện chỉ còn ${maxStock} quyển trong kho!`);
                    isOverStock = true;
                } else if (qtyVal < 1) {
                    qtyVal = 1;
                    qtyInput.value = 1;
                }
            }
            
            let delayTime = isOverStock ? 1500 : 0;

            setTimeout(() => {
                fetch("{{ route('cart.update') }}", {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({
                        old_variant_id: this.dataset.oldId,
                        product_variant_id: variantVal,
                        quantity: qtyVal
                    })
                }).then(() => location.reload());
            }, delayTime);
        });
    });
</script>
@endpush