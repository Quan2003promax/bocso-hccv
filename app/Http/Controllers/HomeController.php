<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\ServiceRegistration;
use App\Events\RegistrationCreated;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class HomeController extends Controller
{
    public function index()
    {
        $departments = Department::where('status', 'active')->get();
        $pendingRegistrations = ServiceRegistration::with('department')
            // ->whereIn('status', ['pending', 'received','processing', 'completed', 'returned']) case full field status
            ->whereIn('status', ['pending', 'received', 'processing', 'returned']) // case short field status
            ->orderByRaw("FIELD(status, 'pending', 'received','processing', 'completed', 'returned')")
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

            // Tạo số thứ tự
            $queueNumber = $this->generateQueueNumber($request->department_id);

            $registration = ServiceRegistration::create([
                'full_name' => trim($request->full_name),
                'birth_year' => (int) $request->birth_year,
                'identity_number' => trim($request->identity_number),
                'department_id' => $request->department_id,
                'queue_number' => $queueNumber,
                'status' => 'pending'
            ]);
            //fire event
            RegistrationCreated::dispatch([
                'id'           => $registration->id,
                'queue_number' => $registration->queue_number,
                'full_name'    => $registration->full_name,
                'department'   => $registration->department->name,
                'created_at'   => $registration->created_at->format('H:i d/m/Y'),
                'status'       => $registration->status,
                'show_url'     => route('admin.service-registrations.show', $registration),
                'delete_url'   => route('admin.service-registrations.destroy', $registration),
            ]);
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
