<?php
/**
 * View: Change Password
 * File: views/auth/change_password.php
 */

if (!isLoggedIn()) { redirect('/login'); exit; }
$pageTitle = 'Đổi mật khẩu';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container-fluid px-4">
    <h1 class="mt-4"><?= $pageTitle ?></h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item active">Đổi mật khẩu</li>
    </ol>

    <?php displayFlashMessage(); ?>

    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <i class="fas fa-key me-1"></i> Đổi mật khẩu
                </div>
                <div class="card-body">
                    <form method="POST" action="/change-password">
                        <div class="mb-3">
                            <label class="form-label">Mật khẩu cũ <span class="text-danger">*</span></label>
                            <input type="password" name="old_password" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Mật khẩu mới <span class="text-danger">*</span></label>
                            <input type="password" name="new_password" id="new_password" class="form-control" required minlength="6">
                            <small class="text-muted">Ít nhất 6 ký tự</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Xác nhận mật khẩu mới <span class="text-danger">*</span></label>
                            <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> <strong>Lưu ý:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Mật khẩu phải ít nhất 6 ký tự</li>
                                <li>Nên sử dụng kết hợp chữ, số và ký tự đặc biệt</li>
                                <li>Không chia sẻ mật khẩu cho người khác</li>
                            </ul>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between">
                            <a href="/dashboard" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Quay lại
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Đổi mật khẩu
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('confirm_password').addEventListener('input', function() {
    const newPass = document.getElementById('new_password').value;
    const confirmPass = this.value;
    
    if (newPass !== confirmPass) {
        this.setCustomValidity('Mật khẩu không khớp!');
    } else {
        this.setCustomValidity('');
    }
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>