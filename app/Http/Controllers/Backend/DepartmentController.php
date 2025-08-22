<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::orderBy('created_at', 'desc')->get();
        return view('backend.departments.index', compact('departments'));
    }

    public function create()
    {
        return view('backend.departments.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:departments',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive'
        ]);

        Department::create($request->all());

        return redirect()->route('admin.departments.index')
            ->with('success', 'Phòng ban đã được tạo thành công!');
    }

    public function edit(Department $department)
    {
        return view('backend.departments.edit', compact('department'));
    }

    public function update(Request $request, Department $department)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:departments,name,' . $department->id,
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive'
        ]);

        $department->update($request->all());

        return redirect()->route('admin.departments.index')
            ->with('success', 'Phòng ban đã được cập nhật thành công!');
    }

    public function destroy(Department $department)
    {
        $department->delete();

        return redirect()->route('admin.departments.index')
            ->with('success', 'Phòng ban đã được xóa thành công!');
    }
}
