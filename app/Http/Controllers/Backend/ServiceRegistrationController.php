<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\ServiceRegistration;
use App\Models\Department;
use Illuminate\Http\Request;
use App\Events\StatusUpdated;
use Illuminate\Support\Str;

class ServiceRegistrationController extends Controller
{
    public function index()
    {
        $registrations = ServiceRegistration::with('department')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('backend.service-registrations.index', compact('registrations'));
    }

    public function store(Request $request)
    {
        // Sử dụng validation rules từ model
        $validator = \Validator::make($request->all(), ServiceRegistration::getRules(), ServiceRegistration::$messages);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Kiểm tra xem phòng ban có tồn tại và active không
            $department = Department::where('id', $request->department_id)
                ->where('status', 'active')
                ->first();

            if (!$department) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['department_id' => 'Phòng ban không hoạt động hoặc không tồn tại']);
            }

            // Tạo số thứ tự
            $queueNumber = $this->generateQueueNumber($request->department_id);

            $registration = ServiceRegistration::create([
                'full_name' => trim($request->full_name),
                'birth_year' => (int) $request->birth_year,
                'identity_number' => trim($request->identity_number),
                'email' => trim($request->email),
                'phone' => trim($request->phone),
                'department_id' => $request->department_id,
                'queue_number' => $queueNumber,
                'status' => 'pending'
            ]);

            return redirect()->back()
                ->with('success', 'Đăng ký thành công! Số thứ tự của bạn là: ' . $queueNumber);
        } catch (\Exception $e) {
            \Log::error('Lỗi đăng ký dịch vụ: ' . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->withErrors(['general' => 'Có lỗi xảy ra khi đăng ký dịch vụ. Vui lòng thử lại.']);
        }
    }

    public function show($id)
    {
        $registration = ServiceRegistration::with('department')->findOrFail($id);
        return view('backend.service-registrations.show', compact('registration'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,received,processing,completed,returned',
        ]);

        $registration = ServiceRegistration::find($id);

        if (!$registration) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy đăng ký.'
            ], 404);
        }

        $registration->status = $request->status;
        $registration->save();

        StatusUpdated::dispatch([
            'id'            => $registration->id,
            'queue_number'  => $registration->queue_number,
            'new_status'    => $registration->status,              // pending/received/...
            'full_name'     => $registration->full_name,
            'department'    => $registration->department->name,
            'created_at'    => $registration->created_at->format('H:i d/m/Y'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật trạng thái thành công.'
        ]);
    }

    public function destroy($id)
    {
        $registration = \App\Models\ServiceRegistration::findOrFail($id);
        $registration->delete();

        return redirect()->back()->with('success', 'Xóa thành công!');
    }

    private function generateQueueNumber($departmentId)
    {
        $department = Department::find($departmentId);
        if (!$department) {
            throw new \Exception('Không tìm thấy phòng ban');
        }

        $today = now()->format('Ymd');

        // Lấy số thứ tự cuối cùng của ngày hôm nay
        $lastRegistration = ServiceRegistration::where('department_id', $departmentId)
            ->whereDate('created_at', today())
            ->orderBy('id', 'desc')
            ->first();

        if ($lastRegistration) {
            $lastNumber = (int) substr($lastRegistration->queue_number, -3);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        // Format đơn giản: chỉ hiển thị số thứ tự
        return str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }
}
