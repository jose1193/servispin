<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\JsonResponse;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

 public function __construct()
{
    $this->middleware('permission:manage manager')->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
}


    public function index()
{
    $roles = Role::orderBy('id', 'DESC')->get();
    return response()->json(['roles' => $roles],200);
     
}

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
{
    $permissions = Permission::orderBy('id', 'DESC')->get();
    return response()->json(['permissions' => $permissions], 200);
}

    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

 public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:roles,name',
            'permission' => 'required',
        ]);
    
        $role = Role::create(['name' => $request->input('name')]);
        $role->syncPermissions($request->input('permission'));
    
          return response()->json(['message' => 'Role created successfully'], 200);
    }

   
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
       

        $role = Role::findOrFail($id);
        $rolePermissions = Permission::join("role_has_permissions", "role_has_permissions.permission_id", "=", "permissions.id")
            ->where("role_has_permissions.role_id", $id)
            ->get();

        return response()->json(['role' => $role, 'rolePermissions' => $rolePermissions], 200);
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
    

        $role = Role::findOrFail($id);
        $permissions = Permission::all();
        $rolePermissions = DB::table("role_has_permissions")->where("role_id", $id)
            ->pluck('permission_id')
            ->all();

        return response()->json(['role' => $role, 'permissions' => $permissions, 'rolePermissions' => $rolePermissions], 200);
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
     

        $request->validate([
            'name' => 'required',
            'permission' => 'required|array',
            'permission.*' => 'exists:permissions,id',
        ]);

        $role = Role::findOrFail($id);
        $role->name = $request->input('name');
        $role->save();

        $role->syncPermissions($request->input('permission'));

        return response()->json(['message' => 'Role updated successfully'], 200);
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
       

        Role::findOrFail($id)->delete();

        return response()->json(['message' => 'Role deleted successfully'], 200);
    }
}
