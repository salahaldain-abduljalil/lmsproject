<?php

namespace App\Http\Controllers\Backend;

use App\Exports\UsersExport;
use App\Http\Controllers\Controller;
use App\Imports\PermissionsImport;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function AllPermission()
    {

        $permissions = Permission::all();
        return view('admin.backend.pages.permission.permission_all', compact('permissions'));
    }
    public function AddPermission()
    {

        return view('admin.backend.pages.permission.add_permission');
    }

    public function StorePermission(Request $request)
    {

        Permission::create([
            'name' => $request->name,
            'group_name' => $request->group_name,
        ]);

        $notification = array(
            'message' => 'Permission Created Successfully',
            'alert-type' => 'success'
        );
        return redirect()->route('all.permission')->with($notification);
    }
    public function EditPermission($id)
    {

        $permission = Permission::find($id);
        return view('admin.backend.pages.permission.edit_permission', compact('permission'));
    }

    public function UpdatePermission(Request $request)
    {

        $per_id = $request->id;

        Permission::find($per_id)->update([
            'name' => $request->name,
            'group_name' => $request->group_name,
        ]);

        $notification = array(
            'message' => 'Permission Updated Successfully',
            'alert-type' => 'success'
        );
        return redirect()->route('all.permission')->with($notification);
    }

    public function DeletePermission($id)
    {

        Permission::find($id)->delete();

        $notification = array(
            'message' => 'Permission Deleted Successfully',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification);
    }

    public function ImportPermission()
    {

        return view('admin.backend.pages.permission.import_permission');
    }
    public function Export()
    {

        return Excel::download(new UsersExport, 'permission.xlsx');
    }
    public function Import(Request $request)
    {

        Excel::import(new PermissionsImport, $request->file('import_file'));

        $notification = array(
            'message' => 'Permission Imported Successfully',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification);
    }

    public function AllRoles()
    {

        $roles = Role::all();
        return view('admin.backend.pages.roles.all_roles', compact('roles'));
    }

    public function AddRoles()
    {

        return view('admin.backend.pages.roles.add_roles');
    }

    public function StoreRoles(Request $request)
    {

        Role::create([
            'name' => $request->name,
        ]);

        $notification = array(
            'message' => 'Role Created Successfully',
            'alert-type' => 'success'
        );
        return redirect()->route('all.roles')->with($notification);
    }
    public function EditRoles($id)
    {

        $roles = Role::find($id);
        return view('admin.backend.pages.roles.edit_roles', compact('roles'));
    }

    public function UpdateRoles(Request $request)
    {

        $role_id = $request->id;

        Role::find($role_id)->update([
            'name' => $request->name,
        ]);

        $notification = array(
            'message' => 'Role Updated Successfully',
            'alert-type' => 'success'
        );
        return redirect()->route('all.roles')->with($notification);
    }

    public function DeleteRoles($id)
    {

        Role::find($id)->delete();

        $notification = array(
            'message' => 'Role Deleted Successfully',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification);
    }

    //////////// Add Role Permission All Mehtod ////////////////

    public function AddRolesPermission()
    {


        $roles = Role::all();
        $permission_groups = User::getpermissionGroups();

        return view('admin.backend.pages.rolesetup.add_roles_permission', compact('roles', 'permission_groups'));
    }
    public function RolePermissionStore(Request $request)
    {

        $data = array();
        $permissions = $request->permission;

        foreach ($permissions as $item) {
            $data['role_id'] = $request->role_id;
            $data['permission_id'] = $item;
            DB::table('role_has_permissions')->insert($data);
        }

        $notification = array(
            'message' => 'Role Permission Added Successfully',
            'alert-type' => 'success'
        );
        return redirect()->route('all.roles.permission')->with($notification);
    }

    public function AllRolesPermission()
    {

        $roles = Role::all();
        return view('admin.backend.pages.rolesetup.all_roles_permission', compact('roles'));
    }
    public function AdminEditRoles($id)
    {

        $role = Role::find($id);
        $permissions = Permission::all();
        $permission_groups = User::getpermissionGroups();

        return view('admin.backend.pages.rolesetup.edit_roles_permission', compact('role', 'permission_groups', 'permissions'));
    }

    public function AdminUpdateRoles(Request $request, $id)
    {

        $role = Role::find($id);
        //$permissions = $request->permission;
        $permissions = collect($request->input('permission'))
            ->map(fn ($val) => (int)$val)->all();

        if (!empty($permissions)) {
            $role->syncPermissions($permissions);
        }

        $notification = array(
            'message' => 'Role Permission Updated Successfully',
            'alert-type' => 'success'
        );
        return redirect()->route('all.roles.permission')->with($notification);
    }

    public function AdminDeleteRoles($id)
    {

        $role = Role::find($id);
        if (!is_null($role)) {
            $role->delete();
        }

        $notification = array(
            'message' => 'Role Permission Deleted Successfully',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification);
    }
}
