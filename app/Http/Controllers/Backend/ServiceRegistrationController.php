<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\ServiceRegistration;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

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
        $request->validate([
            'full_name' => 'required|string|max:255',
            'birth_year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'identity_number' => 'required|string|max:20',
            'department_id' => 'required|exists:departments,id'
        ], [
            'full_name.required' => 'Vui lòng nhập họ và tên',
            'full_name.string' => 'Họ và tên phải là chuỗi ký tự',
            'full_name.max' => 'Họ và tên không được quá 255 ký tự',
            'birth_year.required' => 'Vui lòng nhập năm sinh',
            'birth_year.integer' => 'Năm sinh phải là số nguyên',
            'birth_year.min' => 'Năm sinh không hợp lệ',
            'birth_year.max' => 'Năm sinh không hợp lệ',
            'identity_number.required' => 'Vui lòng nhập số căn cước công dân',
            'identity_number.string' => 'Số căn cước công dân phải là chuỗi ký tự',
            'identity_number.max' => 'Số căn cước công dân không được quá 20 ký tự',
            'department_id.required' => 'Vui lòng chọn phòng ban',
            'department_id.exists' => 'Phòng ban không tồn tại'
        ]);

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

            // Transaction để đảm bảo không trùng số
            [$registration, $queueNumber] = DB::transaction(function () use ($request) {
                $queueNumber = $this->generateQueueNumber($request->department_id, true);

                $registration = ServiceRegistration::create([
                    'full_name' => trim($request->full_name),
                    'birth_year' => (int) $request->birth_year,
                    'identity_number' => trim($request->identity_number),
                    'department_id' => $request->department_id,
                    'queue_number' => $queueNumber,
                    'status' => 'pending'
                ]);

                return [$registration, $queueNumber];
            });

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

    public function updateStatus(Request $request, ServiceRegistration $registration)
    {
        $request->validate([
            'status' => 'required|in:pending,received,processing,completed,returned',
        ], [
            'status.required' => 'Trạng thái là bắt buộc',
            'status.in' => 'Trạng thái không hợp lệ'
        ]);

        try {
            $oldStatus = $registration->status;
            $registration->update([
                'status' => $request->status
            ]);

            // Log thay đổi trạng thái
            \Log::info("Trạng thái đăng ký #{$registration->id} thay đổi từ '{$oldStatus}' sang '{$request->status}'");

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Trạng thái đã được cập nhật thành công!',
                    'data' => [
                        'id' => $registration->id,
                        'status' => $registration->status,
                        'old_status' => $oldStatus
                    ]
                ]);
            }

            return redirect()->back()->with('success', 'Trạng thái đã được cập nhật thành công!');
            
        } catch (\Exception $e) {
            \Log::error('Lỗi cập nhật trạng thái: ' . $e->getMessage());
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Có lỗi xảy ra khi cập nhật trạng thái'
                ], 500);
            }

            return redirect()->back()->withErrors(['general' => 'Có lỗi xảy ra khi cập nhật trạng thái']);
        }
    }

    public function destroy($id)
    {
        $registration = \App\Models\ServiceRegistration::findOrFail($id);
        $registration->delete();

        return redirect()->back()->with('success', 'Xóa thành công!');
    }

    public function resetQueue()
    {
        Cache::put('global_queue_counter', 0);
        return redirect()->back()->with('success', 'Đã reset số thứ tự về 000');
    }

    private function generateQueueNumber($departmentId, bool $updateCacheIfUsed = false)
    {
        $department = Department::find($departmentId);
        if (!$department) {
            throw new \Exception('Không tìm thấy phòng ban');
        }
        // Nếu admin vừa reset thì ưu tiên cache counter
        $manualCounter = Cache::get('global_queue_counter', 0);
        if ($manualCounter > 0) {
            $next = $manualCounter + 1;
            if ($updateCacheIfUsed) {
                Cache::put('global_queue_counter', $next);
            }
            return str_pad($next, 3, '0', STR_PAD_LEFT);
        }

        // Không dùng cache: tính theo DB với lock để tránh trùng
        $last = ServiceRegistration::lockForUpdate()->orderBy('id', 'desc')->first();
        $lastNumber = $last ? (int) preg_replace('/\\D/', '', $last->queue_number) : 0;
        $next = $lastNumber + 1;
        return str_pad($next, 3, '0', STR_PAD_LEFT);
    }
}
