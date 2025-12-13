<?php
/**
 * View: Login
 * File: views/auth/login.php
 */
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Đăng nhập - Hệ thống quản lý giảng viên</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; }
        .login-container { min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .login-card { max-width: 450px; width: 100%; }
        .card { border-radius: 15px; box-shadow: 0 10px 40px rgba(0,0,0,0.3); }
        .card-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 15px 15px 0 0 !important; padding: 30px; }
        .card-body { padding: 40px; }
        .form-control:focus { border-color: #667eea; box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25); }
        .btn-login { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; padding: 12px; font-size: 16px; }
        .btn-login:hover { opacity: 0.9; }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="card">
                <div class="card-header text-center">
                    <i class="fas fa-graduation-cap fa-3x mb-3"></i>
                    <h3 class="mb-0">Hệ thống Quản lý</h3>
                    <p class="mb-0">Giảng viên Thỉnh giảng</p>
                </div>
                <div class="card-body">
                    <?php if (isset($_SESSION['flash_message'])): ?>
                        <div class="alert alert-<?= $_SESSION['flash_type'] ?? 'info' ?> alert-dismissible fade show">
                            <?= $_SESSION['flash_message'] ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php unset($_SESSION['flash_message'], $_SESSION['flash_type']); ?>
                    <?php endif; ?>

                    <form method="POST" action="/login">
                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-user me-2"></i>Tên đăng nhập</label>
                            <input type="text" name="username" class="form-control form-control-lg" required autofocus>
                        </div>

                        <div class="mb-4">
                            <label class="form-label"><i class="fas fa-lock me-2"></i>Mật khẩu</label>
                            <input type="password" name="password" class="form-control form-control-lg" required>
                        </div>

                        <button type="submit" class="btn btn-primary btn-login w-100 btn-lg">
                            <i class="fas fa-sign-in-alt me-2"></i>Đăng nhập
                        </button>
                    </form>

                    <hr class="my-4">

                    <div class="text-center text-muted">
                        <small>
                            <i class="fas fa-info-circle me-1"></i>
                            Trường Cao đẳng Nghề TP.HCM<br>
                            © <?= date('Y') ?> - Phiên bản 1.0
                        </small>
                    </div>
                </div>
            </div>

            <!-- Demo accounts -->
            <div class="card mt-3">
                <div class="card-body">
                    <h6 class="text-center mb-3"><i class="fas fa-key me-2"></i>Tài khoản demo</h6>
                    <div class="row g-2">
                        <div class="col-6">
                            <div class="border p-2 rounded text-center">
                                <small class="d-block fw-bold">Admin</small>
                                <small class="text-muted">admin / admin123</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border p-2 rounded text-center">
                                <small class="d-block fw-bold">Phòng ĐT</small>
                                <small class="text-muted">phongdt / phongdt123</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border p-2 rounded text-center">
                                <small class="d-block fw-bold">Trưởng Khoa</small>
                                <small class="text-muted">truongkhoa / tk123</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border p-2 rounded text-center">
                                <small class="d-block fw-bold">Giáo vụ</small>
                                <small class="text-muted">giaovu / gv123</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>