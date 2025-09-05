<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Socket Test - Hệ thống bốc số thứ tự</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .test-result {
            padding: 10px;
            margin: 5px 0;
            border-radius: 5px;
        }
        .test-pass {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
        .test-fail {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
        .debug-info {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 15px;
            border-radius: 5px;
            font-family: monospace;
            white-space: pre-wrap;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-bug me-2"></i>Socket Manager Test</h3>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <h5>Test Controls</h5>
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-primary" onclick="runTests()">
                                    <i class="fas fa-play me-2"></i>Run All Tests
                                </button>
                                <button type="button" class="btn btn-success" onclick="simulateRegistration()">
                                    <i class="fas fa-plus me-2"></i>Simulate Registration
                                </button>
                                <button type="button" class="btn btn-warning" onclick="simulateStatusUpdate()">
                                    <i class="fas fa-edit me-2"></i>Simulate Status Update
                                </button>
                                <button type="button" class="btn btn-info" onclick="showDebugInfo()">
                                    <i class="fas fa-info me-2"></i>Debug Info
                                </button>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h5>Test Results</h5>
                            <div id="test-results">
                                <div class="text-muted">Chưa có kết quả test nào...</div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h5>Debug Information</h5>
                            <div id="debug-info" class="debug-info">
                                Chưa có thông tin debug...
                            </div>
                        </div>

                        <div class="mb-4">
                            <h5>Navigation Test</h5>
                            <p>Test navigation giữa các trang để kiểm tra socket reconnection:</p>
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.service-registrations.index') }}" class="btn btn-outline-primary">
                                    <i class="fas fa-list me-2"></i>Go to Index
                                </a>
                                <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-home me-2"></i>Go to Home
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="http://localhost:6001/socket.io/socket.io.js"></script>
    <script src="https://unpkg.com/laravel-echo@1.15.3/dist/echo.iife.js"></script>
    <script src="{{ asset('js/socket-manager.js') }}"></script>
    <script src="{{ asset('js/socket-test.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function runTests() {
            const resultsDiv = document.getElementById('test-results');
            resultsDiv.innerHTML = '<div class="text-info"><i class="fas fa-spinner fa-spin me-2"></i>Đang chạy tests...</div>';
            
            // Override console.log để capture test results
            const originalLog = console.log;
            const testLogs = [];
            
            console.log = function(...args) {
                testLogs.push(args.join(' '));
                originalLog.apply(console, args);
            };
            
            // Run tests
            window.socketTest.runAllTests();
            
            // Restore console.log
            setTimeout(() => {
                console.log = originalLog;
                
                // Display results
                let html = '';
                testLogs.forEach(log => {
                    if (log.includes('✅')) {
                        html += `<div class="test-result test-pass">${log}</div>`;
                    } else if (log.includes('❌')) {
                        html += `<div class="test-result test-fail">${log}</div>`;
                    } else if (log.includes('📊') || log.includes('📈') || log.includes('🎉') || log.includes('⚠️')) {
                        html += `<div class="test-result" style="background-color: #e2e3e5; border: 1px solid #d6d8db; color: #383d41;">${log}</div>`;
                    }
                });
                
                resultsDiv.innerHTML = html || '<div class="text-muted">Không có kết quả test...</div>';
            }, 2000);
        }

        function simulateRegistration() {
            window.simulateRegistration();
            showDebugInfo();
        }

        function simulateStatusUpdate() {
            window.simulateStatusUpdate();
            showDebugInfo();
        }

        function showDebugInfo() {
            const debugInfo = window.getSocketDebugInfo();
            const debugDiv = document.getElementById('debug-info');
            debugDiv.textContent = JSON.stringify(debugInfo, null, 2);
        }

        // Auto-refresh debug info every 5 seconds
        setInterval(showDebugInfo, 5000);
        
        // Show initial debug info
        setTimeout(showDebugInfo, 1000);
    </script>
</body>
</html>
