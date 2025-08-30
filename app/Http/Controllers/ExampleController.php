<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ExampleController extends Controller
{
    /**
     * Constructor với middleware kiểm tra quyền và phòng ban
     */
    public function __construct()
    {
        // Kiểm tra quyền cơ bản
        $this->middleware('permission:user-list');
        
        // Kiểm tra quyền và phòng ban cụ thể
        $this->middleware('check.department.permission:user-edit,IT')->only(['edit', 'update']);
        
        // Kiểm tra chỉ phòng ban
        $this->middleware('check.department.permission:null,HR')->only(['hrOnly']);
        
        // Kiểm tra chỉ quyền
        $this->middleware('check.department.permission:user-delete')->only(['destroy']);
    }

    /**
     * Hiển thị danh sách - yêu cầu quyền user-list
     */
    public function index()
    {
        return response()->json([
            'message' => 'Bạn có quyền xem danh sách',
            'user' => auth()->user()->name,
            'department' => auth()->user()->department ? auth()->user()->department->name : 'Chưa phân công'
        ]);
    }

    /**
     * Chỉnh sửa - yêu cầu quyền user-edit VÀ thuộc phòng ban IT
     */
    public function edit($id)
    {
        return response()->json([
            'message' => 'Bạn có quyền chỉnh sửa và thuộc phòng ban IT',
            'user' => auth()->user()->name,
            'department' => auth()->user()->department->name,
            'editing_user_id' => $id
        ]);
    }

    /**
     * Cập nhật - yêu cầu quyền user-edit VÀ thuộc phòng ban IT
     */
    public function update(Request $request, $id)
    {
        return response()->json([
            'message' => 'Cập nhật thành công',
            'user' => auth()->user()->name,
            'department' => auth()->user()->department->name,
            'updated_user_id' => $id
        ]);
    }

    /**
     * Chỉ dành cho phòng ban HR - không yêu cầu quyền cụ thể
     */
    public function hrOnly()
    {
        return response()->json([
            'message' => 'Chỉ dành cho phòng ban HR',
            'user' => auth()->user()->name,
            'department' => auth()->user()->department->name
        ]);
    }

    /**
     * Xóa - yêu cầu quyền user-delete
     */
    public function destroy($id)
    {
        return response()->json([
            'message' => 'Xóa thành công',
            'user' => auth()->user()->name,
            'deleted_user_id' => $id
        ]);
    }

    /**
     * Kiểm tra quyền của user hiện tại
     */
    public function checkPermissions()
    {
        $user = auth()->user();
        
        return response()->json([
            'user' => $user->name,
            'department' => $user->department ? $user->department->name : 'Chưa phân công',
            'roles' => $user->getRoleNames(),
            'permissions' => $user->getAllPermissions()->pluck('name'),
            'has_user_edit' => $user->hasPermissionTo('user-edit'),
            'has_user_delete' => $user->hasPermissionTo('user-delete'),
            'belongs_to_it' => $user->belongsToDepartment('IT'),
            'has_permission_in_department' => $user->hasPermissionInDepartment('user-edit', 'IT')
        ]);
    }
}
