<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ShippingFeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
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
        ];
    }

    public function messages(): array
    {
        return [
            'province_id.required' => 'Vui lòng chọn tỉnh/thành phố.',
            'district_id.required' => 'Vui lòng chọn quận/huyện.',
            'ward_code.required' => 'Vui lòng chọn phường/xã.',
            'province_id.exists' => 'Tỉnh/thành phố không hợp lệ.',
            'district_id.exists' => 'Quận/huyện không thuộc tỉnh/thành phố đã chọn.',
            'ward_code.exists' => 'Phường/xã không thuộc quận/huyện đã chọn.',
        ];
    }
}
