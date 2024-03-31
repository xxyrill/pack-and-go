<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserValidation;
use App\Mail\ForgotPassword;
use App\Models\Booking;
use App\Models\ContactNumberOtp;
use App\Models\EmailOtp;
use App\Models\PasswordReset;
use App\Models\User;
use App\Models\UserBlock;
use App\Models\UserBusinessDetails;
use App\Models\UserDriverDetails;
use App\Models\UserRating;
use App\Models\UserRatingComment;
use App\Models\UserSubscription;
use App\Models\UserSuspension;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserValidation $userValidation)
    {
        $userValidation->validated();
        $user_payload = [
            'first_name' => $userValidation['first_name'],
            'last_name' => $userValidation['last_name'],
            'middle_name' => $userValidation['middle_name'],
            'email' => $userValidation['email'],
            'password' => bcrypt($userValidation['password']),
            'user_name' => $userValidation['user_name'],
            'type' => $userValidation['type'],
            'contact_number' => $userValidation['contact_number']
        ];
        $user = User::create($user_payload);
        $user->userRole()->create(['role' => $user_payload['type']]);
        if($user_payload['type'] == 'business'){
            $user->userBusiness()->create([
                'business_name' => $userValidation['business_name'],
                'business_address' => $userValidation['business_address'],
                'business_complete_address' => $userValidation['business_complete_address'],
                'business_contact_person' => $userValidation['business_contact_person'],
                'business_contact_person_number' => $userValidation['business_contact_person_number'],
                'business_permit_number' => $userValidation['business_permit_number'],
                'business_tourism_number' => $userValidation['business_tourism_number'],
                'business_contact_person' => $userValidation['business_contact_person'],
            ]);
        }elseif ($user_payload['type'] == 'driver') {
            $user->userDriver()->create([
                'vehicle_list_id' => $userValidation['vehicle_list_id'],
                'driver_license_number' => $userValidation['driver_license_number'],
                'make' => $userValidation['make'],
                'year_model' => $userValidation['year_model'],
                'plate_number' => $userValidation['plate_number'],
                'helper' => $userValidation['helper']
            ]);
        }
        return response([
            'message' => 'Data inserted.'
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $data = User::where('id', $id)
                        ->withCount('userVehicles')
                        ->with(['userBlockedReverse' => function($q) {
                            $q->where('user_id', Auth::id());
                        }])
                        ->first();
        if($data){
            if ($data->type == 'driver') {
                $data->load('userDriver');
            } elseif ($data->type == 'business') {
                $data->load('userBusiness');
            }
            return response(['data' => $data], 200);
        }else{
            return response(['message' => 'Not found.'],404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,$id)
    {
        if(User::find($id)){
            $user = Auth::id();
            $validate = $request->validate([
                "first_name" => 'required|string',
                "last_name" => 'required|string',
                "middle_name" => 'nullable|string|unique:table,column,except,id',
                "suffix" => 'nullable|string',
                "user_name" => 'required|unique:users,user_name,'.$user.',id',
                "gender" => 'nullable|in:male,female',
                "birth_date" => 'nullable|date',
                "type" => 'required|in:driver,customer,business',
                "driver_license_number" => 'required_if:type,driver',
                "license_expiry_date" => 'required_if:type,driver',

                "business_name" => 'required_if:type,business',
                "business_address" => 'required_if:type,business',
                "business_complete_address" => 'required_if:type,business',
                "business_permit_number" => 'required_if:type,business',
                "business_dti_number" => 'required_if:type,business',
                "business_city_tourism_number" => 'required_if:type,business',
                "business_contact_person" => 'required_if:type,business',
                "business_contact_person_number" => 'required_if:type,business',
            ]);
            User::find($id)->update($validate);
            if($validate['type'] == 'driver'){
                UserDriverDetails::where('user_id', Auth::id())->update([
                    "driver_license_number" => $validate['driver_license_number'],
                    "license_expiry_date" => $validate['license_expiry_date']
                ]);
            }elseif ($validate['type'] == 'business') {
                UserBusinessDetails::where('user_id', Auth::id())->update([
                    "business_name" => $validate['business_name'],
                    "business_address" => $validate['business_address'],
                    "business_complete_address" => $validate['business_complete_address'],
                    "business_permit_number" => $validate['business_permit_number'],
                    "business_dti_number" => $validate['business_dti_number'],
                    "business_tourism_number" => $validate['business_city_tourism_number'],
                    "business_contact_person" => $validate['business_contact_person'],
                    "business_contact_person_number" => $validate['business_contact_person_number'],
                ]);
            }
            return response([
                'message' => 'User updated.'
            ], 200);
        }else{
            return response([
                'message' => 'Not found.'
            ], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function verifyFields(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users,email',
            'user_name' => 'required|unique:users,user_name',
        ]);
        return response('No errors', 200);
    }

    public function getUserData(User $user)
    {
        $user_id = Auth::user()->id;
        $data = $user->where('id', $user_id)->first();

        if ($data->type == 'driver') {
            $data->load('userDriver');
        } elseif ($data->type == 'business') {
            $data->load('userBusiness');
        }

        return response(['data' => $data], 200);
    }
    public function getUserSubscription(UserSubscription $user_subscription)
    {
        $user_id = Auth::user()->id;
        $data = $user_subscription->where('user_id', $user_id)
                ->with('subscription')
                ->first();

        return response(['data' => $data], 200);
    }
    public function saveDriverLicensePhoto(UserDriverDetails $user_driver_details, Request $request)
    {
        $validate = $request->validate([
            'type' => 'required|in:front,back',
            'file' => 'required|image',
            'user_driver_details_id' => 'required|exists:user_driver_details,id'
        ]);
        $id = Auth::id();
        if($validate['type'] == 'front'){
            $path = Storage::disk('public')->put('/license/front/'.$id.'', $validate['file']);
            $user_driver_details->find($validate['user_driver_details_id'])->update(['front_license_path' => $path]);
            return response(['message' => 'Succesfully saved.']);
        }else{
            $path = Storage::disk('public')->put('/license/back/'.$id.'', $validate['file']);
            $user_driver_details->find($validate['user_driver_details_id'])->update(['back_license_path' => $path]);
            return response(['message' => 'Succesfully saved.']);
        }
    }
    public function updateEmail(Request $request)
    {
        $validate = $request->validate([
            'otp_code' => 'required',
            'email' => 'required'
        ]);
        $user_id = Auth::id();

        $data = EmailOtp::where('email', $validate['email'])
                          ->where('email_otp', $validate['otp_code'])
                          ->where('user_id', $user_id)
                          ->first();
        if($data){
            User::find($user_id)->update(['email' => $validate['email']]);
            return response([
                'message' => 'Email succesfully updated.'
            ]);
        }else{
            return response([
                'errors' => [
                    'otp_code' => ['Invalid OTP code.']
                ]
            ], 400);
        }
    }
    public function updateContactNumber(Request $request)
    {
        $validate = $request->validate([
            'otp_code' => 'required',
            'contact_number' => 'required'
        ]);
        $user_id = Auth::id();

        $data = ContactNumberOtp::where('contact_number', $validate['contact_number'])
                          ->where('contact_number_otp', $validate['otp_code'])
                          ->where('user_id', $user_id)
                          ->first();
        if($data){
            User::find($user_id)->update(['contact_number' => $validate['contact_number']]);
            return response([
                'message' => 'Contact number succesfully updated.'
            ]);
        }else{
            return response([
                'errors' => [
                    'otp_code' => ['Invalid OTP code.']
                ]
            ], 400);
        }
    }
    public function checkAuthentication(Request $request)
    {
        $validate = $request->validate([
            'password' => 'required'
        ]);
        $user = Auth::user();
        if(Hash::check($validate['password'], $user->password)){
            return response([
                'status' => 'verified'
            ], 200);
        }else{
            return response([
                'errors' => [
                    'password' => ['Password did not match. Please try again.']
                ]
            ], 400);
        }
    }
    public function updatePassword(Request $request)
    {
        $validate = $request->validate([
            'password' => 'required',
            'new_password'  => 'required|confirmed',
            'new_password_confirmation'  => 'required'
        ]);
        $user = Auth::user();
        if(Hash::check($validate['password'], $user->password)){
            User::find($user->id)->update(['password' => bcrypt($validate['new_password'])]);
            return response([
                'message' => 'Password modification process has been completed'
            ]);
        }else{
            return response([
                'errors' => [
                    'new_password' => ['Password did not match. Please try again.']
                ]
            ], 400);
        }
        
    }
    public function updateProfilePicture(Request $request)
    {
        $validate = $request->validate([
            'file' => 'required|image'
        ]);
        $id = Auth::id();
        $path = Storage::disk('public')->put('/profile-picture/user/'.$id.'', $validate['file']);
        User::find($id)->update(['profile_picture_path' => $path]);
        return response(['message' => 'Succesfully saved.']);
    }
    public function rateService(Request $request)
    {
        $validate = $request->validate([
            "service_user_id" => 'required|exists:users,id',
            "booking_id" => 'required|exists:bookings,id',
            "rate" => 'required|integer'
        ]);
        $validate['customer_id'] = Auth::id();
        $validate['additional_comment'] = $request->additional_comment;
        UserRating::create($validate);
        $rating = UserRating::where('service_user_id', $validate['service_user_id']);
        $sum = (int)$rating->sum('rate');
        $count = $rating->count();
        $average = ($count != 0 ) ? $sum / $count : 0;
        if($count > 5 && $average <= 3.0){
            UserSuspension::create([
                "user_id" => $validate['service_user_id'],
                "start_date" => Carbon::now()->toDateString(),
                "end_date" => Carbon::now()->addDays(7)->toDateString()
            ]);
        }
        Booking::find($validate['booking_id'])->update(['rated' => true]);
        return response([
            'message' => 'Rate saved.'
        ]);
    }
    public function userRating(Request $request)
    {
        $data = UserRating::where('service_user_id', Auth::id())
                            ->with('customer','comment.service.userBusiness');

        $details = [
            'from' =>   $request->skip + 1,
            'to'   =>   min(($request->skip + $request->take), $data->count()),
            'total'=>   $data->count()
        ];
        $message = ($data->count() == 0) ? "No Results Found" : "Results Found";
        return response([
            'data'      => $data->skip($request->skip)
                                ->take($request->take)
                                ->orderBy('id','desc')
                                ->get(),
            'details'   => $details,
            'message'   => $message
        ]);
    }
    public function commentRatingStore(Request $request)
    {
        $validate = $request->validate([
            'user_rating_id' => 'required|exists:user_ratings,id',
            'comment' => 'required|string'
        ]);
        $validate['user_id'] = Auth::id();

        UserRatingComment::create($validate);

        return response([
            'message' => 'comment added'
        ]);
    }
    public function commentRatingUpdate(Request $request)
    {
        $validate = $request->validate([
            'user_rating_comment_id' => 'required|exists:user_rating_comments,id',
            'comment' => 'required|string'
        ]);

        UserRatingComment::find($validate['user_rating_comment_id'])
                            ->update(['comment' => $validate['comment']]);

        return response([
            'message' => 'Comment updated.'
        ]);
    }
    public function commentRatingDelete($id)
    {
        $user_rating_comment = UserRatingComment::find($id);
        if($user_rating_comment){
            $user_rating_comment->delete();
        }else{
            return response([
                'message' => 'Error, invalid user rating'
            ], 400);
        }
    }
    public function numberOfRatings()
    {
        $rating = UserRating::where('service_user_id', Auth::id());
        return response([
            'total_stars' => $rating->sum('rate'),
            'rating_numbers' => $rating->count()
        ]);
    }
    public function userBlocked(Request $request)
    {
        $validate = $request->validate([
            'blocked_user' => 'required|exists:users,id',
            'type' => 'required|in:blocked,unblocked'
        ]);

        if($validate['type'] == 'blocked'){
            $user_blocked = UserBlock::withTrashed()
                                       ->where('user_id', Auth::id())
                                       ->where('blocked_user_id', $validate['blocked_user']);
            if($user_blocked->first()){
                $user_blocked->restore();
            }else{
                UserBlock::create(['user_id' => Auth::id(),'blocked_user_id' => $validate['blocked_user']]);
            }
            return response([
                'message' => 'User blocked.'
            ]);
        }elseif ($validate['type'] == 'unblocked') {
            UserBlock::where('user_id', Auth::id())
                       ->where('blocked_user_id', $validate['blocked_user'])
                       ->delete();
            return response([
                'message' => 'User unblocked.'
            ]);
        }
    }
    public function blockList(Request $request)
    {
        $validate = $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        $data = UserBlock::where('user_id', Auth::id())->where('blocked_user_id', $validate['user_id'])->first();
        if($data){
            return response(1);
        }else{
            return response(0);
        }
    }
    public function getCurrentPlant()
    {
        $data = UserSubscription::where('user_id', Auth::id())
                                  ->with('subscription.subscriptionInclusion', 'userSubscriptionPay')
                                  ->where('status', 'active')
                                  ->first();
        return response([
            'data' => $data
        ]);
    }
    public function subscribePlan(UserSubscription $user_subscription, Request $request)
    {
        $request->validate([
            'subscription_id' => 'required|exists:subscriptions,id'
        ]);
        $conditions = $user_subscription->where('user_id', Auth::id());
        if($conditions->count() > 0){
            $conditions->update(['status' =>  'inactive']);
            $conditions->delete();
        }
        $user_subscription->create([
            'subscription_id'       => $request->subscription_id,
            'user_id'               => Auth::id(),
            'status'                => 'active'
        ]);
    }
    public function forgotPasswordMail(Request $request)
    {
        $validate = $request->validate([
            'email' => 'email|required|exists:users,email'
        ]);
        $user = User::where('email', $validate['email'])->first();
        $password_reset = PasswordReset::create(['email' => $validate['email']]);
        $password_reset->update(['link' => hash('sha256', $user->id.$password_reset->id.$password_reset->created_at)]);

        $mailData = [
            'name'      => $user->first_name." ".$user->middle_name." ".$user->last_name,
            'link'      => env('FE_URL_PRODUCTION').'password-reset/'.$password_reset->link
        ];
        try {
            Mail::to($user->email)->send(new ForgotPassword($mailData));
            return response([
                'message' => 'Successfully emailed.'
            ],200);
        } catch (\Throwable $th) {
            return response([
                'message' => 'Error! Failed to send.'
            ], 400);
        }
    }
    public function changePassword(Request $request)
    {
        $validate = $request->validate([
            'hash' => 'required|string',
            'password' => 'required|confirmed'
        ]);

        $password_reset = PasswordReset::where('link', $validate['hash']);
        $condition = $password_reset->first();
        $user = User::where('email', $condition->email);
        $condition_user = $user->first();
        if($condition && $condition_user){
            $password = bcrypt($validate['password']);
            $user->update(['password' => $password]);
            $condition->delete();
            return response('Success',200);
        }else{
            return response([
                'errors' => ['password' => 'Invalid Url.']
            ],400);
        }
    }
    public function getSuspension(Request $request)
    {
        $data = UserSuspension::where('user_id', Auth::id())
                                ->whereDate('start_date', '<=', Carbon::now())
                                ->whereDate('end_date', '>=', Carbon::now())
                                ->first();
        return response([
            'suspended' => $data ? true : false,
            'expiry'    => $data->end_date ?? null
        ]);
    }
}
