<?php

namespace App\Http\Controllers\Backend;

use App\Exports\UsersExport;
use App\Http\Controllers\Controller;
use App\Imports\PermissionsImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Permission\Models\Permission;

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
    } // End Method

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
    } // End Method

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
}
