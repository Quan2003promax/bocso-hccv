<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\ServiceRegistration;
use App\Http\Resources\DepartmentResource;
use App\Http\Resources\ServiceRegistrationResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ServiceApiController extends Controller
{
    /**
     * Đăng ký dịch vụ qua API
     */
    public function register(Request $request)
    {
        try {
            // Sử dụng validation rules từ model
            $validator = Validator::make($request->all(), ServiceRegistration::getRules(), ServiceRegistration::$messages);

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

            // Tạo số thứ tự
            $queueNumber = $this->generateQueueNumber($request->department_id);

            // Tạo đăng ký
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
