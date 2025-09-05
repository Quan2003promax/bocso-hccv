<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\ServiceRegistration;
use App\Models\Department;
use Illuminate\Http\Request;
use App\Events\StatusUpdated;
use App\Events\DeleteRegistration;
use Illuminate\Support\Str;

class ServiceRegistrationController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:service-registration-list|service-registration-create|service-registration-edit|service-registration-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:service-registration-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:service-registration-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:service-registration-delete', ['only' => ['destroy']]);
        $this->middleware('permission:service-registration-update-status', ['only' => ['updateStatus']]);
    }
    public function index(Request $request)
    {

        $userDepartmentIds = auth()->user()->departments->pluck('id')->toArray();
        $query = ServiceRegistration::with('department')
            ->whereIn('department_id', $userDepartmentIds);

        // Filter theo phòng ban nếu có
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        // Filter theo trạng thái nếu có
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter theo từ khóa tìm kiếm
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('queue_number', 'LIKE', "%{$search}%")
                    ->orWhere('full_name', 'LIKE', "%{$search}%")
                    ->orWhere('identity_number', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%")
                    ->orWhere('phone', 'LIKE', "%{$search}%")
                    ->orWhereHas('department', function ($subQuery) use ($search) {
                        $subQuery->where('name', 'LIKE', "%{$search}%");
                    });
            });
        }

        $registrations = $query->orderBy('created_at', 'desc')->paginate(20);

        // Lấy danh sách phòng ban để hiển thị trong filter
        $departments = Department::where('status', 'active')->get();

        // Lấy danh sách trạng thái để hiển thị trong filter
        $statuses = [
            'pending' => 'Chờ xử lý',
            'received' => 'Đã tiếp nhận',
            'processing' => 'Đang xử lý',
            'completed' => 'Hoàn thành',
            'returned' => 'Trả hồ sơ'
        ];

        return view('backend.service-registrations.index', compact('registrations', 'departments', 'statuses'));
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
            'id'           => $registration->id,
            'queue_number' => $registration->queue_number,
            'full_name'    => $registration->full_name,
            'department'   => $registration->department->name,
            'department_id' => $registration->department_id,
            'new_status'   => $registration->status,
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
        DeleteRegistration::dispatch([
            'id'            => $registration->id,
            'department_id' => $registration->department_id,
        ]);
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
