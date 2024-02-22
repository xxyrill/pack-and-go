<?php

namespace App\Http\Controllers;

use App\Mail\sendAuthentication;
use App\Mail\sendMail;
use App\Models\EmailOtp;
use App\Models\Mailing;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class MailingController extends Controller
{
    public function sendRescheduleBooking(Request $request)
    {
        $validate = $request->validate([
            'email' => 'required|email',
            'booking_id' => 'required|exists:bookings,id',
            'booking_exact_date' => 'required|date',
            'booking_reschedule_date' => 'required|date'
        ]);
        
        $mailData = [
            'booking_exact_date'      => Carbon::parse($validate['booking_exact_date'])->format('F d, Y'),
            'booking_reschedule_date' => Carbon::parse($validate['booking_reschedule_date'])->format('F d, Y'),
        ];

        try {
            Mail::to($validate['email'])->send(new sendMail($mailData));
            Mailing::create([
                'email' => $validate['email'],
                'type' => 'booking_reschedule',
                'booking_id' => $validate['booking_id']
            ]);
            return response([
                'message' => 'Successfully emailed.'
            ],200);
        } catch (\Throwable $th) {
            return response([
                'message' => 'Error! Failed to send.'
            ], 400);
        }
    }
    public function changeEmail(Request $request)
    {
        $id = Auth::id();
        $validate = $request->validate([
            'email' => 'required|email|unique:users,email'
        ]);
        $mailData = [
            'otp_code' => Str::random(6)
        ];
        try {
            Mail::to($validate['email'])->send(new sendAuthentication($mailData));
            EmailOtp::create([
                'email' => $validate['email'],
                'user_id' => $id,
                'email_otp' => $mailData['otp_code']
            ]);
            return response([
                'message' => 'Successfully emailed.'
            ],200);
        } catch (\Throwable $th) {
            return response([
                'message' => 'Error! Failed to send.'
            ], 400);
        }        
    }
}
