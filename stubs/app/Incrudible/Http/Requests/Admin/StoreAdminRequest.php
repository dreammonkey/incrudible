<?php

namespace App\Incrudible\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Incrudible\Incrudible\Facades\Incrudible;

class StoreAdminRequest extends FormRequest
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
                'min:8',
                'max:255',
            ],
            'email' => [
                'required',
                'string',
                'min:1',
                'max:255',
                'email',
                'unique:admins,email',
            ],
            'password' => [
                'required',
                'string',
                'min:1',
                'max:255',
                'required',
                'min:8',
            ],
            'password_confirmation' => [
                'required',
                'string',
                'min:1',
                'max:255',
                'required',
                'min:8',
                'same:password',
            ],
        ];
    }
}
