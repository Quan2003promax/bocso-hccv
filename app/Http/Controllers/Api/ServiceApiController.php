<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\ServiceRegistration;
use App\Http\Resources\DepartmentResource;
use App\Http\Resources\ServiceRegistrationResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ServiceApiController extends Controller
{
    /**
     * Đăng ký dịch vụ qua API
     */
    public function register(Request $request)
    {
        try {
            // Validation
            $validator = Validator::make($request->all(), [
                'full_name' => 'required|string|max:255',
                'birth_year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
                'identity_number' => 'required|string|max:20',
                'department_id' => 'required|exists:departments,id'
            ], [
                'full_name.required' => 'Vui lòng nhập họ và tên',
                'birth_year.required' => 'Vui lòng nhập năm sinh',
                'birth_year.integer' => 'Năm sinh phải là số nguyên',
                'birth_year.min' => 'Năm sinh không hợp lệ',
                'birth_year.max' => 'Năm sinh không hợp lệ',
                'identity_number.required' => 'Vui lòng nhập số căn cước công dân',
                'department_id.required' => 'Vui lòng chọn phòng ban',
                'department_id.exists' => 'Phòng ban không tồn tại'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Kiểm tra phòng ban
            $department = Department::where('id', $request->department_id)
                ->where('status', 'active')
                ->first();

            if (!$department) {
                return response()->json([
                    'success' => false,
                    'message' => 'Phòng ban không hoạt động hoặc không tồn tại'
                ], 400);
            }

            // Transaction để tránh trùng số
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

            // Load relationship để sử dụng Resource
            $registration->load('department');

            return response()->json([
                'success' => true,
                'message' => 'Đăng ký thành công',
                'data' => [
                    'registration' => new ServiceRegistrationResource($registration),
                    'estimated_wait_time' => $this->getEstimatedWaitTime($request->department_id)
                ]
            ], 201);

        } catch (\Exception $e) {
            \Log::error('API Error - Service Registration: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra, vui lòng thử lại sau',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Lấy danh sách phòng ban
     */
    public function getDepartments()
    {
        try {
            $departments = Department::where('status', 'active')->get();

            return response()->json([
                'success' => true,
                'data' => DepartmentResource::collection($departments)
            ]);

        } catch (\Exception $e) {
            \Log::error('API Error - Get Departments: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi lấy danh sách phòng ban'
            ], 500);
        }
    }

    /**
     * Lấy trạng thái hàng đợi
     */
    public function getQueueStatus(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'department_id' => 'nullable|exists:departments,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $query = ServiceRegistration::with('department')
                ->where('status', 'pending');

            if ($request->department_id) {
                $query->where('department_id', $request->department_id);
            }

            $pendingRegistrations = $query->orderBy('created_at', 'asc')
                ->limit(20)
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'pending_count' => $pendingRegistrations->count(),
                    'registrations' => ServiceRegistrationResource::collection($pendingRegistrations),
                    'last_updated' => now()->toISOString()
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('API Error - Get Queue Status: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi lấy trạng thái hàng đợi'
            ], 500);
        }
    }

    /**
     * Kiểm tra số thứ tự
     */
    public function checkQueueNumber(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'queue_number' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vui lòng nhập số thứ tự',
                    'errors' => $validator->errors()
                ], 422);
            }

            $registration = ServiceRegistration::with('department')
                ->where('queue_number', $request->queue_number)
                ->first();

            if (!$registration) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy số thứ tự này'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => new ServiceRegistrationResource($registration)
            ]);

        } catch (\Exception $e) {
            \Log::error('API Error - Check Queue Number: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi kiểm tra số thứ tự'
            ], 500);
        }
    }

    /**
     * Lấy thống kê tổng quan
     */
    public function getStatistics()
    {
        try {
            $totalDepartments = Department::where('status', 'active')->count();
            $totalRegistrations = ServiceRegistration::count();
            $pendingRegistrations = ServiceRegistration::where('status', 'pending')->count();
            $completedRegistrations = ServiceRegistration::where('status', 'completed')->count();
            $processingRegistrations = ServiceRegistration::where('status', 'processing')->count();

            // Thống kê theo phòng ban
            $departmentStats = Department::withCount(['serviceRegistrations' => function($query) {
                $query->where('status', 'pending');
            }])->where('status', 'active')->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'overview' => [
                        'total_departments' => $totalDepartments,
                        'total_registrations' => $totalRegistrations,
                        'pending_registrations' => $pendingRegistrations,
                        'completed_registrations' => $completedRegistrations,
                        'processing_registrations' => $processingRegistrations
                    ],
                    'department_stats' => DepartmentResource::collection($departmentStats),
                    'last_updated' => now()->toISOString()
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('API Error - Get Statistics: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi lấy thống kê'
            ], 500);
        }
    }

    /**
     * Tạo số thứ tự
     */
    private function generateQueueNumber($departmentId, bool $updateCacheIfUsed = false)
    {
        $department = Department::find($departmentId);
        if (!$department) {
            throw new \Exception('Không tìm thấy phòng ban');
        }
        // Nếu admin reset: dùng counter cache
        $manualCounter = Cache::get('global_queue_counter', 0);
        if ($manualCounter > 0) {
            $next = $manualCounter + 1;
            if ($updateCacheIfUsed) {
                Cache::put('global_queue_counter', $next);
            }
            return str_pad($next, 3, '0', STR_PAD_LEFT);
        }

        // Không dùng cache: lock để lấy số mới nhất, tránh trùng
        $last = ServiceRegistration::lockForUpdate()->orderBy('id', 'desc')->first();
        $lastNumber = $last ? (int) preg_replace('/\\D/', '', $last->queue_number) : 0;
        $next = $lastNumber + 1;
        return str_pad($next, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Ước tính thời gian chờ
     */
    private function getEstimatedWaitTime($departmentId)
    {
        $pendingCount = ServiceRegistration::where('department_id', $departmentId)
            ->where('status', 'pending')
            ->count();

        // Ước tính 5 phút cho mỗi người
        $estimatedMinutes = $pendingCount * 5;
        
        if ($estimatedMinutes < 60) {
            return $estimatedMinutes . ' phút';
        } else {
            $hours = floor($estimatedMinutes / 60);
            $minutes = $estimatedMinutes % 60;
            return $hours . ' giờ ' . $minutes . ' phút';
        }
    }
}
