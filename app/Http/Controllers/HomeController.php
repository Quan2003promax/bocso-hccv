<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\ServiceRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        $departments = Department::where('status', 'active')->get();
        $pendingRegistrations = ServiceRegistration::with('department')
            ->whereIn('status', ['pending', 'received', 'returned'])
            ->orderByRaw("FIELD(status, 'pending','received','returned')")
            ->orderBy('created_at', 'asc')
            ->limit(10)
            ->get();

        return view('home', compact('departments', 'pendingRegistrations'));
    }

    public function register(Request $request)
    {
        // Debug: Log dữ liệu đầu vào
        \Log::info('Dữ liệu đăng ký:', $request->all());

        // Validation rules đơn giản hơn để test
        $request->validate([
            'full_name' => 'required',
            'birth_year' => 'required|integer',
            'identity_number' => 'required',
            'department_id' => 'required'
        ], [
            'full_name.required' => 'Vui lòng nhập họ và tên',
            'birth_year.required' => 'Vui lòng nhập năm sinh',
            'birth_year.integer' => 'Năm sinh phải là số nguyên',
            'identity_number.required' => 'Vui lòng nhập số căn cước công dân',
            'department_id.required' => 'Vui lòng chọn phòng ban'
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

            // Tạo số và lưu trong một transaction để tránh trùng số
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

            \Log::info('Đăng ký thành công:', ['queue_number' => $queueNumber, 'registration_id' => $registration->id]);

            return redirect()->back()
                ->with('success', 'Đăng ký thành công! Số thứ tự của bạn là: ' . $queueNumber);

        } catch (\Exception $e) {
            // Log lỗi để debug
            \Log::error('Lỗi đăng ký dịch vụ: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return redirect()->back()
                ->withInput()
                ->withErrors(['general' => 'Có lỗi xảy ra: ' . $e->getMessage()]);
        }
    }

    private function generateQueueNumber($departmentId, bool $updateCacheIfUsed = false)
    {
        $department = Department::find($departmentId);
        if (!$department) {
            throw new \Exception('Không tìm thấy phòng ban');
        }

        // Nếu admin đã reset, ưu tiên bộ đếm cache
        $manualCounter = Cache::get('global_queue_counter', 0);
        if ($manualCounter > 0) {
            $next = $manualCounter + 1;
            if ($updateCacheIfUsed) {
                Cache::put('global_queue_counter', $next);
            }
            return str_pad($next, 3, '0', STR_PAD_LEFT);
        }

        // Ngược lại: khóa bảng và tính theo bản ghi cuối cùng để tránh trùng
        $last = ServiceRegistration::lockForUpdate()->orderBy('id', 'desc')->first();
        $lastNumber = $last ? (int) preg_replace('/\\D/', '', $last->queue_number) : 0;
        $next = $lastNumber + 1;
        return str_pad($next, 3, '0', STR_PAD_LEFT);
    }
}
