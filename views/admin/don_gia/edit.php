<?php
/**
 * View: Form sửa Đơn giá (Admin)
 * File: views/admin/don_gia/edit.php
 */

if (!isLoggedIn()) {
    redirect('/login');
    exit;
}

$pageTitle = 'Sửa Đơn giá';
require_once __DIR__ . '/../../layouts/header.php';
?>

<div class="container-fluid px-4">
    <h1 class="mt-4"><?= $pageTitle ?></h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="/admin/don-gia">Quản lý Đơn giá</a></li>
        <li class="breadcrumb-item active">Sửa</li>
    </ol>

    <?php displayFlashMessage(); ?>

    <div class="row">
        <div class="col-xl-8 col-lg-10 mx-auto">
            <div class="card mb-4">
                <div class="card-header bg-warning">
                    <i class="fas fa-edit me-1"></i>
                    Cập nhật đơn giá
                </div>
                <div class="card-body">
                    <form method="POST" action="/admin/don-gia/edit/<?= $don_gia['don_gia_id'] ?>" id="editDonGiaForm">
                        
                        <!-- Cơ sở và Trình độ -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="co_so_id" class="form-label">
                                        Cơ sở <span class="text-danger">*</span>
                                    </label>
                                    <select name="co_so_id" id="co_so_id" class="form-select" required>
                                        <option value="">-- Chọn cơ sở --</option>
                                        <?php foreach ($co_so_list as $cs): ?>
                                            <option value="<?= $cs['co_so_id'] ?>" 
                                                <?php 
                                                $selected = $_POST['co_so_id'] ?? $don_gia['co_so_id'];
                                                echo ($selected == $cs['co_so_id']) ? 'selected' : '';
                                                ?>>
                                                [<?= htmlspecialchars($cs['ma_co_so']) ?>] 
                                                <?= htmlspecialchars($cs['ten_co_so']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="trinh_do_id" class="form-label">
                                        Trình độ <span class="text-danger">*</span>
                                    </label>
                                    <select name="trinh_do_id" id="trinh_do_id" class="form-select" required>
                                        <option value="">-- Chọn trình độ --</option>
                                        <?php foreach ($trinh_do_list as $td): ?>
                                            <option value="<?= $td['trinh_do_id'] ?>" 
                                                <?php 
                                                $selected = $_POST['trinh_do_id'] ?? $don_gia['trinh_do_id'];
                                                echo ($selected == $td['trinh_do_id']) ? 'selected' : '';
                                                ?>>
                                                <?= htmlspecialchars($td['ten_trinh_do']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Đơn giá -->
                        <div class="mb-3">
                            <label for="don_gia" class="form-label">
                                Đơn giá 1 giờ (VNĐ) <span class="text-danger">*</span>
                            </label>
                            <input type="number" 
                                   class="form-control" 
                                   id="don_gia" 
                                   name="don_gia"
                                   value="<?= htmlspecialchars($_POST['don_gia'] ?? $don_gia['don_gia']) ?>"
                                   min="0"
                                   step="1000"
                                   required>
                            <div class="form-text">Đơn giá tính theo VNĐ/giờ</div>
                        </div>

                        <div class="row">
                            <!-- Ngày áp dụng -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="ngay_ap_dung" class="form-label">
                                        Ngày áp dụng <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" 
                                           class="form-control" 
                                           id="ngay_ap_dung" 
                                           name="ngay_ap_dung"
                                           value="<?= htmlspecialchars($_POST['ngay_ap_dung'] ?? $don_gia['ngay_ap_dung']) ?>"
                                           required>
                                </div>
                            </div>

                            <!-- Ngày kết thúc -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="ngay_ket_thuc" class="form-label">
                                        Ngày kết thúc
                                    </label>
                                    <input type="date" 
                                           class="form-control" 
                                           id="ngay_ket_thuc" 
                                           name="ngay_ket_thuc"
                                           value="<?= htmlspecialchars($_POST['ngay_ket_thuc'] ?? $don_gia['ngay_ket_thuc']) ?>">
                                    <div class="form-text">Để trống nếu không giới hạn</div>
                                </div>
                            </div>
                        </div>

                        <!-- Ghi chú -->
                        <div class="mb-3">
                            <label for="ghi_chu" class="form-label">Ghi chú</label>
                            <textarea class="form-control" 
                                      id="ghi_chu" 
                                      name="ghi_chu"
                                      rows="3"><?= htmlspecialchars($_POST['ghi_chu'] ?? $don_gia['ghi_chu']) ?></textarea>
                        </div>

                        <!-- Trạng thái -->
                        <div class="mb-3">
                            <div class="form-check">
                                <?php 
                                $is_active = $_POST['is_active'] ?? $don_gia['is_active'];
                                ?>
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="is_active" 
                                       name="is_active"
                                       <?= $is_active ? 'checked' : '' ?>>
                                <label class="form-check-label" for="is_active">
                                    <strong>Kích hoạt đơn giá</strong>
                                </label>
                            </div>
                        </div>

                        <hr>

                        <!-- Buttons -->
                        <div class="d-flex justify-content-between">
                            <a href="/admin/don-gia" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Quay lại
                            </a>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save"></i> Cập nhật
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Cảnh báo nếu có hợp đồng -->
            <?php
            $has_related = $don_gia['has_contracts'] ?? false;
            if ($has_related):
            ?>
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i>
                <strong>Lưu ý:</strong> Đơn giá này đã được sử dụng trong hợp đồng. 
                Thay đổi có thể ảnh hưởng đến các hợp đồng hiện tại.
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Validation ngày
document.getElementById('editDonGiaForm').addEventListener('submit', function(e) {
    const ngayApDung = document.getElementById('ngay_ap_dung').value;
    const ngayKetThuc = document.getElementById('ngay_ket_thuc').value;
    
    if (ngayKetThuc && ngayKetThuc <= ngayApDung) {
        e.preventDefault();
        alert('Ngày kết thúc phải sau ngày áp dụng!');
        return;
    }
    
    const donGia = parseFloat(document.getElementById('don_gia').value);
    if (!donGia || donGia <= 0) {
        e.preventDefault();
        alert('Vui lòng nhập đơn giá hợp lệ!');
        return;
    }
});
</script>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>