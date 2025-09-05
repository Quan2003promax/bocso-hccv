@extends('layouts.app')

@section('content')
<div class="tw-container">
    <div class="tw-card">
        {{-- Header --}}
        <div class="tw-header">
            <div class="tw-flex-between">
                <div>
                    <h2 class="tw-title">Sửa phòng ban</h2>
                    <p class="tw-sub">Cập nhật thông tin phòng ban: {{ $department->name }}</p>
                </div>
                <div>
                    <a class="tw-btn-secondary" href="{{ route('admin.departments.index') }}">
                        <i class="fas fa-arrow-left me-2"></i> Quay lại danh sách
                    </a>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.departments.update', $department) }}">
            @csrf
            @method('PUT')

            <div class="tw-body">
                @if ($errors->any())
                    <div class="tw-alert-danger">
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-triangle tw-alert-icon"></i>
                            </div>
                            <div class="ms-3">
                                <h3 class="tw-alert-title">Lỗi!</h3>
                                <div class="tw-alert-text">
                                    <ul class="tw-list">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Grid 2 cột giống y Tailwind --}}
                <div class="tw-grid">
                    {{-- Tên phòng ban --}}
                    <div>
                        <label for="name" class="tw-label">
                            Tên phòng ban <span class="tw-required">*</span>
                        </label>
                        <input type="text"
                               id="name"
                               name="name"
                               class="tw-input @error('name') tw-input-invalid @enderror"
                               value="{{ old('name', $department->name) }}"
                               placeholder="Ví dụ: Phòng Tổ chức - Hành chính"
                               required>
                        @error('name')
                            <p class="tw-error">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Trạng thái --}}
                    <div>
                        <label for="status" class="tw-label">
                            Trạng thái <span class="tw-required">*</span>
                        </label>
                        <select id="status"
                                name="status"
                                class="tw-input @error('status') tw-input-invalid @enderror"
                                required>
                            <option value="">Chọn trạng thái</option>
                            <option value="active"   {{ old('status', $department->status) == 'active' ? 'selected' : '' }}>Hoạt động</option>
                            <option value="inactive" {{ old('status', $department->status) == 'inactive' ? 'selected' : '' }}>Không hoạt động</option>
                        </select>
                        @error('status')
                            <p class="tw-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <hr class="tw-sep">

                {{-- Mô tả --}}
                <div class="mb-3">
                    <label for="description" class="tw-label">Mô tả</label>
                    <textarea id="description"
                              name="description"
                              rows="4"
                              class="tw-input @error('description') tw-input-invalid @enderror"
                              placeholder="Mô tả ngắn gọn về chức năng, nhiệm vụ của phòng ban...">{{ old('description', $department->description) }}</textarea>
                    @error('description')
                        <p class="tw-error">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Footer --}}
            <div class="tw-footer">
                <div class="d-flex align-items-center gap-2">
                    <button type="submit" class="tw-btn-primary">
                        <i class="fas fa-save me-2"></i> Cập nhật phòng ban
                    </button>
                    <a href="{{ route('admin.departments.index') }}" class="tw-btn-gray">
                        <i class="fas fa-times me-2"></i> Hủy
                    </a>
                </div>

                <div class="tw-footnote">
                    <i class="fas fa-info-circle me-1"></i> Thay đổi sẽ áp dụng ngay
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<style>
/* ================================
   “Tailwind look” bằng lớp tw-*
   Không cần cài Tailwind
   ================================ */

/* Khung ngoài giống max-w-7xl mx-auto sm:px-6 lg:px-8 py-6 */
.tw-container{
  max-width: 80rem; /* ~1280px */
  margin-inline: auto;
  padding-inline: 1.5rem; /* sm:px-6 */
  padding-block: 1.5rem;  /* py-6 */
}
@media (min-width: 1024px){ .tw-container{ padding-inline: 2rem; } } /* lg:px-8 */

/* Card: bg-white overflow-hidden shadow-sm sm:rounded-lg */
.tw-card{
  background: #fff;
  border-radius: .75rem; /* ~rounded-lg */
  overflow: hidden;
  box-shadow: 0 1px 2px rgba(16, 24, 40, .04), 0 1px 1px rgba(16, 24, 40, .02);
  border: 1px solid #e5e7eb;
}

