<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserAdminRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:150|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => ['required', Rule::exists('roles', 'id')],
            'phone' => 'nullable|string|max:20',
            'student_code' => 'nullable|string|max:20',
            'avatar' => 'nullable|url|max:500',
            'status' => ['nullable', Rule::in(['active', 'banned'])],
        ];
    }
}
