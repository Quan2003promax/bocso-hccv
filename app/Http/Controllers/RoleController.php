<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Auth;
use DB;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:role-list|role-create|role-edit|role-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:role-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:role-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:role-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $roles = Role::with('permissions')->orderBy('id', 'DESC')->paginate(10);
        return view('roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::orderBy('name')->get();
        return view('roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:roles,name',
            'permission' => 'required|array',
            'permission.*' => 'exists:permissions,id'
        ]);

        try {
            DB::beginTransaction();
            
            $role = Role::create(['name' => $request->input('name')]);
            $role->syncPermissions($request->input('permission'));

            DB::commit();

            return redirect()->route('roles.index')
                            ->with('success', 'Vai trò được tạo thành công');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                            ->withInput()
                            ->with('error', 'Có lỗi xảy ra khi tạo vai trò: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $role = Role::with('permissions')->findOrFail($id);
        return view('roles.show', compact('role'));
    }

    public function edit($id)
    {
        $role = Role::findOrFail($id);
        
        if ($role->name == 'Super-Admin') {
            return redirect()->route('roles.index')
                            ->with('error', 'Bạn không có quyền chỉnh sửa vai trò này');
        }

        $permissions = Permission::orderBy('name')->get();
        $rolePermissions = $role->permissions->pluck('id')->toArray();

        return view('roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => [
                'required',
                Rule::unique('roles', 'name')->ignore($id)
            ],
            'permission' => 'required|array',
            'permission.*' => 'exists:permissions,id'
        ]);

        try {
            DB::beginTransaction();
            
            $role = Role::findOrFail($id);
            $role->name = $request->input('name');
            $role->save();

            $role->syncPermissions($request->input('permission'));

            DB::commit();

            return redirect()->route('roles.index')
                            ->with('success', 'Vai trò được cập nhật thành công');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                            ->withInput()
                            ->with('error', 'Có lỗi xảy ra khi cập nhật vai trò: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $role = Role::findOrFail($id);

        if (auth()->user()->roles->contains($id)) {
            return redirect()->route('roles.index')
                            ->with('error', 'Bạn không có quyền xóa vai trò này');
        }
        
        if ($role->name == "Super-Admin") {
            return redirect()->route('roles.index')
                            ->with('error', 'Bạn không có quyền xóa vai trò Super-Admin');
        }

        try {
            $role->delete();
            return redirect()->route('roles.index')
                            ->with('success', 'Vai trò được xóa thành công');
        } catch (\Exception $e) {
            return redirect()->route('roles.index')
                            ->with('error', 'Có lỗi xảy ra khi xóa vai trò: ' . $e->getMessage());
        }
    }
}
