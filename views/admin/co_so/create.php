<?php
/**
 * View: Form thêm Cơ sở đào tạo (Admin)
 * File: views/admin/co_so/create.php
 */

if (!isLoggedIn()) {
    redirect('/login');
    exit;
}

$pageTitle = 'Thêm Cơ sở Mới';
require_once __DIR__ . '/../../layouts/header.php';
?>

<div class="container-fluid px-4">
    <h1 class="mt-4"><?= $pageTitle ?></h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="/admin/co-so">Quản lý Cơ sở</a></li>
        <li class="breadcrumb-item active">Thêm mới</li>
    </ol>

    <?php displayFlashMessage(); ?>

    <div class="row">
        <div class="col-xl-10 col-lg-11 mx-auto">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-plus-circle me-1"></i>
                    Thông tin cơ sở mới
                </div>
                <div class="card-body">
                    <form method="POST" action="/admin/co-so/create" id="createCoSoForm">
                        
                        <div class="row">
                            <!-- Mã cơ sở -->
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="ma_co_so" class="form-label">
                                        Mã cơ sở <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="ma_co_so" 
                                           name="ma_co_so"
                                           value="<?= htmlspecialchars($_POST['ma_co_so'] ?? '') ?>"
                                           maxlength="20"
                                           required
                                           placeholder="VD: CS_Q1">
                                    <div class="form-text">Tối đa 20 ký tự, tự động viết hoa</div>
                                </div>
                            </div>

                            <!-- Tên cơ sở -->
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="ten_co_so" class="form-label">
                                        Tên cơ sở <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="ten_co_so" 
                                           name="ten_co_so"
                                           value="<?= htmlspecialchars($_POST['ten_co_so'] ?? '') ?>"
                                           maxlength="100"
                                           required
                                           placeholder="VD: Trường Cao đẳng Nghề TP.HCM - Cơ sở Quận 1">
                                    <div class="form-text">Tối đa 100 ký tự</div>
                                </div>
                            </div>
                        </div>

                        <!-- Địa chỉ -->
                        <div class="mb-3">
                            <label for="dia_chi" class="form-label">Địa chỉ</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="dia_chi" 
                                   name="dia_chi"
                                   value="<?= htmlspecialchars($_POST['dia_chi'] ?? '') ?>"
                                   maxlength="200"
                                   placeholder="VD: 123 Nguyễn Văn Linh, Phường Tân Phú, Quận 7, TP.HCM">
                        </div>

                        <div class="row">
                            <!-- Số điện thoại -->
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="so_dien_thoai" class="form-label">Số điện thoại</label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="so_dien_thoai" 
                                           name="so_dien_thoai"
                                           value="<?= htmlspecialchars($_POST['so_dien_thoai'] ?? '') ?>"
                                           maxlength="15"
                                           placeholder="0283xxxxxx">
                                    <div class="form-text">10-11 chữ số</div>
                                </div>
                            </div>

                            <!-- Email -->
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" 
                                           class="form-control" 
                                           id="email" 
                                           name="email"
                                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                                           maxlength="100"
                                           placeholder="coso1@truong.edu.vn">
                                </div>
                            </div>

                            <!-- Người phụ trách -->
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="nguoi_phu_trach" class="form-label">Người phụ trách</label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="nguoi_phu_trach" 
                                           name="nguoi_phu_trach"
                                           value="<?= htmlspecialchars($_POST['nguoi_phu_trach'] ?? '') ?>"
                                           maxlength="100"
                                           placeholder="VD: Nguyễn Văn A">
                                </div>
                            </div>
                        </div>

                        <!-- Thứ tự -->
                        <div class="mb-3">
                            <label for="thu_tu" class="form-label">
                                Thứ tự hiển thị <span class="text-danger">*</span>
                            </label>
                            <input type="number" 
                                   class="form-control" 
                                   id="thu_tu" 
                                   name="thu_tu"
                                   value="<?= htmlspecialchars($_POST['thu_tu'] ?? '0') ?>"
                                   min="0"
                                   required
                                   style="max-width: 200px;">
                            <div class="form-text">Thứ tự càng nhỏ hiển thị càng trước</div>
                        </div>

                        <!-- Trạng thái -->
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="is_active" 
                                       name="is_active"
                                       <?= (!isset($_POST['is_active']) || $_POST['is_active']) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="is_active">
                                    <strong>Kích hoạt cơ sở</strong>
                                    <small class="text-muted d-block">
                                        Cơ sở đang hoạt động sẽ hiển thị khi tạo Đơn giá, Hợp đồng
                                    </small>
                                </label>
                            </div>
                        </div>

                        <hr>

                        <!-- Buttons -->
                        <div class="d-flex justify-content-between">
                            <a href="/admin/co-so" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Quay lại
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Lưu cơ sở
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Hướng dẫn -->
            <div class="card border-info mb-4">
                <div class="card-header bg-info text-white">
                    <i class="fas fa-info-circle"></i> Hướng dẫn
                </div>
                <div class="card-body">
                    <ul class="mb-0">
                        <li><strong>Cơ sở</strong> là các địa điểm đào tạo của trường</li>
                        <li>Ví dụ: Cơ sở Quận 1, Cơ sở Quận 7, Cơ sở Thủ Đức...</li>
                        <li>Mỗi cơ sở có thể có <strong>đơn giá giờ dạy khác nhau</strong></li>
                        <li>Khi tạo hợp đồng, cần chọn cơ sở để tính đơn giá chính xác</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Auto uppercase mã
document.getElementById('ma_co_so').addEventListener('input', function() {
    this.value = this.value.toUpperCase();
});

// Validate số điện thoại
document.getElementById('so_dien_thoai').addEventListener('input', function() {
    this.value = this.value.replace(/[^0-9]/g, '');
});
</script>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>