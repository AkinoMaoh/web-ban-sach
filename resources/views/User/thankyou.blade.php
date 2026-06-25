@include('User.header')
<div class="container text-center" style="padding: 100px 0;">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div style="font-size: 80px; color: #7fad39; margin-bottom: 20px;">
                <i class="fa fa-check-circle" aria-hidden="true"></i>
            </div>

            <h2 style="font-weight: 700; margin-bottom: 20px; color: #1c1c1c;">ĐẶT HÀNG THÀNH CÔNG!</h2>

            <p style="font-size: 18px; color: #555;">
                {{ $message ?? 'Cảm ơn bạn đã mua sắm tại cửa hàng của chúng tôi.' }}
            </p>

            <div style="background-color: #f5f5f5; padding: 20px; border-radius: 8px; margin: 25px 0; font-size: 20px;">
                Mã đơn hàng của bạn là: <strong style="color: #e53637;">#{{ $orderId ?? 'N/A' }}</strong>
            </div>

            <p style="color: #777; margin-bottom: 40px;">
                Chúng tôi sẽ sớm liên hệ với bạn theo số điện thoại đã cung cấp để xác nhận đơn hàng và tiến hành giao hàng.
            </p>

            <a href="{{ route('user.index') }}" class="btn primary-btn" style="background-color: #7fad39; border-color: #7fad39; padding: 12px 30px; border-radius: 4px; font-weight: bold; color: #fff; text-decoration: none; text-transform: uppercase;">
                TIẾP TỤC MUA SẮM
            </a>
        </div>
    </div>
</div>
@include('User.footer')