<?php

namespace App\Incrudible\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Incrudible\Incrudible\Facades\Incrudible;

class PasswordConfirmRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Incrudible::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $guard = incrudible_guard_name();

        return [
            'password' => ['required', "current_password:{$guard}"],
        ];
    }
}
