<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Permission;

class PermissionsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index() {
        try {
            $permissions = Permission::latest('id')->get();

            return response([
                'permissions' => $permissions,
                'message' => 'Get all permissions successfully.'
            ]);
        } catch (\Throwable $th) {
            return response([
                'error' => $th->getMessage(),
                'message' => 'Failed to get permissions.'
            ],500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {
        
        $request->validate([
            'name' => 'required|string|unique:permissions,name',
            'module' => 'required|string|max:255',

        ]);

        try {
            $permission = Permission::create([
                'name' => mb_strtolower(trim($request->name)),
                'module' => mb_strtolower(trim($request->module)),
                'guard_name' => 'web',
            ]);

            return response([
                'permission' => $permission,
                'message' => 'Permissions created successfully.'
            ]);
        } catch (\Throwable $th) {
            return response([
                'error' => $th->getMessage(),
                'message' => 'Error created permission.'
            ],500);
        }
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Permission $permission) {

         $request->validate([
            'name' => 'required|string|unique:permissions,name,'.$permission->id,
            'module' => 'required|string|max:255',
        ]);

        try {
           
            $permission->name = mb_strtolower(trim($request->name));
            $permission->module = mb_strtolower(trim($request->module));
            $permission->guard_name = 'web';
            $permission->save();

            return response([
                'permission' => $permission,
                'message' => 'Permissions updated successfully.'
            ]);
        } catch (\Throwable $th) {
            return response([
                'error' => $th->getMessage(),
                'message' => 'Error updated permission.'
            ],500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Permission $permission) {
        try {
            $permission->delete();
            return response([
                'permission' => $permission,
                'message' => 'Deleted permission successfully.'
            ]);
        } catch (\Throwable $th) {
            return response([
                'error' => $th->getMessage(),
                'message' => 'Failed deleted permission.'
            ],500);
        }
    }
}
