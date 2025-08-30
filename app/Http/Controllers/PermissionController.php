<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;

class PermissionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:permission-list|permission-create|permission-edit|permission-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:permission-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:permission-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:permission-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $permissions = Permission::orderBy('name')->get();
        
        if ($request->ajax()) {
            return response()->json([
                'permissions' => $permissions,
            ]);
        }
        
        return view('permissions.index', compact('permissions'));
    }

    public function create()
    {
        return view('permissions.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:permissions,name',
            'guard_name' => 'nullable|string'
        ]);

        try {
            Permission::create([
                'name' => $request->name,
                'guard_name' => $request->guard_name ?? 'web'
            ]);

            return redirect()->route('permissions.index')
                            ->with('success', 'Quyền được thêm thành công');
        } catch (\Exception $e) {
            return redirect()->back()
                            ->withInput()
                            ->with('error', 'Có lỗi xảy ra khi tạo quyền: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $permission = Permission::with('roles')->findOrFail($id);
        return view('permissions.show', compact('permission'));
    }

    public function edit($id)
    {
        $permission = Permission::findOrFail($id);
        return view('permissions.edit', compact('permission'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => [
                'required',
                Rule::unique('permissions', 'name')->ignore($id)
            ],
            'guard_name' => 'nullable|string'
        ]);

        try {
            $permission = Permission::findOrFail($id);
            $permission->name = $request->name;
            if ($request->has('guard_name')) {
                $permission->guard_name = $request->guard_name;
            }
            $permission->save();

            return redirect()->route('permissions.index')
                            ->with('success', 'Quyền được cập nhật thành công');
        } catch (\Exception $e) {
            return redirect()->back()
                            ->withInput()
                            ->with('error', 'Có lỗi xảy ra khi cập nhật quyền: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $permission = Permission::findOrFail($id);
            
            // Kiểm tra xem quyền có đang được sử dụng bởi role nào không
            $rolesUsingPermission = $permission->roles()->count();
            if ($rolesUsingPermission > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể xóa quyền này vì đang được sử dụng bởi ' . $rolesUsingPermission . ' vai trò!'
                ]);
            }

            $permission->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Quyền đã được xóa thành công!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xóa quyền: ' . $e->getMessage()
            ]);
        }
    }
}
