<?php

namespace App\Http\Controllers;

use App\Models\VehicleList;
use Illuminate\Http\Request;

class VehicleListController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        
        $search = ($request['search'] != '' || $request['search'] != null ) ? $request['search'] : null;
        $order_by = ($request['order_by'] != '' || $request['order_by'] != null ) ? $request['order_by'] : 'id';
        $sort_by = ($request['sort_by'] != '' || $request['sort_by'] != null ) ? $request['sort_by'] : 'desc';

        $data = VehicleList::when($search, function($q) use($search){
            $q->where('type','ilike','%'.$search.'%');
        })
        ->orderBy($order_by,$sort_by);

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
    public function store(VehicleList $vehicleList, Request $request)
    {
        $validate = $request->validate([
            'type'  => 'required'
        ]);
        
        $data = $vehicleList->create($validate);
        return response('Item successfully added to the database.', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(VehicleList $vehicleList, $id)
    {
        $data = $vehicleList->find($id);
        if($data){
            return response($data, 202);
        }else{
            return response('No record of the item in the database.', 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(VehicleList $vehicleList)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, VehicleList $vehicleList, $id)
    {
        if(isset($id)){
            $validate = $request->validate([
                'type'   => 'required|string',
            ]);
            $updated = $vehicleList->where('id',$id);
            $condition = $updated->first();
            if($condition != null){
                $updated->update($validate);
                return response([
                    'data' => 'Update completed successfully.'
                ], 202);
            }else{
                return response([
                    'error' => 'ID not recognized or invalid.'
                ], 400);
            }
        }else{
            return response([
                'error' => 'ID not recognized or invalid.'
            ], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(VehicleList $vehicleList, $id)
    {
        $data = $vehicleList->find($id);
        if ($data) {
            $data->delete();
            return response('Item successfully deleted.', 202);
        } else {
            return response('ID not recognized or invalid.', 400);
        }
    }
}
