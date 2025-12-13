<?php
/**
 * View: Form sửa Niên khóa (Admin)
 * File: views/admin/nien_khoa/edit.php
 */

if (!isLoggedIn()) {
    redirect('/login');
    exit;
}

$pageTitle = 'Sửa Niên khóa';
require_once __DIR__ . '/../../layouts/header.php';
?>

<div class="container-fluid px-4">
    <h1 class="mt-4"><?= $pageTitle ?></h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="/admin/nien-khoa">Quản lý Niên khóa</a></li>
        <li class="breadcrumb-item active">Sửa: <?= htmlspecialchars($nien_khoa['ten_nien_khoa']) ?></li>
    </ol>

    <?php displayFlashMessage(); ?>

    <div class="row">
        <div class="col-xl-10 col-lg-11 mx-auto">
            <div class="card mb-4">
                <div class="card-header bg-warning">
                    <i class="fas fa-edit me-1"></i>
                    Cập nhật thông tin niên khóa
                </div>
                <div class="card-body">
                    <form method="POST" action="/admin/nien-khoa/edit/<?= $nien_khoa['nien_khoa_id'] ?>" id="editNienKhoaForm">
                        
                        <!-- Khoa và Nghề -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="khoa_id" class="form-label">
                                        Khoa <span class="text-danger">*</span>
                                    </label>
                                    <select name="khoa_id" id="khoa_id" class="form-select" required>
                                        <option value="">-- Chọn khoa --</option>
                                        <?php foreach ($khoa_list as $khoa): ?>
                                            <option value="<?= $khoa['khoa_id'] ?>" 
                                                <?php 
                                                $selected_khoa = $_POST['khoa_id'] ?? $nien_khoa['khoa_id'];
                                                echo ($selected_khoa == $khoa['khoa_id']) ? 'selected' : '';
                                                ?>>
                                                [<?= htmlspecialchars($khoa['ma_khoa']) ?>] 
                                                <?= htmlspecialchars($khoa['ten_khoa']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nghe_id" class="form-label">
                                        Nghề <span class="text-danger">*</span>
                                    </label>
                                    <select name="nghe_id" id="nghe_id" class="form-select" required>
                                        <option value="">-- Chọn khoa trước --</option>
                                    </select>
                                    <div class="form-text">Chọn khoa để hiển thị danh sách nghề</div>
                                </div>
                            </div>
                        </div>

                        <!-- Cấp độ -->
                        <div class="mb-3">
                            <label for="cap_do_id" class="form-label">
                                Cấp độ giảng dạy <span class="text-danger">*</span>
                            </label>
                            <select name="cap_do_id" id="cap_do_id" class="form-select" required>
                                <option value="">-- Chọn cấp độ --</option>
                                <?php foreach ($cap_do_list as $cd): ?>
                                    <option value="<?= $cd['cap_do_id'] ?>" 
                                        <?php 
                                        $selected_cd = $_POST['cap_do_id'] ?? $nien_khoa['cap_do_id'];
                                        echo ($selected_cd == $cd['cap_do_id']) ? 'selected' : '';
                                        ?>>
                                        <?= htmlspecialchars($cd['ten_cap_do']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="row">
                            <!-- Mã niên khóa -->
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="ma_nien_khoa" class="form-label">
                                        Mã niên khóa <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="ma_nien_khoa" 
                                           name="ma_nien_khoa"
                                           value="<?= htmlspecialchars($_POST['ma_nien_khoa'] ?? $nien_khoa['ma_nien_khoa']) ?>"
                                           maxlength="20"
                                           required>
                                    <div class="form-text">Tối đa 20 ký tự</div>
                                </div>
                            </div>

                            <!-- Năm bắt đầu -->
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="nam_bat_dau" class="form-label">
                                        Năm bắt đầu <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" 
                                           class="form-control" 
                                           id="nam_bat_dau" 
                                           name="nam_bat_dau"
                                           value="<?= htmlspecialchars($_POST['nam_bat_dau'] ?? $nien_khoa['nam_bat_dau']) ?>"
                                           min="2000"
                                           max="2100"
                                           required>
                                </div>
                            </div>

                            <!-- Năm kết thúc -->
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="nam_ket_thuc" class="form-label">
                                        Năm kết thúc <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" 
                                           class="form-control" 
                                           id="nam_ket_thuc" 
                                           name="nam_ket_thuc"
                                           value="<?= htmlspecialchars($_POST['nam_ket_thuc'] ?? $nien_khoa['nam_ket_thuc']) ?>"
                                           min="2000"
                                           max="2100"
                                           required>
                                    <div class="form-text" id="soNamText">
                                        Số năm: <?= ($nien_khoa['nam_ket_thuc'] - $nien_khoa['nam_bat_dau']) ?> năm
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tên niên khóa -->
                        <div class="mb-3">
                            <label for="ten_nien_khoa" class="form-label">
                                Tên niên khóa <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control" 
                                   id="ten_nien_khoa" 
                                   name="ten_nien_khoa"
                                   value="<?= htmlspecialchars($_POST['ten_nien_khoa'] ?? $nien_khoa['ten_nien_khoa']) ?>"
                                   maxlength="50"
                                   required>
                            <div class="form-text">Tối đa 50 ký tự</div>
                        </div>

                        <!-- Mô tả -->
                        <div class="mb-3">
                            <label for="mo_ta" class="form-label">Mô tả</label>
                            <textarea class="form-control" 
                                      id="mo_ta" 
                                      name="mo_ta"
                                      rows="3"><?= htmlspecialchars($_POST['mo_ta'] ?? $nien_khoa['mo_ta']) ?></textarea>
                        </div>

                        <!-- Trạng thái -->
                        <div class="mb-3">
                            <div class="form-check">
                                <?php 
                                $is_active = $_POST['is_active'] ?? $nien_khoa['is_active'];
                                ?>
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="is_active" 
                                       name="is_active"
                                       <?= $is_active ? 'checked' : '' ?>>
                                <label class="form-check-label" for="is_active">
                                    <strong>Kích hoạt niên khóa</strong>
                                </label>
                            </div>
                        </div>

                        <hr>

                        <!-- Buttons -->
                        <div class="d-flex justify-content-between">
                            <a href="/admin/nien-khoa" class="btn btn-secondary">
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
// Load nghề khi trang load
window.addEventListener('DOMContentLoaded', async function() {
    const khoaId = document.getElementById('khoa_id').value;
    const selectedNgheId = <?= $nien_khoa['nghe_id'] ?>;
    
    if (khoaId) {
        await loadNgheByKhoa(khoaId, selectedNgheId);
    }
});

// Cascade: Khoa → Nghề
document.getElementById('khoa_id').addEventListener('change', async function() {
    await loadNgheByKhoa(this.value);
});

async function loadNgheByKhoa(khoaId, selectedNgheId = null) {
    const ngheSelect = document.getElementById('nghe_id');
    ngheSelect.innerHTML = '<option value="">-- Chọn nghề --</option>';
    
    if (!khoaId) return;
    
    try {
        const response = await fetch(`/admin/nien-khoa/get-by-khoa?khoa_id=${khoaId}&is_active=1`);
        const result = await response.json();
        
        if (result.success && result.data.length > 0) {
            result.data.forEach(nghe => {
                const option = new Option(
                    `[${nghe.ma_nghe}] ${nghe.ten_nghe}`, 
                    nghe.nghe_id,
                    false,
                    selectedNgheId && nghe.nghe_id == selectedNgheId
                );
                ngheSelect.add(option);
            });
        }
    } catch (error) {
        console.error('Error:', error);
    }
}

// Tính số năm
function updateSoNam() {
    const namBatDau = parseInt(document.getElementById('nam_bat_dau').value);
    const namKetThuc = parseInt(document.getElementById('nam_ket_thuc').value);
    
    if (namBatDau && namKetThuc) {
        const soNam = namKetThuc - namBatDau;
        document.getElementById('soNamText').textContent = `Số năm: ${soNam} năm`;
    }
}

document.getElementById('nam_bat_dau').addEventListener('change', updateSoNam);
document.getElementById('nam_ket_thuc').addEventListener('change', updateSoNam);

// Auto uppercase mã
document.getElementById('ma_nien_khoa').addEventListener('input', function() {
    this.value = this.value.toUpperCase();
});

// Form validation
document.getElementById('editNienKhoaForm').addEventListener('submit', function(e) {
    const namBatDau = parseInt(document.getElementById('nam_bat_dau').value);
    const namKetThuc = parseInt(document.getElementById('nam_ket_thuc').value);
    
    if (namKetThuc <= namBatDau) {
        e.preventDefault();
        alert('Năm kết thúc phải lớn hơn năm bắt đầu!');
        return;
    }
    
    if ((namKetThuc - namBatDau) > 10) {
        e.preventDefault();
        alert('Khoảng cách không được quá 10 năm!');
        return;
    }
});
</script>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>