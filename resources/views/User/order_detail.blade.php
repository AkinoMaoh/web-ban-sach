@extends('layout.user')
@include('User.header')

<style>
    /* --- CSS RIÊNG CHO TRANG CHI TIẾT ĐƠN HÀNG --- */
    .serif-font { font-family: 'Playfair Display', Georgia, serif; }
    .order-detail-section .table td, .order-detail-section .table th { vertical-align: middle; }
    .badge { font-weight: 600; letter-spacing: 0.3px; }
    .btn-outline-primary { color: var(--primary-color, #1a73e8); border-color: var(--primary-color, #1a73e8); }
    .btn-outline-primary:hover { background-color: var(--primary-color, #1a73e8); color: #fff; }
    
    /* CSS hiệu ứng chọn sao */
    .star-rating { display: flex; flex-direction: row-reverse; justify-content: center; }
    .star-rating input { display: none; }
    .star-rating label { color: #ddd; font-size: 2rem; padding: 0 0.2rem; cursor: pointer; transition: color 0.2s; }
    .star-rating input:checked ~ label, .star-rating label:hover, .star-rating label:hover ~ label { color: #ffc107; }
</style>

<div class="bg-white py-3 mb-4 shadow-sm border-bottom">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent px-0 mb-0 py-0">
                <li class="breadcrumb-item"><a href="{{ route('user.index') }}" class="text-muted"><i class="fas fa-home"></i> Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="{{ route('user.orderHistory') }}" class="text-muted">Lịch sử mua hàng</a></li>
                <li class="breadcrumb-item active" aria-current="page" style="color: var(--primary-color, #1a73e8); font-weight: 600;">Chi tiết đơn hàng #{{ $order->id }}</li>
            </ol>
        </nav>
    </div>
</div>

<section class="order-detail-section mb-5 pb-5">
    <div class="container">
        
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
            <h3 class="serif-font font-weight-bold mb-2 mb-md-0 text-dark">
                Chi tiết đơn hàng <span style="color: var(--primary-color, #1a73e8);">#{{ $order->id }}</span>
            </h3>
            <div>
                @if($order->status == 'pending')
                    <span class="badge badge-warning px-4 py-2 text-dark shadow-sm" style="font-size: 14px; border-radius: 8px;"><i class="fas fa-clock mr-1"></i> Chờ xác nhận</span>
                @elseif($order->status == 'confirmed')
                    <span class="badge badge-primary px-4 py-2 text-white shadow-sm" style="font-size: 14px; border-radius: 8px;"><i class="fas fa-check-circle mr-1"></i> Đã xác nhận</span>
                @elseif($order->status == 'shipping')
                    <span class="badge badge-info px-4 py-2 shadow-sm" style="font-size: 14px; border-radius: 8px;"><i class="fas fa-truck mr-1"></i> Đang giao</span>
                @elseif($order->status == 'completed')
                    <span class="badge badge-success px-4 py-2 shadow-sm" style="font-size: 14px; border-radius: 8px;"><i class="fas fa-check mr-1"></i> Thành công</span>
                @elseif($order->status == 'cancelled')
                    <span class="badge badge-danger px-4 py-2 shadow-sm" style="font-size: 14px; border-radius: 8px;"><i class="fas fa-times"></i> Đã hủy</span>
                @endif
            </div>
        </div>

        <div class="row">
            <!-- Cột Trái: Thông tin giao hàng -->
            <div class="col-lg-4 mb-4">
                <div class="bg-white p-4 rounded shadow-sm border h-100">
                    <h5 class="font-weight-bold border-bottom pb-3 mb-3 text-dark">Thông tin người nhận</h5>
                    <div class="mb-3">
                        <p class="mb-1 text-muted small text-uppercase font-weight-bold">Họ và tên</p>
                        <p class="font-weight-bold text-dark">{{ $order->shipping_name }}</p>
                    </div>
                    <div class="mb-3">
                        <p class="mb-1 text-muted small text-uppercase font-weight-bold">Số điện thoại</p>
                        <p class="font-weight-bold text-dark">{{ $order->shipping_phone }}</p>
                    </div>
                    <div class="mb-3">
                        <p class="mb-1 text-muted small text-uppercase font-weight-bold">Địa chỉ nhận hàng</p>
                        <p class="text-dark">{{ $order->shipping_address }}</p>
                    </div>
                    
                    <h5 class="font-weight-bold border-bottom pb-3 mt-4 mb-3 text-dark">Thanh toán</h5>
                    <div class="mb-3">
                        <p class="mb-1 text-muted small text-uppercase font-weight-bold">Phương thức</p>
                        <p class="font-weight-bold text-dark">
                            @if($order->payment_method == 'cod')
                                <i class="fas fa-money-bill-wave text-success mr-1"></i> Thanh toán khi nhận hàng (COD)
                            @elseif($order->payment_method == 'vnpay')
                                <i class="fas fa-credit-card text-info mr-1"></i> Thanh toán qua VNPAY
                            @else
                                {{ strtoupper($order->payment_method) }}
                            @endif
                        </p>
                    </div>
                    @if($order->notes)
                    <div class="mb-0">
                        <p class="mb-1 text-muted small text-uppercase font-weight-bold">Ghi chú</p>
                        <p class="text-dark bg-light p-2 rounded" style="font-size: 14px;">{{ $order->notes }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Cột Phải: Danh sách sản phẩm -->
            <div class="col-lg-8">
                <div class="bg-white rounded shadow-sm border overflow-hidden h-100">
                    <div class="p-4 border-bottom bg-light">
                        <h5 class="font-weight-bold mb-0 text-dark">Sản phẩm đã mua</h5>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-borderless mb-0 align-middle">
                            <thead class="border-bottom text-muted" style="font-size: 13px; text-transform: uppercase;">
                                <tr>
                                    <th class="py-3 pl-4">Sản phẩm</th>
                                    <th class="py-3 text-center">Đơn giá</th>
                                    <th class="py-3 text-center">Số lượng</th>
                                    <th class="py-3 text-right pr-4">Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orderDetails as $item)
                                    <tr class="border-bottom">
                                        <td class="py-3 pl-4">
                                            <div class="d-flex align-items-start">
                                                <!-- Kiểm tra xem SP còn ảnh không, nếu mất SP thì dùng ảnh placeholder -->
                                                @php
                                                    $imagePath = ($item->productVariant && $item->productVariant->product) 
                                                        ? asset('uploads/products/' . $item->productVariant->product->image) 
                                                        : asset('images/default-product.png');
                                                @endphp
                                                <img src="{{ $imagePath }}" class="rounded shadow-sm border" style="width: 65px; height: 90px; object-fit: cover;" alt="Book">
                                                
                                                <div class="ml-3 flex-grow-1">
                                                    <!-- Lấy tên và phiên bản trực tiếp từ order_details (không bị mất khi Admin xóa sản phẩm) -->
                                                    <span class="d-block font-weight-bold text-dark" style="font-size: 16px;">{{ $item->product_name }}</span>
                                                    <small class="text-muted d-block mb-3">Phiên bản: <strong class="text-dark">{{ $item->variant_name ?? 'Tiêu chuẩn' }}</strong></small>
                                                    
                                                    <!-- XỬ LÝ NÚT ĐÁNH GIÁ (Chỉ hiện khi đơn đã hoàn thành) -->
                                                    @if($order->status == 'completed')
                                                        <!-- NẾU SẢN PHẨM GỐC VẪN CÒN TỒN TẠI TRONG DB -->
                                                        @if($item->productVariant && $item->productVariant->product)
                                                            <div class="mt-2">
                                                                <button type="button" class="btn btn-outline-primary font-weight-bold px-3 py-2 shadow-sm d-inline-flex align-items-center" 
                                                                        data-toggle="modal" data-target="#reviewModal-{{ $item->id }}"
                                                                        style="border-radius: 8px; font-size: 13px; border-width: 2px;">
                                                                    <i class="fas fa-star text-warning mr-2" style="font-size: 15px;"></i> Viết đánh giá sản phẩm
                                                                </button>
                                                            </div>

                                                            <!-- Modal Đánh giá -->
                                                            <div class="modal fade" id="reviewModal-{{ $item->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                                    <div class="modal-content border-0 shadow">
                                                                        <form action="{{ route('review.store') }}" method="POST">
                                                                            @csrf
                                                                            <input type="hidden" name="product_id" value="{{ $item->productVariant->product->id }}">
                                                                            <input type="hidden" name="order_detail_id" value="{{ $item->id }}">

                                                                            <div class="modal-header bg-light border-0">
                                                                                <h5 class="modal-title font-weight-bold">Đánh giá: {{ $item->product_name }}</h5>
                                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                    <span aria-hidden="true">&times;</span>
                                                                                </button>
                                                                            </div>
                                                                            
                                                                            <div class="modal-body p-4 text-left">
                                                                                <p class="text-muted small mb-3">Phiên bản: <strong>{{ $item->variant_name ?? 'Tiêu chuẩn' }}</strong></p>
                                                                                <h6 class="font-weight-bold mb-3 text-center">Vui lòng chọn mức độ hài lòng</h6>
                                                                                
                                                                                <div class="star-rating mb-4 justify-content-center">
                                                                                    <input type="radio" id="star5_{{ $item->id }}" name="rating" value="5" required>
                                                                                    <label for="star5_{{ $item->id }}" title="5 sao"><i class="fas fa-star"></i></label>
                                                                                    <input type="radio" id="star4_{{ $item->id }}" name="rating" value="4">
                                                                                    <label for="star4_{{ $item->id }}" title="4 sao"><i class="fas fa-star"></i></label>
                                                                                    <input type="radio" id="star3_{{ $item->id }}" name="rating" value="3">
                                                                                    <label for="star3_{{ $item->id }}" title="3 sao"><i class="fas fa-star"></i></label>
                                                                                    <input type="radio" id="star2_{{ $item->id }}" name="rating" value="2">
                                                                                    <label for="star2_{{ $item->id }}" title="2 sao"><i class="fas fa-star"></i></label>
                                                                                    <input type="radio" id="star1_{{ $item->id }}" name="rating" value="1">
                                                                                    <label for="star1_{{ $item->id }}" title="1 sao"><i class="fas fa-star"></i></label>
                                                                                </div>
                                                                                
                                                                                <textarea class="form-control shadow-sm" name="comment" rows="3" placeholder="Nhận xét của bạn về chất lượng sách, đóng gói, giao hàng..." required style="resize: none;"></textarea>
                                                                            </div>
                                                                            <div class="modal-footer border-0 bg-light">
                                                                                <button type="button" class="btn btn-secondary rounded-pill px-4" data-dismiss="modal">Hủy</button>
                                                                                <button type="submit" class="btn btn-primary rounded-pill px-4">Gửi nhận xét</button>
                                                                            </div>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <!-- NẾU SẢN PHẨM ĐÃ BỊ ADMIN XÓA -->
                                                        @else
                                                            <div class="mt-2">
                                                                <span class="badge badge-secondary py-2 px-3 shadow-sm" style="font-size: 13px; border-radius: 8px;">
                                                                    <i class="fas fa-ban mr-1"></i> Sản phẩm đã ngừng kinh doanh
                                                                </span>
                                                            </div>
                                                        @endif
                                                    @endif

                                                </div>
                                            </div>
                                        </td>
                                        
                                        <!-- Cột Đơn giá -->
                                        <td class="py-3 text-center align-middle font-weight-bold text-muted">
                                            {{ number_format($item->price, 0, ',', '.') }} đ
                                        </td>
                                        
                                        <!-- Cột Số lượng -->
                                        <td class="py-3 text-center align-middle">
                                            <span class="badge badge-light border px-3 py-2 text-dark" style="font-size: 14px;">x{{ $item->quantity }}</span>
                                        </td>
                                        
                                        <!-- Cột Thành tiền -->
                                        <td class="py-3 text-right align-middle pr-4 font-weight-bold text-dark">
                                            {{ number_format($item->price * $item->quantity, 0, ',', '.') }} đ
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Phần tổng kết tiền -->
                    <div class="p-4 bg-light text-right">
                        <div class="d-flex justify-content-end mb-2">
                            <span class="text-muted mr-4">Tổng tiền sản phẩm:</span>
                            <span class="font-weight-bold text-dark" style="width: 120px;">{{ number_format($order->total_amount - $order->shipping_fee, 0, ',', '.') }} đ</span>
                        </div>
                        <div class="d-flex justify-content-end mb-3">
                            <span class="text-muted mr-4">Phí vận chuyển:</span>
                            <span class="font-weight-bold text-dark" style="width: 120px;">{{ number_format($order->shipping_fee, 0, ',', '.') }} đ</span>
                        </div>
                        <div class="d-flex justify-content-end align-items-center border-top pt-3 mt-2">
                            <span class="font-weight-bold text-dark mr-4" style="font-size: 16px;">TỔNG CỘNG:</span>
                            <span class="font-weight-bold" style="color: var(--primary-color, #1a73e8); font-size: 24px; width: 120px;">{{ number_format($order->total_amount, 0, ',', '.') }} đ</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-4">
            <!-- Nút Quay lại -->
            <a href="{{ route('user.orderHistory') }}" class="btn btn-outline-dark rounded-pill px-4 py-2 font-weight-bold mr-2">
                <i class="fas fa-arrow-left mr-2"></i> Quay lại lịch sử
            </a>

            <!-- Nút Hủy (Chỉ hiện nếu là pending) -->
            @if(in_array($order->status, ['pending']))
                <form action="{{ route('user.history.cancel', $order->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này không?');">
                    @csrf
                    <button type="submit" class="btn btn-danger rounded-pill px-4 py-2 font-weight-bold">
                        <i class="fas fa-times mr-2"></i> Hủy đơn hàng
                    </button>
                </form>
            @endif
        </div>  
    </div>
</section>

@include('User.footer')