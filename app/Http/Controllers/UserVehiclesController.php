<?php

namespace App\Http\Controllers;

use App\Models\UserSubscription;
use App\Models\UserVehicles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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
                $id = UserVehicles::create($validate);
                return response([
                    'id' => $id->id, 
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
    public function vehicleStatus($id, Request $request)
    {
        $vehicle = UserVehicles::find($id);
        if($vehicle){
            $request->validate(['status' => 'required|in:active,inactive']);
            $vehicle->update(['status' => $request->status]);
            return response([
                'message' => ($request->status == 'active') ? 'You can now recieve a booking with this vehicle' : 'Vehicle is inactive, you cannot get any bookings for now.'
            ], 200);
        }else{
            return response([
                'message' => 'Invalid vehicle'
            ], 400);
        }
    }
    public function vehicleUserLists(Request $request)
    {
        
        $status = ($request['status'] != '' || $request['status'] != null ) ? $request['status'] : null;
        $filter_date = ($request['filter_date'] != '' || $request['filter_date'] != null ) ? $request['filter_date'] : null;
        $data = UserVehicles::when($filter_date, function($q) use($filter_date){
                                $q->whereDate('created_at', '>=', $filter_date[0])
                                ->whereDate('created_at', '<=', $filter_date[1]);
                            })
                            ->when($status === 'inactive', function($q) use($status){
                                $q->whereNotNull('deleted_at');
                            })
                            ->when($status === 'active', function($q) use($status){
                                $q->whereNull('deleted_at');
                            })
                            ->with('vehicle','user.userBusiness');

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
    public function uploadDocuments(Request $request)
    {
        $validate = $request->validate([
            'type' => 'required|in:or,cr',
            'file' => 'required|image',
            'user_vehicle_id' => 'required|exists:user_vehicles,id'
        ]);
        $id = Auth::id();
        if($validate['type'] == 'or'){
            $path = Storage::disk('public')->put('/vehicle-document/or/'.$id.'', $validate['file']);
            UserVehicles::find($validate['user_vehicle_id'])->update(['or_path' => $path]);
            return response(['message' => 'Succesfully saved.']);
        }else{
            $path = Storage::disk('public')->put('/vehicle-document/cr/'.$id.'', $validate['file']);
            UserVehicles::find($validate['user_vehicle_id'])->update(['cr_path' => $path]);
            return response(['message' => 'Succesfully saved.']);
        }
    }
}
