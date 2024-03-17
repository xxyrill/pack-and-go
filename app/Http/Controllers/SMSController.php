<?php

namespace App\Http\Controllers;

use App\Models\ContactNumberOtp;
use App\Models\OtpCodes;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class SMSController extends Controller
{
    public function sendOtp(OtpCodes $otp_codes, Request $request)
    {
        $payload = $request->validate([
            'contact_number' => 'required|integer',
            'type' => 'required|in:registration,booking_confirmation',
            'order_number' => 'required_if:type,booking_confirmation',
            'delivery_date' => 'required_if:type,booking_confirmation',
            'route' => 'required_if:type,booking_confirmation',
            'type' => 'required_if:type,booking_confirmation',
        ]);
        $current_date_time = Carbon::now('Asia/Manila');
        $validation = $otp_codes->where('contact_number', $payload['contact_number'])->first();
        $message = null;
        if($payload['type'] == 'registration'){
            $message = 'Please use within 5 minutes.';
        }else{
            $message = "Service request number: {$payload['order_number']}\nIf price agreed, provide OTP to driver.\n";
        }

        if($validation){
            $minutes_difference = $current_date_time->diffInMinutes($validation->timer);
            if($minutes_difference < 0 ){
                return response(['message'=>'Please wait until five minutes.'], 400);
            }
        }
        $apiKey = env('SMS_KEY');
        $baseUrl = 'https://api.semaphore.co/api/v4/otp';
        $request_payload = [
            'apikey' => $apiKey,
            'number' => '+63'.$payload['contact_number'],
            'message' => $message
        ];
        $response = Http::post($baseUrl, $request_payload);
        if ($response->successful()) {
            $code = $response[0]['code'];
            $contact_number = $payload['contact_number'];
            $otp_codes->updateOrCreate(['contact_number' => $contact_number], 
                                              ['otp_code' => $code,
                                               'contact_number' => $contact_number,
                                               'timer' => $current_date_time]);
            return response(['message' => 'A one-time password is being sent'],200);

        } else {
            return response(['message' => 'Something went wrong. Try again'],400);
        }
    }
    public function verifyOtp(OtpCodes $otp_codes,Request $request)
    {
        $validate = $request->validate([
            'otp_code' => 'required|integer',
            'contact_number' => 'required|integer'
        ]);
        
        $validate_otp = $otp_codes->where('contact_number', $validate['contact_number'])
                                  ->where('otp_code', $validate['otp_code'])
                                  ->first();
        if($validate_otp){
            return response(['message' => 'User authentication successful.'], 200);
        }else{
            return response(['message' => 'Code mismatch: Please try again'], 422);
        }
    }
    public function sendOtpUpdateContactNumber(Request $request)
    {
        $id = Auth::id();
        $validate = $request->validate([
            'contact_number' => 'required|unique:users,contact_number'
        ]);
        try {
            $apiKey = env('SMS_KEY');
            $baseUrl = 'https://api.semaphore.co/api/v4/otp';
            $request_payload = [
                'apikey' => $apiKey,
                'number' => '+63'.$validate['contact_number'],
                'message' => 'Please use within 5 minutes.'
            ];
            $response = Http::post($baseUrl, $request_payload);
            if ($response->successful()) {
                $code = $response[0]['code'];
                ContactNumberOtp::create([
                    "user_id" => $id,
                    "contact_number_otp" => $code,
                    "contact_number" => $validate['contact_number']
                ]);
                return response(['message' => 'A one-time password is being sent'],200);

            } else {
                return response(['message' => 'Something went wrong. Try again'],400);
            }
        } catch (\Throwable $th) {
            return response([
                'message' => 'Error! Failed to send.'
            ], 400);
        }  
    }
}
