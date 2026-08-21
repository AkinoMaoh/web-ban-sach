# Chức năng voucher hoàn chỉnh

## Phạm vi đã triển khai

- Admin tạo, xem, sửa, lọc, bật/tắt, lưu trữ và khôi phục voucher.
- Hỗ trợ giảm số tiền cố định hoặc phần trăm có mức giảm tối đa.
- Điều kiện ngày bắt đầu/kết thúc, đơn tối thiểu, tổng lượt dùng và lượt dùng mỗi khách.
- Có thể ẩn voucher khỏi danh sách checkout nhưng khách vẫn nhập được mã chiến dịch riêng.
- Checkout luôn lấy lại giá và sản phẩm từ database/session; không tin `total_order` từ JavaScript.
- Mỗi đơn lưu snapshot `voucher_code`, tạm tính và số tiền giảm để sửa voucher sau này không đổi đơn cũ.
- Mỗi lượt voucher có trạng thái `reserved`, `used` hoặc `released`.
- COD giữ lượt khi đặt đơn, đánh dấu đã dùng khi đơn hoàn thành và hoàn lượt khi hủy.
- VNPAY kiểm tra chữ ký, mã đơn và số tiền; thất bại sẽ hủy đơn và hoàn lượt voucher.
- Admin có trang lịch sử sử dụng voucher theo từng đơn hàng.

## Ba migration mới

Chạy theo đúng thứ tự sau:

```powershell
php artisan migrate --path=database/migrations/2026_08_21_120000_add_management_fields_to_vouchers_table.php
php artisan migrate --path=database/migrations/2026_08_21_120100_create_voucher_usages_table.php
php artisan migrate --path=database/migrations/2026_08_21_120200_add_voucher_snapshot_to_orders_table.php
```

Dự án hiện có các migration tạo bảng gốc `2026_01_01_...` đang hiện `Pending` dù các bảng đã có trong file SQL. Vì vậy trên database hiện tại **không chạy `migrate:fresh`** và tạm thời không chạy `php artisan migrate` chung. Ba lệnh `--path` phía trên chỉ bổ sung phần voucher mới, không xóa code hay dữ liệu cũ.

Sau khi chạy, kiểm tra riêng từng migration:

```powershell
php artisan migrate:status --path=database/migrations/2026_08_21_120000_add_management_fields_to_vouchers_table.php
php artisan migrate:status --path=database/migrations/2026_08_21_120100_create_voucher_usages_table.php
php artisan migrate:status --path=database/migrations/2026_08_21_120200_add_voucher_snapshot_to_orders_table.php
```

## Lệnh khởi động sau khi nhận code

Tại thư mục `web-ban-sach`:

```powershell
composer install
npm install
php artisan optimize:clear
composer dump-autoload
```

Terminal thứ nhất:

```powershell
php artisan serve
```

Terminal thứ hai:

```powershell
npm run dev
```

Thông báo Xdebug không tải được DLL không phải lỗi voucher. Có thể tắt dòng `zend_extension=xdebug...` trong `php.ini` nếu không dùng Xdebug.

## Kiểm thử tự động

```powershell
php artisan test tests/Unit/VoucherDiscountTest.php
php artisan test tests/Feature/VoucherServiceTest.php
```

Test feature tự tạo ba bảng tạm trong SQLite nên không chạy nhóm migration gốc đang bị trùng.

## Checklist kiểm thử thủ công

1. Tạo voucher tiền cố định, đơn tối thiểu thấp hơn giá trị giỏ và bật công khai.
2. Tạo voucher phần trăm, nhập mức tối đa và xác nhận tiền giảm không vượt mức đó.
3. Thử mã chưa bắt đầu, hết hạn, bị tắt và hết lượt; hệ thống phải báo đúng lý do.
4. Thử giỏ chưa đủ đơn tối thiểu; modal phải khóa nút chọn và API vẫn phải từ chối nếu gọi trực tiếp.
5. Khách vãng lai nhập email, áp voucher giới hạn một lượt, đặt COD và kiểm tra `voucher_usages` ở trạng thái `reserved`.
6. Hủy đơn COD; lượt phải chuyển thành `released` và `used_count` giảm đúng một lần.
7. Hoàn thành đơn COD; lượt phải chuyển thành `used`.
8. Thanh toán VNPAY thành công; `payment_status` thành `paid`, voucher thành `used` và đơn vẫn ở `pending` để admin duyệt kho theo quy trình hiện tại.
9. Hủy/thất bại VNPAY; đơn thành `cancelled`, `payment_status` thành `failed` và voucher được hoàn lượt.
10. Mở chi tiết đơn ở cả user/admin; kiểm tra đủ tạm tính, voucher, phí vận chuyển và tổng cộng.
11. Sửa mức giảm của voucher; đơn cũ phải giữ nguyên snapshot số tiền giảm.
12. Lưu trữ voucher đã có lịch sử; lịch sử không được mất, sau đó thử khôi phục.

## Quy trình Git dành cho thành viên

Trước khi đẩy nhánh:

```powershell
git status
git log --oneline main..HEAD
php artisan test tests/Unit/VoucherDiscountTest.php
php artisan test tests/Feature/VoucherServiceTest.php
git push -u origin feature/cap-nhat-voucher-lan-2
```

Sau đó mở Pull Request từ `feature/cap-nhat-voucher-lan-2` vào `main` để nhóm trưởng review. Không push trực tiếp lên `main` và không dùng `--force` sau khi đã mở PR nếu chưa thống nhất với nhóm trưởng.

## Gợi ý mô tả Pull Request

```text
Hoàn thiện chức năng voucher end-to-end:
- CRUD, lọc, bật/tắt, lưu trữ và lịch sử sử dụng phía admin
- validate tập trung, giới hạn tổng và theo khách hàng
- tính lại giỏ hàng/voucher ở backend, lưu snapshot trên đơn
- đồng bộ COD, VNPAY và các luồng hủy/hoàn thành
- nâng cấp UX checkout và bổ sung test

Migration: chạy 3 migration ngày 2026_08_21 theo hướng dẫn trong docs/VOUCHER_FEATURE.md.
```
