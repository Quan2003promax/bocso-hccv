<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\ServiceRegistration;
use App\Events\RegistrationCreated;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use App\Jobs\ProcessDocumentUpload;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $departments = Department::where('status', 'active')->get();
        
        // Xử lý search theo số thứ tự
        $searchQuery = $request->get('search');
        $searchResults = collect();
        
        if ($searchQuery) {
            $searchResults = ServiceRegistration::with('department')
                ->where('queue_number', 'LIKE', "%{$searchQuery}%")
                ->orWhere('full_name', 'LIKE', "%{$searchQuery}%")
                ->orWhere('identity_number', 'LIKE', "%{$searchQuery}%")
                ->orWhereHas('department', function($query) use ($searchQuery) {
                    $query->where('name', 'LIKE', "%{$searchQuery}%");
                })
                ->orderBy('created_at', 'desc')
                ->get();
        }
        
        $pendingRegistrations = ServiceRegistration::with('department')
            // ->whereIn('status', ['pending', 'received','processing', 'completed', 'returned']) case full field status
            ->whereIn('status', ['pending', 'received', 'processing', 'returned']) // case short field status
            ->orderByRaw("FIELD(status, 'pending', 'received','processing', 'completed', 'returned')")
            ->orderBy('created_at', 'asc')
            ->get();

        return view('home', compact('departments', 'pendingRegistrations', 'searchQuery', 'searchResults'));
    }

    public function search(Request $request)
    {
        $searchQuery = (string) $request->get('search');

        // Với route search.queue, luôn trả JSON (phục vụ AJAX)
        if ($request->routeIs('search.queue') || $request->expectsJson() || $request->ajax()) {
            if (trim($searchQuery) === '') {
                return response()->json([
                    'found' => false,
                    'message' => 'Vui lòng nhập số thứ tự'
                ], 422);
            }

            $registrations = ServiceRegistration::with('department')
                ->where('queue_number', trim($searchQuery))
                ->orderBy('created_at', 'desc')
                ->get();

            if ($registrations->isEmpty()) {
                return response()->json([
                    'found' => false,
                    'message' => 'Không tìm thấy số thứ tự phù hợp'
                ]);
            }

            $items = $registrations->map(function ($r) {
                return [
                    'id' => $r->id,
                    'queue_number' => $r->queue_number,
                    'full_name' => $r->full_name,
                    'identity_number' => $r->identity_number,
                    'department' => optional($r->department)->name,
                    'status' => $r->status,
                    'created_at' => optional($r->created_at)->format('H:i d/m/Y'),
                ];
            })->values();

            return response()->json([
                'found' => true,
                'total' => $items->count(),
                'items' => $items,
            ]);
        }

        // Fallback: non-AJAX search returns to home with results (kept for compatibility)
        if (!$searchQuery) {
            return redirect()->route('home');
        }

        $searchResults = ServiceRegistration::with('department')
            ->where('queue_number', 'LIKE', "%{$searchQuery}%")
            ->orWhere('full_name', 'LIKE', "%{$searchQuery}%")
            ->orWhere('identity_number', 'LIKE', "%{$searchQuery}%")
            ->orWhereHas('department', function($query) use ($searchQuery) {
                $query->where('name', 'LIKE', "%{$searchQuery}%");
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $departments = Department::where('status', 'active')->get();
        $pendingRegistrations = ServiceRegistration::with('department')
            ->whereIn('status', ['pending', 'received', 'processing', 'returned'])
            ->orderByRaw("FIELD(status, 'pending', 'received','processing', 'completed', 'returned')")
            ->orderBy('created_at', 'asc')
            ->limit(10)
            ->get();

        return view('home', compact('departments', 'pendingRegistrations', 'searchQuery', 'searchResults'));
    }

    public function register(Request $request)
    {
        // Debug: Log dữ liệu đầu vào
        \Log::info('Dữ liệu đăng ký:', $request->all());

        // Sử dụng validation rules từ model
        $validator = Validator::make($request->all(), ServiceRegistration::getRules(), ServiceRegistration::$messages);

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

            // Xử lý file upload nếu có
            $documentData = $this->handleFileUpload($request);

            // Tạo số thứ tự
            $queueNumber = $this->generateQueueNumber($request->department_id);

            // Tạo dữ liệu đăng ký
            $registrationData = [
                'full_name' => trim($request->full_name),
                'birth_year' => (int) $request->birth_year,
                'identity_number' => trim($request->identity_number),
                'email' => trim($request->email),
                'phone' => trim($request->phone),
                'department_id' => $request->department_id,
                'queue_number' => $queueNumber,
                'status' => 'pending'
            ];

            // Thêm thông tin file nếu có upload
            if ($documentData) {
                $registrationData = array_merge($registrationData, $documentData);
            }

            $registration = ServiceRegistration::create($registrationData);

            // Dispatch job xử lý file upload bất đồng bộ nếu có file
            if ($documentData) {
                ProcessDocumentUpload::dispatch($registration->id, $documentData['document_file'], $documentData['document_original_name']);
            }

            \Log::info('Đăng ký thành công:', [
                'queue_number' => $queueNumber, 
                'registration_id' => $registration->id,
                'has_file' => !empty($documentData)
            ]);

            //fire event
            RegistrationCreated::dispatch([
                'id'             => $registration->id,
                'queue_number'   => $registration->queue_number,
                'full_name'      => $registration->full_name,
                'birth_year'     => $registration->birth_year,
                'identity_number' => $registration->identity_number,
                'email'          => $registration->email,
                'phone'          => $registration->phone,
                'document_file'  => $registration->document_file, // lưu ý: tên file
                'department_id'  => $registration->department_id,
                'department'     => $registration->department->name,
                'document_original_name' => $registration->document_original_name,
                'document_mime_type' => $registration->document_mime_type,
                'new_status'     => $registration->status,
                'at'             => now()->toDateTimeString()
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
                ->withErrors(['general' => 'Có lỗi xảy ra khi đăng ký dịch vụ. Vui lòng thử lại.']);
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

    /**
     * Xử lý file upload an toàn
     */
    private function handleFileUpload(Request $request)
    {
        if (!$request->hasFile('document_file') || !$request->file('document_file')->isValid()) {
            return null;
        }

        $file = $request->file('document_file');
        $originalName = $file->getClientOriginalName();
        $mimeType = $file->getMimeType();
        $size = $file->getSize();

        // Kiểm tra kích thước file (128MB = 134,217,728 bytes)
        if ($size > 134217728) {
            throw new \Exception('Kích thước file vượt quá 128MB');
        }

        // Tạo tên file an toàn
        $safeFileName = time() . '_' . Str::random(10) . '_' . Str::slug($originalName);
        // Lưu file vào storage
        $path = $file->storeAs('documents', $safeFileName, 'public');

        return [
            'document_file' => $path,
            'document_original_name' => $originalName,
            'document_mime_type' => $mimeType,
            'document_size' => $size
        ];
    }
}
