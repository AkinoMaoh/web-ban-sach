<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            
            // BỔ SUNG THÊM 3 TRƯỜNG MỚI VÀO ĐÂY ĐỂ CHO PHÉP DỮ LIỆU ĐI QUA:
            'phone' => ['nullable', 'string', 'max:20'],
            'gender' => ['nullable', 'string', 'in:male,female'],
            'address' => ['nullable', 'string', 'max:1000'],
        ];
    }
}