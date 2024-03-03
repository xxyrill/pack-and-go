<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    public function index(Request $request)
    {
        $type = ($request['type'] != '' || $request['type'] != null ) ? $request['type'] : null;
        $status = ($request['status'] != '' || $request['status'] != null ) ? $request['status'] : null;
        
        $data = Subscription::when($type, function($q) use($type){
                            $q->where('type', $type);
                        })
                        ->when($status, function($q) use($status){
                            $q->where('status', $status);
                        })
                        ->with('subscriptionInclusion');

        $details = [
            'from' =>   $request->skip + 1,
            'to'   =>   min(($request->skip + $request->take), $data->count()),
            'total'=>   $data->count()
        ];
        $message = ($data->count() == 0) ? "No Results Found" : "Results Found";
        return response([
            'data'      => $data->skip($request->skip)
                                ->take($request->take)
                                ->orderBy('id', 'asc')
                                ->get(),
            'details'   => $details,
            'message'   => $message
        ]);
    }
    public function store(Subscription $subscription, Request $request)
    {
        $validate = $request->validate([
            'subscription_period' => 'required|integer',
            'price' => 'required|numeric',
            'type' => 'required|in:driver,business',
            'inclusions' => 'required|array',
            'vehicle_number' => 'required|integer'
        ]);
        $data = $subscription->create([
            'subscription_period' => $validate['subscription_period'],
            'price' => $validate['price'],
            'type' => $validate['type'],
            'vehicle_number' => $validate['vehicle_number'],
            'status' => 'active',
        ]);
        
        $data->subscriptionInclusion()->createMany($validate['inclusions']);
        return response($data);
    }
}
