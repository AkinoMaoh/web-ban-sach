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
        $this->merge([
            'billing_email' => Str::lower(trim((string) $this->input('billing_email'))),
            'applied_voucher' => $this->filled('applied_voucher')
                ? Str::upper(trim((string) $this->input('applied_voucher')))
                : null,
            'specific_address' => trim((string) $this->input('specific_address')),
        ]);
    }

    public function rules(): array
    {
        return [
            'shipping_name' => ['required', 'string', 'max:255'],
            'shipping_phone' => ['required', 'regex:/^0[0-9]{9}$/'],
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
        ];
    }

    public function messages(): array
    {
        return [
            'shipping_name.required' => 'Vui lòng nhập họ và tên người nhận.',
            'shipping_phone.required' => 'Vui lòng nhập số điện thoại.',
            'shipping_phone.regex' => 'Số điện thoại phải gồm 10 chữ số và bắt đầu bằng 0.',
            'billing_email.required' => 'Vui lòng nhập địa chỉ email.',
            'billing_email.email' => 'Địa chỉ email không đúng định dạng.',
            'province_id.required' => 'Vui lòng chọn tỉnh/thành phố.',
            'district_id.required' => 'Vui lòng chọn quận/huyện.',
            'ward_code.required' => 'Vui lòng chọn phường/xã.',
            'specific_address.required' => 'Vui lòng nhập số nhà và tên đường.',
            'payment_method.required' => 'Vui lòng chọn phương thức thanh toán.',
        ];
    }
}
