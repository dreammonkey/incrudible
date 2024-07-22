<?php

namespace App\Incrudible\Http\Requests\Admin;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Incrudible\Incrudible\Facades\Incrudible;

class GetAdminsRequest extends FormRequest
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
            "page" => ["nullable", 'integer', 'min:1'],
            'perPage' => ["nullable", 'integer', 'max:100', 'min:2'],
            'orderDir' => ['nullable', 'in:desc,asc'],
            'orderBy' => ['nullable',  Rule::in([
                'id',
                'username',
                'email',
                'email_verified_at',
                'password',
                'remember_token',
                'created_at',
                'updated_at',
            ])],
            'search' => ['nullable', 'string', 'regex:/^[0-9a-zA-Z ]/']
        ];
    }
}
