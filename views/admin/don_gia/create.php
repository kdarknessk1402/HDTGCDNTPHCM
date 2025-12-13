<?php
/**
 * View: Form thêm Đơn giá mới (Admin)
 * File: views/admin/don_gia/create.php
 */

if (!isLoggedIn()) {
    redirect('/login');
    exit;
}

$pageTitle = 'Thêm Đơn giá Mới';
require_once __DIR__ . '/../../layouts/header.php';
?>

<div class="container-fluid px-4">
    <h1 class="mt-4"><?= $pageTitle ?></h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="/admin/don-gia">Quản lý Đơn giá</a></li>
        <li class="breadcrumb-item active">Thêm mới</li>
    </ol>

    <?php displayFlashMessage(); ?>

    <div class="row">
        <div class="col-xl-8 col-lg-10 mx-auto">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-plus-circle me-1"></i>
                    Thông tin đơn giá mới
                </div>
                <div class="card-body">
                    <form method="POST" action="/admin/don-gia/create" id="createDonGiaForm">
                        
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
                                                <?= (isset($_POST['co_so_id']) && $_POST['co_so_id'] == $cs['co_so_id']) ? 'selected' : '' ?>>
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
                                                <?= (isset($_POST['trinh_do_id']) && $_POST['trinh_do_id'] == $td['trinh_do_id']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($td['ten_trinh_do']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="form-text">Trình độ của giảng viên</div>
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
                                   value="<?= htmlspecialchars($_POST['don_gia'] ?? '') ?>"
                                   min="0"
                                   step="1000"
                                   required
                                   placeholder="VD: 150000">
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
                                           value="<?= htmlspecialchars($_POST['ngay_ap_dung'] ?? date('Y-m-d')) ?>"
                                           required>
                                    <div class="form-text">Ngày bắt đầu áp dụng đơn giá</div>
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
                                           value="<?= htmlspecialchars($_POST['ngay_ket_thuc'] ?? '') ?>">
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
                                      rows="3"
                                      placeholder="VD: Đơn giá áp dụng từ năm học 2024-2025"><?= htmlspecialchars($_POST['ghi_chu'] ?? '') ?></textarea>
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
                                    <strong>Kích hoạt đơn giá</strong>
                                    <small class="text-muted d-block">
                                        Đơn giá đang hoạt động sẽ được sử dụng khi tạo hợp đồng
                                    </small>
                                </label>
                            </div>
                        </div>

                        <hr>

                        <!-- Buttons -->
                        <div class="d-flex justify-content-between">
                            <a href="/admin/don-gia" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Quay lại
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Lưu đơn giá
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
                        <li>Đơn giá được tính theo <strong>Cơ sở × Trình độ</strong></li>
                        <li>Ví dụ: Cơ sở Quận 1 + Thạc sĩ = 200,000 VNĐ/giờ</li>
                        <li>Nếu không nhập ngày kết thúc, đơn giá <strong>không giới hạn thời gian</strong></li>
                        <li>Khi tạo hợp đồng, hệ thống sẽ tự động lấy đơn giá phù hợp</li>
                        <li>Có thể tạo nhiều đơn giá cho cùng cơ sở và trình độ (theo thời gian)</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Format số tiền
document.getElementById('don_gia').addEventListener('input', function() {
    // Remove non-numeric characters
    let value = this.value.replace(/[^\d]/g, '');
    this.value = value;
});

// Validation ngày
document.getElementById('createDonGiaForm').addEventListener('submit', function(e) {
    const ngayApDung = document.getElementById('ngay_ap_dung').value;
    const ngayKetThuc = document.getElementById('ngay_ket_thuc').value;
    
    if (ngayKetThuc && ngayKetThuc <= ngayApDung) {
        e.preventDefault();
        alert('Ngày kết thúc phải sau ngày áp dụng!');
        document.getElementById('ngay_ket_thuc').focus();
        return;
    }
    
    const donGia = parseFloat(document.getElementById('don_gia').value);
    if (!donGia || donGia <= 0) {
        e.preventDefault();
        alert('Vui lòng nhập đơn giá hợp lệ!');
        document.getElementById('don_gia').focus();
        return;
    }
});
</script>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>