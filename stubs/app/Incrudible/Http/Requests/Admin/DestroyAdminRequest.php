<?php

namespace App\Incrudible\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Incrudible\Incrudible\Facades\Incrudible;

class DestroyAdminRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth(Incrudible::guardName())->check()
            && $this->route('admin')->id !== auth(Incrudible::guardName())->id();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [];
    }
}
