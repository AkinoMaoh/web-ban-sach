<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $phone = preg_replace('/[\s.\-()]+/', '', (string) $this->input('shipping_phone'));

        if (str_starts_with($phone, '+84')) {
            $phone = '0'.substr($phone, 3);
        }

        $this->merge([
            'shipping_name' => trim((string) $this->input('shipping_name')),
            'shipping_phone' => $phone,
            'billing_email' => Str::lower(trim((string) $this->input('billing_email'))),
            'applied_voucher' => $this->filled('applied_voucher')
                ? Str::upper(trim((string) $this->input('applied_voucher')))
                : null,
            'specific_address' => trim((string) $this->input('specific_address')),
            'save_address' => $this->boolean('save_address'),
            'set_default_address' => $this->boolean('set_default_address'),
        ]);
    }

    public function rules(): array
    {
        return [
            'shipping_name' => ['required', 'string', 'max:255'],
            'shipping_phone' => ['required', 'regex:/^0(?:3|5|7|8|9)[0-9]{8}$/'],
            'billing_email' => ['required', 'email', 'max:255'],
            'province_id' => ['required', 'integer', Rule::exists('provinces', 'id')],
            'district_id' => [
                'required',
                'integer',
                Rule::exists('districts', 'id')->where(
                    fn ($query) => $query->where('province_id', $this->input('province_id'))
                ),
            ],
            'ward_code' => [
                'required',
                'string',
                Rule::exists('wards', 'code')->where(
                    fn ($query) => $query->where('district_id', $this->input('district_id'))
                ),
            ],
            'specific_address' => ['required', 'string', 'max:500'],
            'order_notes' => ['nullable', 'string', 'max:2000'],
            'payment_method' => ['required', Rule::in(['cod', 'vnpay'])],
            'applied_voucher' => ['nullable', 'string', 'max:100'],
            'checkout_token' => ['required', 'uuid'],
            'shipping_quote_token' => ['required', 'string', 'max:4096'],
            'address_id' => [
                'nullable',
                'integer',
                Rule::exists('user_addresses', 'id')->where(
                    fn ($query) => $query->where('user_id', $this->user()?->id ?? 0)
                ),
            ],
            'save_address' => ['boolean'],
            'set_default_address' => ['boolean'],
            'agree_terms' => ['accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'shipping_name.required' => 'Vui lòng nhập họ và tên người nhận.',
            'shipping_phone.required' => 'Vui lòng nhập số điện thoại.',
            'shipping_phone.regex' => 'Số điện thoại Việt Nam không đúng định dạng.',
            'billing_email.required' => 'Vui lòng nhập địa chỉ email.',
            'billing_email.email' => 'Địa chỉ email không đúng định dạng.',
            'province_id.required' => 'Vui lòng chọn tỉnh/thành phố.',
            'district_id.required' => 'Vui lòng chọn quận/huyện.',
            'ward_code.required' => 'Vui lòng chọn phường/xã.',
            'specific_address.required' => 'Vui lòng nhập số nhà và tên đường.',
            'payment_method.required' => 'Vui lòng chọn phương thức thanh toán.',
            'checkout_token.required' => 'Phiên thanh toán đã hết hiệu lực. Vui lòng tải lại trang.',
            'shipping_quote_token.required' => 'Vui lòng chọn địa chỉ để tính phí vận chuyển.',
            'address_id.exists' => 'Địa chỉ đã lưu không hợp lệ.',
            'agree_terms.accepted' => 'Bạn cần đồng ý với điều khoản đặt hàng.',
        ];
    }
}
