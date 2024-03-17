<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\User;
use App\Models\UserVehicles;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function dashboard(Request $request)
    {
        $filter_date = ($request['filter_date'] != '' || $request['filter_date'] != null ) ? $request['filter_date'] : null;
        
        $booking_complete = Booking::when($filter_date, function($q) use($filter_date){
                                        $q->whereDate('created_at', '>=', $filter_date[0])
                                        ->whereDate('created_at', '<=', $filter_date[1]);
                                     })
                                     ->where('status', 'completed')->count();
        $booking_cancelled = Booking::when($filter_date, function($q) use($filter_date){
                                        $q->whereDate('created_at', '>=', $filter_date[0])
                                        ->whereDate('created_at', '<=', $filter_date[1]);
                                     })
                                     ->where('status', 'cancelled')->count();
        $booking_pending = Booking::when($filter_date, function($q) use($filter_date){
                                        $q->whereDate('created_at', '>=', $filter_date[0])
                                        ->whereDate('created_at', '<=', $filter_date[1]);
                                     })
                                     ->where('status', 'pending')->count();
        $customers = User::when($filter_date, function($q) use($filter_date){
                                $q->whereDate('created_at', '>=', $filter_date[0])
                                ->whereDate('created_at', '<=', $filter_date[1]);
                            })
                            ->where('type', 'customer')
                            ->count();
        $vehicles = UserVehicles::when($filter_date, function($q) use($filter_date){
                                        $q->whereDate('created_at', '>=', $filter_date[0])
                                        ->whereDate('created_at', '<=', $filter_date[1]);
                                    })
                                    ->count();

        return response([
            'pending'   => $booking_pending,
            'completed' => $booking_complete,
            'cancelled' => $booking_cancelled,
            'customers' => $customers,
            'vehicles'  => $vehicles,
            'revenue'   => 0
        ]);
    }
}
