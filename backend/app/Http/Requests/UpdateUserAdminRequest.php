<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserAdminRequest extends FormRequest
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
        $id = $this->route('id');

        return [
            'name' => 'sometimes|required|string|max:100',
            'email' => ['sometimes', 'required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($id)],
            'password' => 'nullable|string|min:8|confirmed',
            'role_id' => ['sometimes', 'required', Rule::exists('roles', 'id')],
            'phone' => 'nullable|string|max:20',
            'student_code' => 'nullable|string|max:20',
            'avatar' => 'nullable|url|max:500',
            'status' => ['nullable', Rule::in(['active', 'banned'])],
        ];
    }
}
