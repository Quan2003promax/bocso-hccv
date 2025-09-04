<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Department;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Arr;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:user-list|user-create|user-edit|user-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:user-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:user-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:user-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $users = User::with(['roles', 'permissions', 'departments'])
            ->orderBy('id', 'DESC')
            ->paginate(10);

        return view('users.index', compact('users'));
    }

    public function create()
    {
        if (auth()->user()->hasRole('Super-Admin')) {
            $roles = Role::pluck('name', 'name')->all();
        } else {
            $roles = Role::pluck('name', 'name')->except(['Super-Admin']);
        }

        $departments = Department::where('status', 'active')->pluck('name', 'id')->all();
        $permissions = Permission::orderBy('name')->pluck('name', 'id')->all();

        return view('users.create', compact('roles', 'departments', 'permissions'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'confirm-password' => 'required|same:password',
            'roles' => 'required|array',
            'departments' => 'nullable|array',
            'departments.*' => 'exists:departments,id',
        ]);
        //  dd($request->all());

        try {
            DB::beginTransaction();

            $input = $request->all();
            $input['password'] = Hash::make($input['password']);

            $user = User::create($input);
            
            // Gán vai trò
            $user->assignRole($request->input('roles'));
            
            // Gán quyền trực tiếp nếu có
            if ($request->has('permissions')) {
                $permissionIds = $request->input('permissions');
                $permissions = Permission::whereIn('id', $permissionIds)->pluck('name')->toArray();
                $user->givePermissionTo($permissions);
            }

            // Gán phòng ban (N-N)
            if ($request->has('department_ids')) {
                $user->departments()->sync($request->input('department_ids'));
            }


            DB::commit();

            return redirect()->route('users.index')
                ->with('success', 'Người dùng được tạo thành công');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function show($id)
    {
        $user = User::with(['roles', 'permissions', 'departments'])->findOrFail($id);
        return view('users.show', compact('user'));
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        
        if ($user->hasRole('Super-Admin')) {
            return redirect()->route('users.index')
                ->with('error', 'Bạn không có quyền chỉnh sửa người dùng này');
        }

        if (auth()->user()->hasRole('Super-Admin')) {
            $roles = Role::pluck('name', 'name')->all();
        } else {
            $roles = Role::pluck('name', 'name')->except(['Super-Admin']);
        }

        $departments = Department::where('status', 'active')->pluck('name', 'id')->all();
        $permissions = Permission::orderBy('name')->pluck('name', 'id')->all();
        
        $userRole = $user->roles->pluck('name', 'name')->all();
        $userPermissions = $user->permissions->pluck('id')->toArray();

        return view('users.edit', compact('user', 'roles', 'departments', 'permissions', 'userRole', 'userPermissions'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8|same:confirm-password',
            'confirm-password' => 'nullable|required_with:password|same:password',
            'roles' => 'required|array',
            'department_ids' => 'nullable|array',
            'department_ids.*' => 'exists:departments,id',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        try {
            DB::beginTransaction();

            $input = $request->all();
            if (!empty($input['password'])) {
                $input['password'] = Hash::make($input['password']);
            } else {
                $input = Arr::except($input, ['password']);
            }

            $user = User::findOrFail($id);
            $user->update($input);

            // Cập nhật vai trò
            $user->syncRoles($request->input('roles'));

            // Cập nhật quyền trực tiếp
            $user->syncPermissions([]); // Xóa tất cả quyền trực tiếp
            if ($request->has('permissions')) {
                $permissionIds = $request->input('permissions');
                $permissions = Permission::whereIn('id', $permissionIds)->pluck('name')->toArray();
                $user->givePermissionTo($permissions);
            }

            // Cập nhật phòng ban (quan hệ n-n)
            if ($request->has('department_ids')) {
                $user->departments()->sync($request->input('department_ids'));
            } else {
                $user->departments()->detach(); // nếu không chọn phòng ban nào thì xóa hết
            }

            DB::commit();

            return redirect()->route('users.index')
                ->with('success', 'Người dùng được cập nhật thành công');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra khi cập nhật người dùng: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        if (auth()->id() == $id) {
            return redirect()->route('users.index')
                ->with('error', 'Bạn không thể xóa chính bạn');
        }

        if ($user->hasRole('Super-Admin')) {
            return redirect()->route('users.index')
                ->with('error', 'Bạn không có quyền xóa người dùng này');
        }

        try {
            $user->delete();
            return redirect()->route('users.index')
                ->with('success', 'Người dùng được xóa thành công');
        } catch (\Exception $e) {
            return redirect()->route('users.index')
                ->with('error', 'Có lỗi xảy ra khi xóa người dùng: ' . $e->getMessage());
        }
    }
}
