<?php

namespace App\Incrudible\Http\Requests\Admin;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Incrudible\Incrudible\Facades\Incrudible;

class UpdateAdminRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth(Incrudible::guardName())->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'username' => [
                'required',
                'string',
                'min:1',
                'max:255',
            ],
            // 'email' => [
            //     'required',
            //     'string',
            //     'min:1',
            //     'max:255',
            //     'email',
            //     'unique:admins,email',
            // ],
            // 'email_verified_at' => [
            //     'nullable',
            //     'date',
            //     'after_or_equal:1970-01-01 00:00:01',
            //     'before_or_equal:2038-01-19 03:14:07',
            //     'date_format:Y-m-d H:i:s',
            // ],
            // 'password' => [
            //     'required',
            //     'string',
            //     'min:1',
            //     'max:255',
            //     'required',
            //     'min:8',
            // ],
            // 'remember_token' => [
            //     'nullable',
            //     'string',
            //     'min:1',
            //     'max:100',
            // ],
        ];
    }
}
