<?php
/**
 * View: Form sửa Nghề (Admin)
 * File: views/admin/nghe/edit.php
 */

if (!isLoggedIn()) {
    redirect('/login');
    exit;
}

$pageTitle = 'Sửa Nghề';
require_once __DIR__ . '/../../layouts/header.php';
?>

<div class="container-fluid px-4">
    <h1 class="mt-4"><?= $pageTitle ?></h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="/admin/nghe">Quản lý Nghề</a></li>
        <li class="breadcrumb-item active">Sửa: <?= htmlspecialchars($nghe['ten_nghe']) ?></li>
    </ol>

    <?php displayFlashMessage(); ?>

    <div class="row">
        <div class="col-xl-8 col-lg-10 mx-auto">
            <div class="card mb-4">
                <div class="card-header bg-warning">
                    <i class="fas fa-edit me-1"></i>
                    Cập nhật thông tin nghề
                </div>
                <div class="card-body">
                    <form method="POST" action="/admin/nghe/edit/<?= $nghe['nghe_id'] ?>" id="editNgheForm">
                        
                        <!-- Khoa -->
                        <div class="mb-3">
                            <label for="khoa_id" class="form-label">
                                Khoa <span class="text-danger">*</span>
                            </label>
                            <select name="khoa_id" id="khoa_id" class="form-select" required>
                                <option value="">-- Chọn khoa --</option>
                                <?php foreach ($khoa_list as $khoa): ?>
                                    <option value="<?= $khoa['khoa_id'] ?>" 
                                        <?php 
                                        $selected_khoa = $_POST['khoa_id'] ?? $nghe['khoa_id'];
                                        echo ($selected_khoa == $khoa['khoa_id']) ? 'selected' : '';
                                        ?>>
                                        [<?= htmlspecialchars($khoa['ma_khoa']) ?>] 
                                        <?= htmlspecialchars($khoa['ten_khoa']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text">Chọn khoa quản lý nghề này</div>
                        </div>

                        <div class="row">
                            <!-- Mã nghề -->
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="ma_nghe" class="form-label">
                                        Mã nghề <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="ma_nghe" 
                                           name="ma_nghe"
                                           value="<?= htmlspecialchars($_POST['ma_nghe'] ?? $nghe['ma_nghe']) ?>"
                                           maxlength="20"
                                           required
                                           placeholder="VD: CNTT01">
                                    <div class="form-text">Tối đa 20 ký tự, không trùng trong khoa</div>
                                </div>
                            </div>

                            <!-- Số năm đào tạo -->
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="so_nam_dao_tao" class="form-label">
                                        Số năm đào tạo <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" 
                                           class="form-control" 
                                           id="so_nam_dao_tao" 
                                           name="so_nam_dao_tao"
                                           value="<?= htmlspecialchars($_POST['so_nam_dao_tao'] ?? $nghe['so_nam_dao_tao']) ?>"
                                           min="1"
                                           max="10"
                                           required>
                                    <div class="form-text">Từ 1 đến 10 năm</div>
                                </div>
                            </div>

                            <!-- Thứ tự -->
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="thu_tu" class="form-label">
                                        Thứ tự hiển thị
                                    </label>
                                    <input type="number" 
                                           class="form-control" 
                                           id="thu_tu" 
                                           name="thu_tu"
                                           value="<?= htmlspecialchars($_POST['thu_tu'] ?? $nghe['thu_tu']) ?>"
                                           min="0">
                                    <div class="form-text">Số càng nhỏ hiển thị càng trước</div>
                                </div>
                            </div>
                        </div>

                        <!-- Tên nghề -->
                        <div class="mb-3">
                            <label for="ten_nghe" class="form-label">
                                Tên nghề <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control" 
                                   id="ten_nghe" 
                                   name="ten_nghe"
                                   value="<?= htmlspecialchars($_POST['ten_nghe'] ?? $nghe['ten_nghe']) ?>"
                                   maxlength="100"
                                   required
                                   placeholder="VD: Lập trình máy tính">
                            <div class="form-text">Tối đa 100 ký tự</div>
                        </div>

                        <!-- Mô tả -->
                        <div class="mb-3">
                            <label for="mo_ta" class="form-label">Mô tả</label>
                            <textarea class="form-control" 
                                      id="mo_ta" 
                                      name="mo_ta"
                                      rows="4"
                                      placeholder="Nhập mô tả về nghề đào tạo..."><?= htmlspecialchars($_POST['mo_ta'] ?? $nghe['mo_ta']) ?></textarea>
                        </div>

                        <!-- Trạng thái -->
                        <div class="mb-3">
                            <div class="form-check">
                                <?php 
                                $is_active = $_POST['is_active'] ?? $nghe['is_active'];
                                ?>
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="is_active" 
                                       name="is_active"
                                       <?= $is_active ? 'checked' : '' ?>>
                                <label class="form-check-label" for="is_active">
                                    <strong>Kích hoạt nghề</strong>
                                    <small class="text-muted d-block">
                                        Nghề đang hoạt động sẽ hiển thị trong danh sách chọn khi tạo Niên khóa, Lớp, Môn học
                                    </small>
                                </label>
                            </div>
                        </div>

                        <hr>

                        <!-- Thông tin audit -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <small class="text-muted">
                                    <i class="fas fa-user"></i> Tạo bởi: 
                                    <strong><?= htmlspecialchars($nghe['created_by_name'] ?? 'N/A') ?></strong>
                                    <br>
                                    <i class="fas fa-clock"></i> Ngày tạo: 
                                    <?= formatDateTime($nghe['created_at']) ?>
                                </small>
                            </div>
                            <?php if ($nghe['updated_by_name']): ?>
                            <div class="col-md-6">
                                <small class="text-muted">
                                    <i class="fas fa-user-edit"></i> Cập nhật bởi: 
                                    <strong><?= htmlspecialchars($nghe['updated_by_name']) ?></strong>
                                    <br>
                                    <i class="fas fa-clock"></i> Ngày cập nhật: 
                                    <?= formatDateTime($nghe['updated_at']) ?>
                                </small>
                            </div>
                            <?php endif; ?>
                        </div>

                        <hr>

                        <!-- Buttons -->
                        <div class="d-flex justify-content-between">
                            <a href="/admin/nghe" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Quay lại
                            </a>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save"></i> Cập nhật
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Cảnh báo nếu có dữ liệu liên quan -->
            <?php
            // Giả sử controller đã check và truyền vào
            $has_related = false; // Trong controller sẽ check thực tế
            if ($has_related):
            ?>
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i>
                <strong>Lưu ý:</strong> Nghề này đã có dữ liệu liên quan (Niên khóa, Lớp, Môn học). 
                Hãy cẩn thận khi thay đổi thông tin.
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Auto uppercase mã nghề
document.getElementById('ma_nghe').addEventListener('input', function() {
    this.value = this.value.toUpperCase();
});

// Form validation
document.getElementById('editNgheForm').addEventListener('submit', function(e) {
    const khoa = document.getElementById('khoa_id').value;
    const maNghe = document.getElementById('ma_nghe').value.trim();
    const tenNghe = document.getElementById('ten_nghe').value.trim();
    const soNam = document.getElementById('so_nam_dao_tao').value;
    
    if (!khoa) {
        e.preventDefault();
        alert('Vui lòng chọn khoa!');
        document.getElementById('khoa_id').focus();
        return;
    }
    
    if (!maNghe) {
        e.preventDefault();
        alert('Vui lòng nhập mã nghề!');
        document.getElementById('ma_nghe').focus();
        return;
    }
    
    if (!tenNghe) {
        e.preventDefault();
        alert('Vui lòng nhập tên nghề!');
        document.getElementById('ten_nghe').focus();
        return;
    }
    
    if (soNam < 1 || soNam > 10) {
        e.preventDefault();
        alert('Số năm đào tạo phải từ 1 đến 10!');
        document.getElementById('so_nam_dao_tao').focus();
        return;
    }
});
</script>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>