<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RolesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index() {
        try {
            $roles = Role::all();
            return response([
                'roles' => $roles,
                'message' => 'Get all roles successfully.'
            ]);
        } catch (\Throwable $th) {
                return response([
                    'error' => $th->getMessage(),
                    'message' => 'Error get roles.'
                ],500);        
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name'
        ]);

        try {

            $role = Role::create([
                'name' => $request->name
            ]);

            return response([
                'role' => $role,
                'message' => 'Role created successfully.'
            ]);
        } catch (\Throwable $th) {
                return response([
                    'error' => $th->getMessage(),
                    'message' => 'Error create role.'
                ],500);        
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role) {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,'.$role->id
        ]);

        try {

            $role->name = $request->name;
            $role->save();

            return response([
                'role' => $role,
                'message' => 'Role updated successfully.'
            ]);
        } catch (\Throwable $th) {
                return response([
                    'error' => $th->getMessage(),
                    'message' => 'Error update role.'
                ],500);        
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role) {
        try {
            $role->delete();

            return response([
                'role' => $role,
                'message' => 'Role deleted successfully.'
            ]);
        } catch (\Throwable $th) {
                return response([
                    'error' => $th->getMessage(),
                    'message' => 'Error delete role.'
                ],500);        
        }
    }
}