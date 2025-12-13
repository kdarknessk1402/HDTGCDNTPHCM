<?php
/**
 * View: Form thêm Trình độ (Admin)
 * File: views/admin/trinh_do/create.php
 */

if (!isLoggedIn()) {
    redirect('/login');
    exit;
}

$pageTitle = 'Thêm Trình độ Mới';
require_once __DIR__ . '/../../layouts/header.php';
?>

<div class="container-fluid px-4">
    <h1 class="mt-4"><?= $pageTitle ?></h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="/admin/trinh-do">Quản lý Trình độ</a></li>
        <li class="breadcrumb-item active">Thêm mới</li>
    </ol>

    <?php displayFlashMessage(); ?>

    <div class="row">
        <div class="col-xl-8 col-lg-10 mx-auto">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-plus-circle me-1"></i>
                    Thông tin trình độ mới
                </div>
                <div class="card-body">
                    <form method="POST" action="/admin/trinh-do/create" id="createTrinhDoForm">
                        
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
                                           value="<?= htmlspecialchars($_POST['ma_trinh_do'] ?? '') ?>"
                                           maxlength="20"
                                           required
                                           placeholder="VD: TS">
                                    <div class="form-text">Tối đa 20 ký tự, tự động viết hoa</div>
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
                                           value="<?= htmlspecialchars($_POST['ten_trinh_do'] ?? '') ?>"
                                           maxlength="50"
                                           required
                                           placeholder="VD: Tiến sĩ">
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
                                      rows="3"
                                      placeholder="Nhập mô tả về trình độ chuyên môn..."><?= htmlspecialchars($_POST['mo_ta'] ?? '') ?></textarea>
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
                                   required>
                            <div class="form-text">
                                Thứ tự càng nhỏ hiển thị càng trước. VD: Tiến sĩ (1), Thạc sĩ (2), Đại học (3)
                            </div>
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
                                    <strong>Kích hoạt trình độ</strong>
                                    <small class="text-muted d-block">
                                        Trình độ đang hoạt động sẽ hiển thị khi tạo Giảng viên, Đơn giá
                                    </small>
                                </label>
                            </div>
                        </div>

                        <hr>

                        <!-- Buttons -->
                        <div class="d-flex justify-content-between">
                            <a href="/admin/trinh-do" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Quay lại
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Lưu trình độ
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
                        <li><strong>Trình độ chuyên môn</strong> là bằng cấp cao nhất của giảng viên</li>
                        <li>Ví dụ: Tiến sĩ, Thạc sĩ, Đại học, Cao đẳng, Trung cấp...</li>
                        <li>Trình độ này dùng để tính <strong>đơn giá giờ dạy</strong></li>
                        <li>Mỗi trình độ có thể có đơn giá khác nhau tùy cơ sở</li>
                    </ul>
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