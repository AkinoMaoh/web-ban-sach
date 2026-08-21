<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreVoucherRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'code' => Str::upper(trim((string) $this->input('code'))),
            'name' => trim((string) $this->input('name')),
            'description' => $this->filled('description')
                ? trim((string) $this->input('description'))
                : null,
            'max_discount_value' => $this->input('type') === 'percent'
                ? $this->input('max_discount_value')
                : null,
            'usage_limit' => $this->filled('usage_limit') ? $this->input('usage_limit') : null,
            'usage_limit_per_customer' => $this->filled('usage_limit_per_customer')
                ? $this->input('usage_limit_per_customer')
                : null,
            'is_active' => $this->boolean('is_active'),
            'is_public' => $this->boolean('is_public'),
        ]);
    }

    public function rules(): array
    {
        return [
            'code' => [
                'required',
                'string',
                'max:100',
                'regex:/^[A-Z0-9_-]+$/',
                Rule::unique('vouchers', 'code'),
            ],
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:2000'],
            'type' => ['required', Rule::in(['fixed', 'percent'])],
            'discount_value' => [
                'required',
                'numeric',
                'min:1',
                Rule::when($this->input('type') === 'percent', ['max:100']),
            ],
            'max_discount_value' => ['nullable', 'required_if:type,percent', 'numeric', 'min:1'],
            'min_order_value' => ['required', 'numeric', 'min:0'],
            'usage_limit' => ['nullable', 'integer', 'min:1'],
            'usage_limit_per_customer' => ['nullable', 'integer', 'min:1'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'is_active' => ['required', 'boolean'],
            'is_public' => ['required', 'boolean'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $globalLimit = $this->input('usage_limit');
            $customerLimit = $this->input('usage_limit_per_customer');

            if ($globalLimit !== null
                && $customerLimit !== null
                && (int) $customerLimit > (int) $globalLimit) {
                $validator->errors()->add(
                    'usage_limit_per_customer',
                    'Giới hạn mỗi khách không được lớn hơn tổng lượt sử dụng.'
                );
            }
        });
    }

    public function messages(): array
    {
        return [
            'code.required' => 'Vui lòng nhập mã voucher.',
            'code.regex' => 'Mã chỉ được chứa chữ in hoa, số, dấu gạch ngang và gạch dưới.',
            'code.unique' => 'Mã voucher này đã tồn tại.',
            'name.required' => 'Vui lòng nhập tên chương trình.',
            'type.in' => 'Loại giảm giá không hợp lệ.',
            'discount_value.required' => 'Vui lòng nhập mức giảm.',
            'discount_value.min' => 'Mức giảm phải lớn hơn 0.',
            'discount_value.max' => 'Mức giảm theo phần trăm không được vượt quá 100%.',
            'max_discount_value.required_if' => 'Vui lòng nhập số tiền giảm tối đa cho voucher phần trăm.',
            'min_order_value.required' => 'Vui lòng nhập giá trị đơn tối thiểu.',
            'usage_limit.min' => 'Tổng lượt sử dụng phải từ 1 trở lên.',
            'usage_limit_per_customer.min' => 'Lượt dùng mỗi khách phải từ 1 trở lên.',
            'start_date.required' => 'Vui lòng chọn ngày bắt đầu.',
            'end_date.required' => 'Vui lòng chọn ngày kết thúc.',
            'end_date.after_or_equal' => 'Ngày kết thúc phải bằng hoặc sau ngày bắt đầu.',
        ];
    }
}
