<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserValidation extends FormRequest
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
            'first_name' => 'required',
            'last_name' => 'required',
            'middle_name' => 'required',
            'suffix' => 'nullable',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed',
            'user_name' => 'required|unique:users,user_name',
            'type' => 'required|in:driver,business,customer',
            'contact_number' => 'required|string',
            'gender' => 'required|in:male,female'
        ];
    }

    public function messages() {
        return [
        ];
    }
}
