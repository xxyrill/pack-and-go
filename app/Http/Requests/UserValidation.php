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
            'middle_name' => 'nullable',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed',
            'user_name' => 'required|unique:users,user_name',
            'type' => 'required|in:driver,business,customer',
            'contact_number' => 'required|string',
            
            'business_name' => 'required_if:type,business',
            'business_address' => 'required_if:type,business',
            'business_complete_address' => 'required_if:type,business',
            'business_contact_person' => 'required_if:type,business',
            'business_contact_person_number' => 'required_if:type,business',
            'business_permit_number' => 'nullable',
            'business_tourism_number' => 'nullable',
            'business_contact_person' => 'nullable',

            'vehicle_list_id' => 'required_if:type,driver|exists:vehicle_lists,id',
            'driver_license_number' => 'required_if:type,driver',
            'make' => 'nullable',
            'year_model' => 'nullable',
            'plate_number' => 'required_if:type,driver',
            'helper' => 'nullable',
        ];
    }

    public function messages() {
        return [
            'business_name.required_if' => 'The business name field is required.',
            'business_address.required_if' => 'The business address field is required.',
            'business_complete_address.required_if' => 'The business location field is required.',
            'business_contact_person.required_if' => 'The contact person field is required.',
            'business_contact_person_number.required_if' => 'The contact person number field is required.',

            'vehicle_list_id.required_if' => 'The vehicle type field is required.',
            'vehicle_list_id.exists' => 'The vehicle type you enter is invalid.',
            'driver_license_number.required_if' => "The driver's license field is required.",
            'plate_number.required_if' => 'The plate number field is required.',
        ];
    }
}
