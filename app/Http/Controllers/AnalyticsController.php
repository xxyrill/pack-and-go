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
    public function getTotalRevenue(Request $request)
    {
        $year = ($request['year'] != '' || $request['year'] != null ) ? $request['year'] : null;
        $monthlyData = [];
        for ($month = 1; $month <= 12; $month++) {
            $bookings = Booking::where('status', 'completed')
                            ->whereMonth('created_at', $month)
                            ->when($year, function($q) use($year){
                                $q->whereYear('created_at', $year);
                            })
                            ->sum('price');
            $monthlyData[$month] = $bookings;
        }
        return response(['data' => $monthlyData]);
    }
}
