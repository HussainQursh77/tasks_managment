<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Auth;
class AttachUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::user()->role == 'admin';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',
            'user_role' => 'required|in:developer,tester,manager',
            'contribution_hours' => 'nullable|numeric|min:0'
        ];
    }

    //custom error message
    public function messages()
    {
        return [
            'user_id.required' => 'The user ID is required.',
            'user_id.exists' => 'The selected user does not exist.',
            'user_role.required' => 'The user role is required.',
            'user_role.in' => 'The user role must be one of the following: developer, tester, or manager.',
            'contribution_hours.numeric' => 'Contribution hours must be a valid number.',
        ];
    }
}
