<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3>Test Form</h3>
                    </div>
                    <div class="card-body">
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('service.register') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="full_name" class="form-label">Họ và tên</label>
                                <input type="text" class="form-control" id="full_name" name="full_name" value="{{ old('full_name') }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="birth_year" class="form-label">Năm sinh</label>
                                <input type="number" class="form-control" id="birth_year" name="birth_year" value="{{ old('birth_year') }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="identity_number" class="form-label">Số căn cước</label>
                                <input type="text" class="form-control" id="identity_number" name="identity_number" value="{{ old('identity_number') }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="department_id" class="form-label">Phòng ban</label>
                                <select class="form-select" id="department_id" name="department_id" required>
                                    <option value="">Chọn phòng ban</option>
                                    @foreach(\App\Models\Department::where('status', 'active')->get() as $dept)
                                        <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>
                                            {{ $dept->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">Gửi</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

