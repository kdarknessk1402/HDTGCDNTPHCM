<?php
/**
 * View: Form sửa Trình độ (Admin)
 * File: views/admin/trinh_do/edit.php
 */

if (!isLoggedIn()) {
    redirect('/login');
    exit;
}

$pageTitle = 'Sửa Trình độ';
require_once __DIR__ . '/../../layouts/header.php';
?>

<div class="container-fluid px-4">
    <h1 class="mt-4"><?= $pageTitle ?></h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="/admin/trinh-do">Quản lý Trình độ</a></li>
        <li class="breadcrumb-item active">Sửa: <?= htmlspecialchars($trinh_do['ten_trinh_do']) ?></li>
    </ol>

    <?php displayFlashMessage(); ?>

    <div class="row">
        <div class="col-xl-8 col-lg-10 mx-auto">
            <div class="card mb-4">
                <div class="card-header bg-warning">
                    <i class="fas fa-edit me-1"></i>
                    Cập nhật thông tin trình độ
                </div>
                <div class="card-body">
                    <form method="POST" action="/admin/trinh-do/edit/<?= $trinh_do['trinh_do_id'] ?>" id="editTrinhDoForm">
                        
                        <div class="row">
                            <!-- Mã trình độ -->
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="ma_trinh_do" class="form-label">
                                        Mã trình độ <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="ma_trinh_do" 
                                           name="ma_trinh_do"
                                           value="<?= htmlspecialchars($_POST['ma_trinh_do'] ?? $trinh_do['ma_trinh_do']) ?>"
                                           maxlength="20"
                                           required>
                                    <div class="form-text">Tối đa 20 ký tự</div>
                                </div>
                            </div>

                            <!-- Tên trình độ -->
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="ten_trinh_do" class="form-label">
                                        Tên trình độ <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="ten_trinh_do" 
                                           name="ten_trinh_do"
                                           value="<?= htmlspecialchars($_POST['ten_trinh_do'] ?? $trinh_do['ten_trinh_do']) ?>"
                                           maxlength="50"
                                           required>
                                    <div class="form-text">Tối đa 50 ký tự</div>
                                </div>
                            </div>
                        </div>

                        <!-- Mô tả -->
                        <div class="mb-3">
                            <label for="mo_ta" class="form-label">Mô tả</label>
                            <textarea class="form-control" 
                                      id="mo_ta" 
                                      name="mo_ta"
                                      rows="3"><?= htmlspecialchars($_POST['mo_ta'] ?? $trinh_do['mo_ta']) ?></textarea>
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
                                   value="<?= htmlspecialchars($_POST['thu_tu'] ?? $trinh_do['thu_tu']) ?>"
                                   min="0"
                                   required>
                            <div class="form-text">Thứ tự càng nhỏ hiển thị càng trước</div>
                        </div>

                        <!-- Trạng thái -->
                        <div class="mb-3">
                            <div class="form-check">
                                <?php 
                                $is_active = $_POST['is_active'] ?? $trinh_do['is_active'];
                                ?>
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="is_active" 
                                       name="is_active"
                                       <?= $is_active ? 'checked' : '' ?>>
                                <label class="form-check-label" for="is_active">
                                    <strong>Kích hoạt trình độ</strong>
                                </label>
                            </div>
                        </div>

                        <hr>

                        <!-- Buttons -->
                        <div class="d-flex justify-content-between">
                            <a href="/admin/trinh-do" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Quay lại
                            </a>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save"></i> Cập nhật
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Auto uppercase mã
document.getElementById('ma_trinh_do').addEventListener('input', function() {
    this.value = this.value.toUpperCase();
});
</script>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>