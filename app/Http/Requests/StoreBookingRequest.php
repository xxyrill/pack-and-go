<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
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
            'vehicle_list_id' => 'required|exists:vehicle_lists,id',
            'pick_up_location' => 'required|string',
            'pick_up_longitude' => 'nullable|numeric',
            'pick_up_latitude' => 'nullable|numeric',
            'drop_off_location' => 'required|string',
            'drop_off_longitude' => 'nullable|numeric',
            'drop_off_latitude' => 'nullable|numeric',
            'pickup_house_information' => 'required',
            'pickup_helper_stairs' => 'required|boolean',
            'pickup_helper_elivator' => 'required|boolean',
            'pickup_ring_door' => 'required|boolean',
            'pickup_adition_remarks' => 'nullable',
            'drop_off_house_information' => 'required',
            'booking_date_time_start' => 'required|date',
            'booking_date_time_end' => 'required|date',
            'need_helper' => 'required|boolean',
            'alt_contact_number_one' => 'required',
            'alt_contact_number_two' => 'nullable',
            'alt_email' => 'nullable',
            'booking_items' => 'required|array'
        ];
    }
    public function messages() {
        return [
            'vehicle_list_id.required' => 'Please specify what type of vehicle.'
        ];
    }
}
