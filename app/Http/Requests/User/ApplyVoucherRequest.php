<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class ApplyVoucherRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'voucher_code' => Str::upper(trim((string) $this->input('voucher_code'))),
            'billing_email' => $this->filled('billing_email')
                ? Str::lower(trim((string) $this->input('billing_email')))
                : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'voucher_code' => ['required', 'string', 'max:100'],
            'billing_email' => ['nullable', 'email', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'voucher_code.required' => 'Vui lòng nhập mã giảm giá.',
            'billing_email.email' => 'Email nhận hàng không đúng định dạng.',
        ];
    }
}
