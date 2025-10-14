<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index() {
        try {
            $permissions = Permission::all();

            return response([
                'permission' => $permissions,
                'message' => 'Get all permissions successfully.'
            ]);
        } catch (\Throwable $th) {
            return response([
                'error' => $th->getMessage(),
                'message' => 'Failed to get permissions.'
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {
        
        $request->validate([
            'name' => 'required|string|unique:permissions,name',
            'app' => 'required|string',
            'group' => 'required|string',
        ]);

        try {
            $permission = Permission::create([
                'name' => $request->name,
                'guard_name' => 'web',
                'app' => $request->app,
                'group' => $request->group,
            ]);

            return response([
                'permission' => $permission,
                'message' => 'Permissions created successfully.'
            ]);
        } catch (\Throwable $th) {
            return response([
                'error' => $th->getMessage(),
                'message' => 'Error created permission.'
            ]);
        }
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Permission $permission) {

         $request->validate([
            'name' => 'required|string|unique:permissions,name',
            'app' => 'required|string',
            'group' => 'required|string',
        ]);

        try {
           
            $permission->name = $request->name;
            $permission->guard_name = 'web';
            $permission->app = $request->app;
            $permission->group = $request->group;
            $permission->save();

            return response([
                'permission' => $permission,
                'message' => 'Permissions updated successfully.'
            ]);
        } catch (\Throwable $th) {
            return response([
                'error' => $th->getMessage(),
                'message' => 'Error updated permission.'
            ]);
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
            ]);
        }
    }
}