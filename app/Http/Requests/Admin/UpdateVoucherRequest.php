<?php

namespace App\Http\Requests\Admin;

use App\Models\Voucher;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateVoucherRequest extends StoreVoucherRequest
{
    public function rules(): array
    {
        $rules = parent::rules();
        $voucher = $this->route('voucher');
        $voucherId = $voucher instanceof Voucher ? $voucher->id : $voucher;

        $rules['code'] = [
            'required',
            'string',
            'max:100',
            'regex:/^[A-Z0-9_-]+$/',
            Rule::unique('vouchers', 'code')->ignore($voucherId),
        ];

        return $rules;
    }

    public function withValidator(Validator $validator): void
    {
        parent::withValidator($validator);

        $validator->after(function (Validator $validator): void {
            $voucher = $this->route('voucher');
            $globalLimit = $this->input('usage_limit');

            if ($voucher instanceof Voucher
                && $globalLimit !== null
                && (int) $globalLimit < $voucher->used_count) {
                $validator->errors()->add(
                    'usage_limit',
                    "Tổng lượt không thể nhỏ hơn {$voucher->used_count} lượt đã được giữ hoặc sử dụng."
                );
            }
        });
    }
}
