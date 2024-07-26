<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use Exception;
use Illuminate\Http\Request;

class RolePermissionController extends Controller
{
    //
    public function createPermission(Request $request)
    {
        try {
            //code...
            $request->validate([
                'name' => 'required|string|unique:permissions,name'
            ]);

            $permission = Permission::create(['name' => $request->name]);

            return response()->json(["message" => "Permission created", "Permission" => $permission], 201);
        } catch (Exception $e) {
            //throw $e;
            return response()->json($e);
        }
    }

    public function createRole(Request $request)
    {
        try {
            //code...
            $request->validate([
                'name' => 'required|string|unique:roles,name',
                'permissions' => 'required|array',
                'permissions.*' => 'exists:permissions,id'
            ]);

            $role = Role::create(['name' => $request->name]);
            $permissions = Permission::whereIn('id', $request->permissions)->get();
            $role->syncPermissions($permissions);


            return response()->json(["message" => "Role created", "Role" => $role, "permissions" => $permissions->pluck('id', 'name')], 201);
        } catch (Exception $e) {
            //throw $e;
            return response()->json($e);
        }
    }
}
