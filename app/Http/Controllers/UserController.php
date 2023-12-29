<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserValidation;
use App\Models\User;
use Illuminate\Http\Request;

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
            'suffix' => $userValidation['suffix'],
            'email' => $userValidation['email'],
            'password' => bcrypt($userValidation['password']),
            'user_name' => $userValidation['user_name'],
            'type' => $userValidation['type'],
            'contact_number' => $userValidation['contact_number'],
            'gender' => $userValidation['gender']
        ];
        User::create($user_payload);
        return response([
            'message' => 'Data inserted.'
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
