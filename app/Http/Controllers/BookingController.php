<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookingRequest;
use App\Http\Requests\UpdateBookingRequest;
use App\Models\Booking;
use App\Models\BookingAgreedPrice;
use App\Models\BookingCancelation;
use App\Models\BookingDate;
use App\Models\BookingHistory;
use App\Models\BookingReschedule;
use App\Models\UserVehicles;
use App\Models\UserRating;
use Carbon\Carbon;3
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = ($request['search'] != '' || $request['search'] != null ) ? $request['search'] : null;
        $status = ($request['status'] != '' || $request['status'] != null ) ? $request['status'] : null;
        $date_range = ($request['date_range'] != '' || $request['date_range'] != null ) ? $request['date_range'] : null;
        $sort_by = ($request['sort_by'] != '' || $request['sort_by'] != null ) ? $request['sort_by'] : 'desc';
        $order_by = ($request['order_by'] != '' || $request['order_by'] != null ) ? $request['order_by'] : 'id';
        $sort_by = ($request['sort_by'] != '' || $request['sort_by'] != null ) ? $request['sort_by'] : 'desc';
        $user = Auth::user()->load('userRole');
        $user_vehicles = UserVehicles::where('user_id', $user->id)->pluck('vehicle_list_id')->toArray();
        $data = Booking::when($user->userRole->role == 'driver', function($q) use($user, $search, $user_vehicles){
                            $q->where(function($qu) use($user, $user_vehicles) {
                                $qu->whereIn('vehicle_list_id', $user_vehicles)
                                   ->where(function($q)use($user){
                                       $q->whereNull('user_driver_id')
                                         ->orWhere('user_driver_id', $user->id);
                                   });
                            })
                            ->when($search, function($q) use($search){
                                $q->where(function($q) use($search){
                                    $terms = explode(' ', $search);
                                    $q->where(function($q) use($terms) {
                                        $q->whereHas('customer',function($q) use($terms){
                                            foreach ($terms as $term) {
                                                $q->where('first_name', 'ilike', '%' . $term . '%')
                                                    ->orWhere('last_name', 'ilike', '%' . $term . '%');
                                            }
                                        });
                                    })
                                    ->orWhere('order_number', $search)
                                    ->orWhere('pick_up_location', 'ilike', '%'.$search.'%')
                                    ->orWhere('drop_off_location', 'ilike', '%'.$search.'%');
                                });
                            });
                        })
                        ->when($user->userRole->role == 'business', function($q) use($user, $search, $user_vehicles){
                            $q->where(function($qu) use($user, $user_vehicles) {
                                $qu->whereIn('vehicle_list_id', $user_vehicles)
                                   ->where(function($q)use($user){
                                       $q->whereNull('user_driver_id')
                                         ->orWhere('user_driver_id', $user->id);
                                   });
                            })
                            ->when($search, function($q) use($search){
                                $q->where(function($q) use($search){
                                    $terms = explode(' ', $search);
                    
                                    $q->where(function($q) use($terms) {
                                        $q->whereHas('customer',function($q) use($terms){
                                            foreach ($terms as $term) {
                                                $q->where('first_name', 'ilike', '%' . $term . '%')
                                                    ->orWhere('last_name', 'ilike', '%' . $term . '%');
                                            }
                                        });
                                    })
                                    ->orWhere('order_number', $search)
                                    ->orWhere('pick_up_location', 'ilike', '%'.$search.'%')
                                    ->orWhere('drop_off_location', 'ilike', '%'.$search.'%');
                                });
                            });
                        })
                        ->when($user->userRole->role == 'customer', function($q) use($user, $search){
                            $q->where('user_id', $user->id)
                            ->when($search, function($q) use($search){
                                $q->where(function($q) use($search){
                                    $terms = explode(' ', $search);
                    
                                    $q->where(function($q) use($terms) {
                                        $q->whereHas('driver',function($q) use($terms){
                                            foreach ($terms as $term) {
                                                $q->where('first_name', 'ilike', '%' . $term . '%')
                                                    ->orWhere('last_name', 'ilike', '%' . $term . '%');
                                            }
                                        });
                                    })
                                    ->orWhere('order_number', $search)
                                    ->orWhere('pick_up_location', 'ilike', '%'.$search.'%')
                                    ->orWhere('drop_off_location', 'ilike', '%'.$search.'%');
                                });
                            });
                        })
                        ->when($status, function($q) use($status){
                            $q->where('status', $status);
                        })
                        ->when($date_range, function($q) use($date_range){
                            $q->where('created_at', '>=', $date_range[0])
                              ->where('created_at', '<=', $date_range[1]);
                        })
                        ->with('vehicleType', 'driver.userBusiness', 'customer', 'dates', 'bookingRequestPrice.driver.userBusiness')
                        ->with(['bookingHistory' => function($q) {
                            $q->orderBy('created_at', 'desc');
                        }]);

        $details = [
            'from' =>   $request->skip + 1,
            'to'   =>   min(($request->skip + $request->take), $data->count()),
            'total'=>   $data->count()
        ];
        $message = ($data->count() == 0) ? "No Results Found" : "Results Found";
        return response([
            'data'      => $data->skip($request->skip)
                                ->take($request->take)
                                ->orderBy($order_by, $sort_by)
                                ->get(),
            'details'   => $details,
            'message'   => $message
        ]);
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
    public function store(Booking $booking, StoreBookingRequest $request)
    {
        $validate = $request->validated();
        $validate['user_id'] = Auth::user()->id;
        $validate['status'] = 'pending';
        $data = $booking->create($validate);
        $data->dates()->create([
            'date' => $validate['booking_date_time_start']
        ]);
        $order_number = str_pad($data->id, 10, '0', STR_PAD_LEFT);
        $booking->find($data->id)->update(['order_number' => $order_number]);
        return response('Successfully secured your booking', 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Booking $booking)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Booking $booking)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Booking $booking, $id)
    {
        $booking_data = $booking->where('id', $id);
        $booking_data->update($request->all());
        return response([
            'data' => $booking_data->first()
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Booking $booking)
    {
        //
    }

    public function bookingReschedule(BookingReschedule $booking_reschedule, Request $request)
    {
        $validate = $request->validate([
            'reason' => 'required|string',
            'booking_id' => 'required|exists:bookings,id',
            'booking_date' => 'required|date'
        ]);

        Booking::find($validate['booking_id'])->update(['status' => 'reschedule']);
        BookingDate::create([
            'booking_id' => $validate['booking_id'],
            'date' => Carbon::parse($validate['booking_date'])->addDay()
        ]);
        BookingReschedule::create([
            'reason' => $validate['reason'],
            'booking_id' => $validate['booking_id']
        ]);

        return response([
            'message' => 'Booking Rescheduled.'
        ]);
    }
    
    public function storeHistory(Request $request)
    {
        $validate = $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'track_details' => 'required|string'
        ]);
        
        BookingHistory::create($validate);

        return response([
            'message' => 'message saved.'
        ]);
    }
    public function storeCanceledBooking(Request $request)
    {
        $validate = $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'reason' => 'required|string'
        ]);
        
        BookingCancelation::create($validate);

        return response([
            'message' => 'message saved.'
        ]);
    }
    public function storeBookingPrice(Request $request)
    {
        $validate = $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'price' => 'required|numeric'
        ]);
        $validate['service_user_id'] = Auth::id();
        BookingAgreedPrice::create($validate);
        return response([
            'message' => 'data saved.'
        ]);
    }
}
