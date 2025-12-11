<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - Hệ thống Quản lý Hợp đồng Thỉnh giảng</title>
    
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .login-container {
            max-width: 450px;
            width: 100%;
            padding: 20px;
        }
        
        .login-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }
        
        .login-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .login-header h1 {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .login-header p {
            margin: 0;
            opacity: 0.9;
            font-size: 14px;
        }
        
        .login-body {
            padding: 40px;
        }
        
        .form-floating {
            margin-bottom: 20px;
        }
        
        .form-floating > .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.25);
        }
        
        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: transform 0.2s;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }
        
        .login-footer {
            padding: 20px 40px;
            background: #f8f9fa;
            text-align: center;
            border-top: 1px solid #e9ecef;
        }
        
        .demo-accounts {
            margin-top: 20px;
            padding: 15px;
            background: #e7f3ff;
            border-radius: 10px;
            font-size: 13px;
        }
        
        .demo-accounts .badge {
            margin: 2px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <!-- Header -->
            <div class="login-header">
                <i class="bi bi-file-earmark-text" style="font-size: 48px;"></i>
                <h1>ĐĂNG NHẬP</h1>
                <p>Hệ thống Quản lý Hợp đồng Thỉnh giảng</p>
                <p><small>Cao đẳng Nghề TP. Hồ Chí Minh</small></p>
            </div>
            
            <!-- Body -->
            <div class="login-body">
                <?php 
                // Hiển thị lỗi nếu có
                if (isset($_SESSION['error'])): 
                ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <?php 
                    echo htmlspecialchars($_SESSION['error']); 
                    unset($_SESSION['error']);
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                
                <?php 
                // Hiển thị thông báo thành công (logout, etc)
                if (isset($_SESSION['success'])): 
                ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill"></i>
                    <?php 
                    echo htmlspecialchars($_SESSION['success']); 
                    unset($_SESSION['success']);
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                
                <form method="POST" action="<?php echo BASE_URL; ?>/login.php">
                    <!-- Username -->
                    <div class="form-floating">
                        <input type="text" 
                               class="form-control" 
                               id="username" 
                               name="username" 
                               placeholder="Tên đăng nhập"
                               required 
                               autofocus
                               value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
                        <label for="username">
                            <i class="bi bi-person"></i> Tên đăng nhập
                        </label>
                    </div>
                    
                    <!-- Password -->
                    <div class="form-floating">
                        <input type="password" 
                               class="form-control" 
                               id="password" 
                               name="password" 
                               placeholder="Mật khẩu"
                               required>
                        <label for="password">
                            <i class="bi bi-lock"></i> Mật khẩu
                        </label>
                    </div>
                    
                    <!-- Remember me -->
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="remember" name="remember">
                        <label class="form-check-label" for="remember">
                            Ghi nhớ đăng nhập
                        </label>
                    </div>
                    
                    <!-- Submit button -->
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-login">
                            <i class="bi bi-box-arrow-in-right"></i> ĐĂNG NHẬP
                        </button>
                    </div>
                </form>
                
                <!-- Demo accounts -->
                <div class="demo-accounts">
                    <strong><i class="bi bi-info-circle"></i> Tài khoản demo:</strong><br>
                    <span class="badge bg-danger">admin / admin123</span>
                    <span class="badge bg-info">phongdaotao / 123456</span>
                    <span class="badge bg-success">truongkhoa_cntt / 123456</span>
                    <span class="badge bg-warning text-dark">giaovu_cntt / 123456</span>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="login-footer">
                <small class="text-muted">
                    <i class="bi bi-shield-check"></i> Hệ thống bảo mật - Chỉ dành cho nội bộ
                </small>
            </div>
        </div>
        
        <!-- Copyright -->
        <div class="text-center mt-3">
            <small class="text-white">
                &copy; <?php echo date('Y'); ?> Cao đẳng Nghề TP.HCM
            </small>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Auto dismiss alerts
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                var alerts = document.querySelectorAll('.alert');
                alerts.forEach(function(alert) {
                    var bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 5000);
        });
    </script>
</body>
</html>