/* Header: p-6 border-b border-gray-200 + flex between */
.tw-header{
  padding: 1.5rem;
  border-bottom: 1px solid #e5e7eb;
  background: linear-gradient(180deg,#fcfcfd,#f9fafb);
}
.tw-flex-between{
  display:flex; align-items:center; justify-content:space-between; gap:.75rem;
}

/* Title & sub */
.tw-title{
  font-size: 1.125rem; /* text-lg */
  font-weight: 600;
  color:#111827;
  margin:0;
}
.tw-sub{
  font-size:.875rem; /* text-sm */
  color:#6b7280;
  margin-top:.25rem;
}

/* Body: p-6 */
.tw-body{ padding:1.5rem; }

/* Grid: grid grid-cols-1 md:grid-cols-2 gap-6 */
.tw-grid{
  display:grid; grid-template-columns:1fr; gap:1.5rem;
}
@media (min-width: 768px){
  .tw-grid{ grid-template-columns:repeat(2, minmax(0,1fr)); }
}

/* Label */
.tw-label{
  display:block; margin-bottom:.5rem;
  font-size:.95rem; font-weight:600; color:#374151;
}
.tw-required{ color:#ef4444; }

/* Input “y hệt” focus ring */
.tw-input{
  width:100%;
  border:1px solid #d1d5db;
  border-radius:.5rem;
  padding:.6rem .75rem;
  background:#fff;
  color:#111827;
  transition: box-shadow .15s, border-color .15s;
}
.tw-input:focus{
  outline:0;
  border-color:#3b82f6;
  box-shadow: 0 0 0 .2rem rgba(59,130,246,.25); /* focus:ring-2 */
}
.tw-input-invalid{ border-color:#fca5a5; }

/* Error text: mt-1 text-sm text-red-600 */
.tw-error{
  margin-top:.25rem;
  font-size:.875rem;
  color:#dc2626;
}

/* Alert lỗi: bg-red-50 border-red-200 text-red-700 rounded-md px-4 py-3 */
.tw-alert-danger{
  background:#fef2f2;
  border:1px solid #fecaca;
  color:#b91c1c;
  border-radius:.5rem;
  padding:.75rem 1rem;
  margin-bottom:1.25rem;
}
.tw-alert-icon{ color:#f87171; }
.tw-alert-title{ font-size:.9rem; font-weight:600; margin:0 0 .25rem 0; }
.tw-alert-text{ font-size:.9rem; }
.tw-list{ margin:0; padding-left:1.25rem; }

/* Divider: my-8 border-gray-200 */
.tw-sep{
  margin:2rem 0; border:0; border-top:1px solid #e5e7eb;
}

/* Footer: px-6 py-4 bg-gray-50 border-t border-gray-200 flex between */
.tw-footer{
  display:flex; align-items:center; justify-content:space-between; gap:.75rem;
  padding:1rem 1.5rem;
  background:#f9fafb;
  border-top:1px solid #e5e7eb;
}
.tw-footnote{ font-size:.875rem; color:#6b7280; }

/* Buttons */
.tw-btn-primary {
  display: inline-flex;
  align-items: center;
  padding: 0.5rem 1rem;          /* py-2 px-4 */
  
  color: #000;                /* chữ trắng */
  border-radius: 0.375rem;       /* rounded-md */
  border: none;
  font-size: 0.875rem;
  font-weight: 500;
  transition: background-color .15s ease-in-out, color .15s ease-in-out;
}

.tw-btn-primary:hover {

  color: #000
}
.tw-btn-secondary {
  display: inline-flex;
  align-items: center;
  padding: 0.5rem 1rem;      
  background-color: #4b5563;  
  color: #000000;            
  border-radius: 0.375rem;   
  border: none;
  font-size: 0.875rem;
  font-weight: 500;
  transition: background-color .15s ease-in-out, color .15s ease-in-out;
}

.tw-btn-secondary:hover {
  background-color: #374151;  
  color: #000000;            
}

.tw-btn-gray{
  display:inline-flex; align-items:center; justify-content:center; gap:.5rem;
  color:#111827; 
  padding:.55rem 1rem; border-radius:.5rem; text-decoration:none;
  transition:.15s;
}

</style>
@endpush
