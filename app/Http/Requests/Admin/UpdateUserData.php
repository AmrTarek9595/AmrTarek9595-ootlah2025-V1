<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserData extends FormRequest
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
            'user_login' => 'sometimes|string|max:255',
            'user_email' => 'sometimes|email|max:255|exists:wp_users,user_email',
            'user_pass' => 'sometimes|string|min:8|confirmed',
            'role' => 'sometimes|exists:wp_users_role,title',
            'user_nicename' => 'sometimes|string|max:255',
            'user_url' => 'sometimes|string|max:255',
            'user_status' => 'sometimes|in:0,1', //0 for active users , 1 for inactive users
            'display_name' => 'sometimes|string|max:255',
            "gender" => "sometimes|in:male,female",
            "phone_number" => "sometimes|numeric|max:10",
            "address" => "sometimes|string|max:255",

        ];
    }
}
