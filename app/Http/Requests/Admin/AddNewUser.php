<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AddNewUser extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
            return [
            'user_login' => 'required|string|max:255',
            'user_email' => 'required|string|email|max:255|unique:wp_users,user_email',
            'user_pass' => 'required|string|min:8|confirmed',
            'role' => 'required|string|exists:wp_users_role,title',

        ];
    }
}
