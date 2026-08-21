# Hướng dẫn checkout và thanh toán

## Phạm vi đã hoàn thiện

- Giữ lại dữ liệu người nhận khi validate lỗi bằng `old()` của Laravel.
- Khôi phục bản nháp khi F5 bằng `sessionStorage` của đúng tab trình duyệt.
- Không tin phí ship, giảm giá, tổng tiền hoặc tồn kho gửi từ trình duyệt.
- Báo giá vận chuyển có chữ ký, thời hạn và dấu vân tay giỏ hàng.
- Trọng lượng GHN được cộng theo từng biến thể và số lượng.
- Mỗi lần bấm đặt hàng có UUID riêng để chống tạo đơn trùng.
- Trừ tồn kho nguyên tử khi tạo đơn, hoàn đúng một lần khi hủy/hết hạn.
- Ghi lịch sử thanh toán COD/VNPAY riêng trong bảng `payments`.
- Xác minh chữ ký, merchant, mã giao dịch và số tiền callback VNPAY.
- Có URL IPN để nhận kết quả ngay cả khi khách không quay lại website.
- Tự hủy phiên VNPAY quá hạn và trả voucher/tồn kho.
- Đơn đã trả tiền chỉ được hủy sau quy trình xác nhận hoàn tiền.
- Có nhiều địa chỉ giao hàng, lưu địa chỉ mới và đặt địa chỉ mặc định.
- Gửi email xác nhận, trang cảm ơn có tóm tắt và link theo dõi bảo mật.

## Cập nhật môi trường

Thêm các biến sau vào `.env` rồi điền thông tin thật của nhóm:

```dotenv
GHN_BASE_URL=https://online-gateway.ghn.vn/shiip/public-api/v2
GHN_API_TOKEN=
GHN_SHOP_ID=
STORE_DISTRICT_ID=
GHN_SERVICE_TYPE_ID=2
GHN_DEFAULT_FEE=30000
GHN_DEFAULT_ITEM_WEIGHT=500
GHN_QUOTE_TTL_MINUTES=15

VNP_PAYMENT_URL=https://sandbox.vnpayment.vn/paymentv2/vpcpay.html
VNP_TMN_CODE=
VNP_HASH_SECRET=
VNP_VERSION=2.1.0
VNP_COMMAND=pay
VNP_ORDER_TYPE=billpayment
VNP_LOCALE=vn
VNP_PAYMENT_EXPIRY_MINUTES=15
```

Nếu chưa có tài khoản GHN, hệ thống dùng `GHN_DEFAULT_FEE`. Nếu VNPAY chưa có mã website và secret, giao diện vẫn dùng COD nhưng chặn chọn VNPAY ở máy chủ.

## Các lệnh chạy sau khi lấy code

Trong terminal tại thư mục dự án:

```powershell
composer install
npm install
php artisan optimize:clear
php artisan migrate
php artisan migrate:status
php artisan test
npm run build
```

Khi phát triển, mở hai terminal:

```powershell
php artisan serve
```

```powershell
npm run dev
```

## Scheduler hết hạn thanh toán

Chạy thủ công để kiểm tra:

```powershell
php artisan checkout:expire-payments
```

Khi chạy local có thể mở thêm terminal:

```powershell
php artisan schedule:work
```

Khi triển khai máy chủ, cấu hình cron gọi `php artisan schedule:run` mỗi phút. Nếu scheduler không chạy, các đơn VNPAY bỏ dở sẽ không tự trả tồn kho và voucher.

## Cấu hình IPN VNPAY

Trong trang quản trị merchant VNPAY, đặt URL IPN thành:

```text
https://ten-mien-cua-ban/checkout/vnpay-ipn
```

Localhost không nhận callback từ VNPAY qua Internet. Khi test IPN local cần tunnel HTTPS hoặc chỉ kiểm tra return URL. Không đưa secret VNPAY lên GitHub.

## Quy trình hoàn tiền

Phiên bản này cố ý không tự gọi API refund VNPAY vì API hoàn tiền cần hợp đồng, tài khoản được cấp quyền và quy trình đối soát riêng của merchant.

1. Khách yêu cầu hủy đơn đã thanh toán.
2. Đơn chuyển sang `refund_status=requested`, chưa hoàn kho và chưa trả voucher.
3. Admin hoàn tiền thực tế trên hệ thống VNPAY.
4. Admin nhập mã tham chiếu và bấm **Xác nhận đã hoàn tiền**.
5. Hệ thống chuyển đơn sang đã hủy, hoàn kho, hoàn lượt voucher và lưu trạng thái giao dịch.

Không bấm xác nhận ở bước 4 nếu tiền chưa thực sự được hoàn.

## Danh sách test thủ công

### Form và bản nháp

- Nhập form, F5 và xác nhận dữ liệu vẫn còn.
- Cố tình nhập sai số điện thoại, submit và xác nhận các ô đúng không bị xóa.
- Mở tab mới và xác nhận bản nháp không tràn từ tab cũ.
- Đặt hàng thành công và xác nhận bản nháp được xóa.

### Địa chỉ và phí vận chuyển

- Chọn tỉnh, huyện, xã theo đúng thứ tự.
- Chọn một địa chỉ đã lưu và kiểm tra dữ liệu tự điền.
- Thay địa chỉ sau khi đã tính phí và xác nhận phí được tính lại.
- Sửa hidden token bằng DevTools và xác nhận máy chủ từ chối.

### Voucher

- Áp mã hợp lệ, đổi email rồi xác nhận mã bị yêu cầu áp lại.
- Đổi giỏ hàng sau khi có báo giá rồi thử submit.
- Hủy đơn COD và xác nhận lượt voucher được hoàn đúng một lần.

### Tồn kho và chống trùng

- Mở hai phiên mua sản phẩm chỉ còn một cuốn và đặt gần đồng thời.
- Nhấp nhanh nút đặt hàng hai lần và xác nhận chỉ có một `checkout_token` trong DB.
- Hủy đơn, gọi lại thao tác hủy và xác nhận tồn kho không tăng hai lần.

### VNPAY

- Thanh toán thành công và kiểm tra `orders`, `payments`, `voucher_usages`.
- Tải lại return URL và xác nhận không gửi email/thông báo lần hai.
- Hủy trên cổng thanh toán và xác nhận kho cùng voucher được trả.
- Chạy lệnh hết hạn rồi gửi callback thành công giả lập: đơn phải yêu cầu hoàn tiền, không tự giao hàng.
- Gửi callback sai chữ ký hoặc sai số tiền và xác nhận hệ thống từ chối.

## Lệnh kiểm tra nhanh trước khi tạo pull request

```powershell
git status
php artisan optimize:clear
php artisan migrate:status
php artisan test
npm run build
git log --oneline origin/main..HEAD
```

Chỉ push nhánh tính năng. Không merge hoặc push trực tiếp vào `main`.
