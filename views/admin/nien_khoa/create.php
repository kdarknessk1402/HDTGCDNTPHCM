<?php
/**
 * View: Form thêm Niên khóa mới (Admin)
 * File: views/admin/nien_khoa/create.php
 */

if (!isLoggedIn()) {
    redirect('/login');
    exit;
}

$pageTitle = 'Thêm Niên khóa Mới';
require_once __DIR__ . '/../../layouts/header.php';
?>

<div class="container-fluid px-4">
    <h1 class="mt-4"><?= $pageTitle ?></h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="/admin/nien-khoa">Quản lý Niên khóa</a></li>
        <li class="breadcrumb-item active">Thêm mới</li>
    </ol>

    <?php displayFlashMessage(); ?>

    <div class="row">
        <div class="col-xl-10 col-lg-11 mx-auto">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-plus-circle me-1"></i>
                    Thông tin niên khóa mới
                </div>
                <div class="card-body">
                    <form method="POST" action="/admin/nien-khoa/create" id="createNienKhoaForm">
                        
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
                                                <?= (isset($_POST['khoa_id']) && $_POST['khoa_id'] == $khoa['khoa_id']) ? 'selected' : '' ?>>
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
                                        <?= (isset($_POST['cap_do_id']) && $_POST['cap_do_id'] == $cd['cap_do_id']) ? 'selected' : '' ?>>
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
                                           value="<?= htmlspecialchars($_POST['ma_nien_khoa'] ?? '') ?>"
                                           maxlength="20"
                                           required
                                           placeholder="VD: NK_CNTT01_CD_2023">
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
                                           value="<?= htmlspecialchars($_POST['nam_bat_dau'] ?? date('Y')) ?>"
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
                                           value="<?= htmlspecialchars($_POST['nam_ket_thuc'] ?? (date('Y') + 3)) ?>"
                                           min="2000"
                                           max="2100"
                                           required>
                                    <div class="form-text" id="soNamText">Số năm: 3 năm</div>
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
                                   value="<?= htmlspecialchars($_POST['ten_nien_khoa'] ?? '') ?>"
                                   maxlength="50"
                                   required
                                   placeholder="VD: Niên khóa 2023-2026 - Lập trình máy tính (Cao đẳng)">
                            <div class="form-text">Tối đa 50 ký tự. Tên sẽ tự động được đề xuất khi chọn nghề, cấp độ và năm.</div>
                        </div>

                        <!-- Mô tả -->
                        <div class="mb-3">
                            <label for="mo_ta" class="form-label">Mô tả</label>
                            <textarea class="form-control" 
                                      id="mo_ta" 
                                      name="mo_ta"
                                      rows="3"
                                      placeholder="Nhập mô tả về niên khóa..."><?= htmlspecialchars($_POST['mo_ta'] ?? '') ?></textarea>
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
                                    <strong>Kích hoạt niên khóa</strong>
                                    <small class="text-muted d-block">
                                        Niên khóa đang hoạt động sẽ hiển thị trong danh sách chọn khi tạo Lớp, Môn học, Hợp đồng
                                    </small>
                                </label>
                            </div>
                        </div>

                        <hr>

                        <!-- Buttons -->
                        <div class="d-flex justify-content-between">
                            <a href="/admin/nien-khoa" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Quay lại
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Lưu niên khóa mới
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
                        <li>Chọn <strong>Khoa</strong> trước để hiển thị danh sách Nghề</li>
                        <li>Niên khóa <strong>không được trùng</strong> với (Nghề + Cấp độ + Tên niên khóa)</li>
                        <li>Năm kết thúc phải <strong>lớn hơn</strong> năm bắt đầu</li>
                        <li>Khoảng cách giữa năm bắt đầu và kết thúc <strong>không quá 10 năm</strong></li>
                        <li>Tên niên khóa thường theo format: <code>Niên khóa [năm BĐ]-[năm KT] - [Tên nghề] ([Cấp độ])</code></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Cascade: Khoa → Nghề
document.getElementById('khoa_id').addEventListener('change', async function() {
    const khoaId = this.value;
    const ngheSelect = document.getElementById('nghe_id');
    
    // Reset
    ngheSelect.innerHTML = '<option value="">-- Chọn nghề --</option>';
    
    if (!khoaId) return;
    
    try {
        const response = await fetch(`/admin/nien-khoa/get-by-khoa?khoa_id=${khoaId}&is_active=1`);
        const result = await response.json();
        
        if (result.success && result.data.length > 0) {
            result.data.forEach(nghe => {
                const option = new Option(
                    `[${nghe.ma_nghe}] ${nghe.ten_nghe} (${nghe.so_nam_dao_tao} năm)`, 
                    nghe.nghe_id
                );
                option.dataset.tenNghe = nghe.ten_nghe;
                ngheSelect.add(option);
            });
        } else {
            ngheSelect.innerHTML = '<option value="">-- Khoa này chưa có nghề --</option>';
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Có lỗi khi tải danh sách nghề!');
    }
});

// Tính số năm và đề xuất tên
function updateTenNienKhoa() {
    const namBatDau = parseInt(document.getElementById('nam_bat_dau').value);
    const namKetThuc = parseInt(document.getElementById('nam_ket_thuc').value);
    const ngheSelect = document.getElementById('nghe_id');
    const capDoSelect = document.getElementById('cap_do_id');
    const tenNienKhoaInput = document.getElementById('ten_nien_khoa');
    
    if (namBatDau && namKetThuc) {
        const soNam = namKetThuc - namBatDau;
        document.getElementById('soNamText').textContent = `Số năm: ${soNam} năm`;
        
        // Đề xuất tên
        if (ngheSelect.value && capDoSelect.value) {
            const tenNghe = ngheSelect.options[ngheSelect.selectedIndex].dataset.tenNghe;
            const tenCapDo = capDoSelect.options[capDoSelect.selectedIndex].text;
            
            const tenDeXuat = `Niên khóa ${namBatDau}-${namKetThuc} - ${tenNghe} (${tenCapDo})`;
            
            if (!tenNienKhoaInput.value || confirm('Bạn có muốn dùng tên đề xuất?')) {
                tenNienKhoaInput.value = tenDeXuat;
            }
        }
    }
}

document.getElementById('nam_bat_dau').addEventListener('change', updateTenNienKhoa);
document.getElementById('nam_ket_thuc').addEventListener('change', updateTenNienKhoa);
document.getElementById('nghe_id').addEventListener('change', updateTenNienKhoa);
document.getElementById('cap_do_id').addEventListener('change', updateTenNienKhoa);

// Auto uppercase mã
document.getElementById('ma_nien_khoa').addEventListener('input', function() {
    this.value = this.value.toUpperCase();
});

// Form validation
document.getElementById('createNienKhoaForm').addEventListener('submit', function(e) {
    const namBatDau = parseInt(document.getElementById('nam_bat_dau').value);
    const namKetThuc = parseInt(document.getElementById('nam_ket_thuc').value);
    
    if (namKetThuc <= namBatDau) {
        e.preventDefault();
        alert('Năm kết thúc phải lớn hơn năm bắt đầu!');
        document.getElementById('nam_ket_thuc').focus();
        return;
    }
    
    if ((namKetThuc - namBatDau) > 10) {
        e.preventDefault();
        alert('Khoảng cách giữa năm bắt đầu và kết thúc không được quá 10 năm!');
        return;
    }
});
</script>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>