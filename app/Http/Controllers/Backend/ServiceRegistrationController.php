<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\ServiceRegistration;
use App\Models\Department;
use Illuminate\Http\Request;

class ServiceRegistrationController extends Controller
{
    public function index()
    {
        $registrations = ServiceRegistration::with('department')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('backend.service-registrations.index', compact('registrations'));
    }

    public function show(ServiceRegistration $registration)
    {
        return view('backend.service-registrations.show', compact('registration'));
    }

    public function updateStatus(Request $request, ServiceRegistration $registration)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled',
            'notes' => 'nullable|string'
        ]);

        $registration->update([
            'status' => $request->status,
            'notes' => $request->notes
        ]);

        return redirect()->back()->with('success', 'Trạng thái đã được cập nhật thành công!');
    }

    public function destroy(ServiceRegistration $registration)
    {
        $registration->delete();

        return redirect()->route('admin.service-registrations.index')
            ->with('success', 'Đăng ký dịch vụ đã được xóa thành công!');
    }
}
