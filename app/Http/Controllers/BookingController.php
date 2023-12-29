<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookingRequest;
use App\Http\Requests\UpdateBookingRequest;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // $search = ($request['search'] != '' || $request['search'] != null ) ? $request['search'] : null;
        $user = Auth::user()->load('userRole');
        $data = Booking::when($user->userRole->role == 'driver', function($q) use($user){
                            $q->where(function($qu) use($user){
                                $qu->whereNull('user_id')
                                  ->orWhere('user_id', $user->id);
                            });
                        });

        $details = [
            'from' =>   $request->skip + 1,
            'to'   =>   min(($request->skip + $request->take), $data->count()),
            'total'=>   $data->count()
        ];
        $message = ($data->count() == 0) ? "No Results Found" : "Results Found";
        return response([
            'data'      => $data->skip($request->skip)
                                ->take($request->take)
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
    public function update(UpdateBookingRequest $request, Booking $booking)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Booking $booking)
    {
        //
    }
}
