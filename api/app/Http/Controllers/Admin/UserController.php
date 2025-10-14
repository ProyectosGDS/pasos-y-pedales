<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index() {
        try {
            $users = User::with(['profile','information'])
                ->latest('id')
                ->get();

            return response([
                'users' => $users,
                'message' => 'Get all users successfully.'
            ]);
        } catch (\Throwable $th) {
            return response([
                'error' => $th->getMessage(),
                'message' => 'Message example.'
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {
        $request->validate([
            'first_name' => 'required|string|max:60',
            'last_name' => 'required|string|max:60',
            'cui' => 'required|numeric|digits:13|unique:user_information,cui',
            'gender' => 'required|in:F,M',
            'birthday' => 'required|date|date_format:Y-m-d',
            'email' => 'required|email|unique:user_information,email',
            'phone' => 'required|numeric|digits:8',
            'city' => 'nullable|string|max:60',
            'address' => 'nullable|string|max:255',
        ]);

        try {

            $user = User::create([
                'username' => $request->cui,
                'password' => Hash::make('password'),
            ]);

            $user->information()->create([
                'first_name' => ucwords(trim($request->first_name)),
                'last_name' => ucwords(trim($request->last_name)),
                'cui' => $request->cui,
                'gender' => $request->gender,
                'birthday' => $request->birthday,
                'email' => $request->email,
                'phone' => $request->phone,
                'city' => $request->city ?? null,
                'address' => $request->address ?? null,
            ]);
            

            return response([
                'user' => $user->load(['profile','information']),
                'message' => 'Create user successfully.'
            ]);

        } catch (\Throwable $th) {
            return response([
                'error' => $th->getMessage(),
                'message' => 'Error created user.'
            ],500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user) {
        try {
            return response([
                'user' => $user->load(['profile','information']),
                'message' => 'Get user successfully.'
            ]);
        } catch (\Throwable $th) {
            return response([
                'error' => $th->getMessage(),
                'message' => 'Error loading user.'
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user) {
        $request->validate([
            'information.first_name' => 'required|string|max:60',
            'information.last_name' => 'required|string|max:60',
            'information.cui' => 'required|numeric|digits:13|unique:user_information,cui,'.$request->information['id'],
            'information.gender' => 'required|in:F,M',
            'information.birthday' => 'required|date|date_format:Y-m-d',
            'information.email' => 'required|email|unique:user_information,email,'.$request->information['id'],
            'information.phone' => 'required|numeric|digits:8',
            'information.city' => 'nullable|string|max:60',
            'information.address' => 'nullable|string|max:255',
        ]);

        try {

            $user->information->first_name = $request->information['first_name'];
            $user->information->last_name = $request->information['last_name'];
            $user->information->cui = $request->information['cui'];
            $user->information->gender = $request->information['gender'];
            $user->information->birthday = $request->information['birthday'];
            $user->information->email = $request->information['email'];
            $user->information->phone = $request->information['phone'];
            $user->information->city = $request->information['city'] ?? null;
            $user->information->address = $request->information['address'] ?? null;
            $user->information->save();

            $user->profile_id = $request->profile_id ?? null;
            $user->save();

            return response([
                'message' => 'Data updated successfully.'
            ]);

        } catch (\Throwable $th) {
            return response([
                'error' => $th->getMessage(),
                'message' => 'Error in data update'
            ],500);
        }
    }

    /**
     * Disabled the specified resource from storage.
     */
    public function disabledUser(User $user) {
        try {
            $user->deleted_at = now();
            $user->save();
            return response([
                'message' => 'User disabled successfully.'
            ]);
        } catch (\Throwable $th) {
            return response([
                'error' => $th->getMessage(),
                'message' => 'Error disabled user.'
            ],500);
        }
    }
    /**
     * Reset the password resource from storage.
     */
    public function resetPassword(User $user) {
        try {
            $user->password = Hash::make($user::DEFAULTPASS);
            $user->save();
            return response([
                'message' => 'Reset password successfully'
            ]);
        } catch (\Throwable $th) {
            return response([
                'error' => $th->getMessage(),
                'message' => 'Error reset password.'
            ],500);
        }
    }
}