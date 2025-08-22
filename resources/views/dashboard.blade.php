<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Thống kê tổng quan -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-building text-3xl text-blue-600"></i>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-500">Tổng phòng ban</div>
                                <div class="text-2xl font-semibold text-gray-900">{{ \App\Models\Department::count() }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-clipboard-list text-3xl text-green-600"></i>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-500">Tổng đăng ký</div>
                                <div class="text-2xl font-semibold text-gray-900">{{ \App\Models\ServiceRegistration::count() }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-clock text-3xl text-yellow-600"></i>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-500">Đang chờ</div>
                                <div class="text-2xl font-semibold text-gray-900">{{ \App\Models\ServiceRegistration::where('status', 'pending')->count() }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-check-circle text-3xl text-green-600"></i>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-500">Đã xử lý</div>
                                <div class="text-2xl font-semibold text-gray-900">{{ \App\Models\ServiceRegistration::where('status', 'completed')->count() }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Đăng ký gần đây -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-history me-2"></i>
                        Đăng ký gần đây
                    </h3>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Số thứ tự</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Họ tên</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phòng ban</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thời gian</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse(\App\Models\ServiceRegistration::with('department')->latest()->limit(5)->get() as $registration)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex px-2 py-1 text-sm font-semibold rounded-full bg-blue-100 text-blue-800">
                                                {{ $registration->queue_number }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $registration->full_name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $registration->department->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @switch($registration->status)
                                                @case('pending')
                                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Chờ xử lý</span>
                                                    @break
                                                @case('processing')
                                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Đang xử lý</span>
                                                    @break
                                                @case('completed')
                                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Đã xử lý</span>
                                                    @break
                                                @case('cancelled')
                                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Đã hủy</span>
                                                    @break
                                            @endswitch
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $registration->created_at->format('H:i d/m/Y') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                            Chưa có đăng ký nào
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    @if(\App\Models\ServiceRegistration::count() > 5)
                        <div class="mt-4 text-center">
                            <a href="{{ route('admin.service-registrations.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                Xem tất cả đăng ký →
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
