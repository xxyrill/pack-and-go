<?php

namespace App\Http\Controllers;

use App\Models\UserSubscription;
use App\Models\UserVehicles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserVehiclesController extends Controller
{
    public function index(Request $request)
    {
        $id = Auth::id();

        $data = UserVehicles::where('user_id', $id)
                        ->with('vehicle');

        $details = [
            'from' =>   $request->skip + 1,
            'to'   =>   min(($request->skip + $request->take), $data->count()),
            'total'=>   $data->count()
        ];
        $message = ($data->count() == 0) ? "No Results Found" : "Results Found";
        return response([
            'data'      => $data->skip($request->skip)
                                ->take($request->take)
                                ->orderBy('id', 'desc')
                                ->get(),
            'details'   => $details,
            'message'   => $message
        ]);
    }
    public function store(Request $request)
    {
        $validate = $request->validate([
            'vehicle_list_id' => 'required|exists:vehicle_lists,id',
            'make' => 'required',
            'year_model' => 'required',
            'plate_number' => 'required',
        ],[
            'vehicle_list_id.exists' => 'Invalid vehicle type.'
        ]);
        $validate['user_id'] = Auth::id();
        $subscription = UserSubscription::where('user_id', Auth::id())->with('subscription')->latest()->first();
        if($subscription){
            $count_user_vehicles = UserVehicles::where('user_id', Auth::id())->count();
            if($subscription->subscription->vehicle_number <= $count_user_vehicles){
                return response([
                    'message' => 'Your subscription is for '.$subscription->subscription->vehicle_number.' vehicle only.'
                ], 400);
            }else{
                UserVehicles::create($validate);
                return response([
                    'message' => 'The information regarding the vehicle has been successfully saved.'
                ]);   
            }
        }else{
            return response([
                'message' => 'Upgrade your ride, subscribe to add a vehicle.'
            ], 400);
        }
        
    }
    public function update(Request $request, $id)
    {
        $vehicle = UserVehicles::find($id);
        if($vehicle){
            $validate = $request->validate([
                'vehicle_list_id' => 'required|exists:vehicle_lists,id',
                'make' => 'required',
                'year_model' => 'required',
                'plate_number' => 'required',
            ],[
                'vehicle_list_id.exists' => 'Invalid vehicle type.'
            ]);
            $vehicle->update($validate);
            return response([
                'message' => 'The information regarding the vehicle has been successfully updated.'
            ]);
        }else{
            return response([
                'message' => 'Invalid vehicle'
            ], 400);
        }
    }
    public function delete($id)
    {
        $vehicle = UserVehicles::find($id);
        if($vehicle){
            $vehicle->delete();
            return response([
                'message' => 'The vehicle record has been successfully removed from the database'
            ], 200);
        }else{
            return response([
                'message' => 'Invalid vehicle'
            ], 400);
        }
    }
}
