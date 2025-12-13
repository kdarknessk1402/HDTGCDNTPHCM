<?php
/**
 * View: Form sửa Hợp đồng (Admin)
 * File: views/admin/hop_dong/edit.php
 */

if (!isLoggedIn()) { redirect('/login'); exit; }
$pageTitle = 'Sửa Hợp đồng';
require_once __DIR__ . '/../../layouts/header.php';
?>

<div class="container-fluid px-4">
    <h1 class="mt-4"><?= $pageTitle ?></h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="/admin/hop-dong">Quản lý Hợp đồng</a></li>
        <li class="breadcrumb-item active">Sửa: <?= htmlspecialchars($hop_dong['so_hop_dong']) ?></li>
    </ol>

    <?php displayFlashMessage(); ?>

    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header bg-warning"><i class="fas fa-edit me-1"></i> Cập nhật hợp đồng</div>
                <div class="card-body">
                    <form method="POST" action="/admin/hop-dong/edit/<?= $hop_dong['hop_dong_id'] ?>" enctype="multipart/form-data">
                        
                        <ul class="nav nav-tabs mb-3" role="tablist">
                            <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#tab1">1. Thông tin HĐ</a></li>
                            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab2">2. Lớp & Môn học</a></li>
                            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab3">3. Thời gian & Đơn giá</a></li>
                            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab4">4. Thanh toán</a></li>
                        </ul>

                        <div class="tab-content">
                            <!-- TAB 1 -->
                            <div class="tab-pane fade show active" id="tab1">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Số hợp đồng</label>
                                            <input type="text" name="so_hop_dong" class="form-control" value="<?= htmlspecialchars($hop_dong['so_hop_dong']) ?>" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Ngày hợp đồng <span class="text-danger">*</span></label>
                                            <input type="date" name="ngay_hop_dong" id="ngay_hop_dong" class="form-control" value="<?= $hop_dong['ngay_hop_dong'] ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Giảng viên <span class="text-danger">*</span></label>
                                            <select name="giang_vien_id" id="giang_vien_id" class="form-select" required>
                                                <?php foreach ($giang_vien_list as $gv): ?>
                                                    <option value="<?= $gv['giang_vien_id'] ?>" 
                                                            data-trinh-do="<?= $gv['trinh_do_id'] ?>"
                                                            <?= ($hop_dong['giang_vien_id'] == $gv['giang_vien_id']) ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($gv['ten_giang_vien']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Trạng thái <span class="text-danger">*</span></label>
                                    <select name="trang_thai" class="form-select" required>
                                        <option value="Mới tạo" <?= ($hop_dong['trang_thai'] == 'Mới tạo') ? 'selected' : '' ?>>Mới tạo</option>
                                        <option value="Đã duyệt" <?= ($hop_dong['trang_thai'] == 'Đã duyệt') ? 'selected' : '' ?>>Đã duyệt</option>
                                        <option value="Đang thực hiện" <?= ($hop_dong['trang_thai'] == 'Đang thực hiện') ? 'selected' : '' ?>>Đang thực hiện</option>
                                        <option value="Hoàn thành" <?= ($hop_dong['trang_thai'] == 'Hoàn thành') ? 'selected' : '' ?>>Hoàn thành</option>
                                        <option value="Hủy" <?= ($hop_dong['trang_thai'] == 'Hủy') ? 'selected' : '' ?>>Hủy</option>
                                    </select>
                                </div>
                            </div>

                            <!-- TAB 2 -->
                            <div class="tab-pane fade" id="tab2">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label class="form-label">Khoa <span class="text-danger">*</span></label>
                                            <select name="khoa_id" id="khoa_id" class="form-select" required>
                                                <?php foreach ($khoa_list as $k): ?>
                                                    <option value="<?= $k['khoa_id'] ?>" <?= ($hop_dong['khoa_id'] == $k['khoa_id']) ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($k['ten_khoa']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label class="form-label">Nghề <span class="text-danger">*</span></label>
                                            <select name="nghe_id" id="nghe_id" class="form-select" required>
                                                <option value="">-- Chọn khoa trước --</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label class="form-label">Niên khóa <span class="text-danger">*</span></label>
                                            <select name="nien_khoa_id" id="nien_khoa_id" class="form-select" required>
                                                <option value="">-- Chọn nghề trước --</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label class="form-label">Lớp <span class="text-danger">*</span></label>
                                            <select name="lop_id" id="lop_id" class="form-select" required>
                                                <option value="">-- Chọn nghề trước --</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Môn học <span class="text-danger">*</span></label>
                                    <select name="mon_hoc_id" id="mon_hoc_id" class="form-select" required>
                                        <option value="">-- Chọn lớp trước --</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Cấp độ giảng dạy <span class="text-danger">*</span></label>
                                    <select name="cap_do_id" class="form-select" required>
                                        <?php foreach ($cap_do_list as $cd): ?>
                                            <option value="<?= $cd['cap_do_id'] ?>" <?= ($hop_dong['cap_do_id'] == $cd['cap_do_id']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($cd['ten_cap_do']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <!-- TAB 3 -->
                            <div class="tab-pane fade" id="tab3">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Cơ sở <span class="text-danger">*</span></label>
                                            <select name="co_so_id" id="co_so_id" class="form-select" required>
                                                <?php foreach ($co_so_list as $cs): ?>
                                                    <option value="<?= $cs['co_so_id'] ?>" <?= ($hop_dong['co_so_id'] == $cs['co_so_id']) ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($cs['ten_co_so']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Ngày bắt đầu <span class="text-danger">*</span></label>
                                            <input type="date" name="ngay_bat_dau" class="form-control" value="<?= $hop_dong['ngay_bat_dau'] ?>" required>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Ngày kết thúc <span class="text-danger">*</span></label>
                                            <input type="date" name="ngay_ket_thuc" class="form-control" value="<?= $hop_dong['ngay_ket_thuc'] ?>" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Tổng giờ giảng dạy <span class="text-danger">*</span></label>
                                            <input type="number" name="tong_gio_mon_hoc" id="tong_gio_mon_hoc" class="form-control" value="<?= $hop_dong['tong_gio_mon_hoc'] ?>" min="1" required>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Đơn giá/giờ (VNĐ) <span class="text-danger">*</span></label>
                                            <input type="number" name="don_gia_gio" id="don_gia_gio" class="form-control" value="<?= $hop_dong['don_gia_gio'] ?>" min="0" required>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Tổng tiền (VNĐ)</label>
                                            <input type="text" id="tong_tien_display" class="form-control" value="<?= number_format($hop_dong['tong_tien'], 0, ',', '.') ?> VNĐ" readonly>
                                            <input type="hidden" name="tong_tien" id="tong_tien" value="<?= $hop_dong['tong_tien'] ?>">
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Tổng tiền bằng chữ</label>
                                    <input type="text" name="tong_tien_chu" id="tong_tien_chu" class="form-control" value="<?= htmlspecialchars($hop_dong['tong_tien_chu']) ?>">
                                </div>
                            </div>

                            <!-- TAB 4 -->
                            <div class="tab-pane fade" id="tab4">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Hình thức thanh toán</label>
                                            <select name="hinh_thuc_thanh_toan" class="form-select">
                                                <option value="Chuyển khoản" <?= ($hop_dong['hinh_thuc_thanh_toan'] == 'Chuyển khoản') ? 'selected' : '' ?>>Chuyển khoản</option>
                                                <option value="Tiền mặt" <?= ($hop_dong['hinh_thuc_thanh_toan'] == 'Tiền mặt') ? 'selected' : '' ?>>Tiền mặt</option>
                                                <option value="Khác" <?= ($hop_dong['hinh_thuc_thanh_toan'] == 'Khác') ? 'selected' : '' ?>>Khác</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Đã thanh toán (VNĐ)</label>
                                            <input type="number" name="da_thanh_toan" class="form-control" value="<?= $hop_dong['da_thanh_toan'] ?>" min="0">
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Ngày thanh toán</label>
                                            <input type="date" name="ngay_thanh_toan" class="form-control" value="<?= $hop_dong['ngay_thanh_toan'] ?>">
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">File hợp đồng</label>
                                    <?php if ($hop_dong['file_hop_dong']): ?>
                                        <div class="mb-2">
                                            <a href="/uploads/hop_dong/<?= htmlspecialchars($hop_dong['file_hop_dong']) ?>" target="_blank" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i> Xem file hiện tại
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                    <input type="file" name="file_hop_dong" class="form-control" accept=".pdf,.doc,.docx">
                                    <small class="text-muted">Upload file mới để thay thế</small>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">File biên bản giao nhận</label>
                                    <?php if ($hop_dong['file_bien_ban_giao_nhan']): ?>
                                        <div class="mb-2">
                                            <a href="/uploads/hop_dong/<?= htmlspecialchars($hop_dong['file_bien_ban_giao_nhan']) ?>" target="_blank" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i> Xem file hiện tại
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                    <input type="file" name="file_bien_ban_giao_nhan" class="form-control" accept=".pdf,.doc,.docx">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Ghi chú</label>
                                    <textarea name="ghi_chu" class="form-control" rows="3"><?= htmlspecialchars($hop_dong['ghi_chu']) ?></textarea>
                                </div>
                            </div>
                        </div>

                        <hr>
                        <div class="d-flex justify-content-between">
                            <a href="/admin/hop-dong" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Quay lại</a>
                            <button type="submit" class="btn btn-warning"><i class="fas fa-save"></i> Cập nhật</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Load data on page load
window.addEventListener('DOMContentLoaded', async function() {
    const khoaId = document.getElementById('khoa_id').value;
    const selectedNgheId = <?= $hop_dong['nghe_id'] ?>;
    const selectedNienKhoaId = <?= $hop_dong['nien_khoa_id'] ?>;
    const selectedLopId = <?= $hop_dong['lop_id'] ?>;
    const selectedMonHocId = <?= $hop_dong['mon_hoc_id'] ?>;
    
    if (khoaId) {
        await loadNghe(khoaId, selectedNgheId);
        if (selectedNgheId) {
            await loadNienKhoa(selectedNgheId, selectedNienKhoaId);
            await loadLop(selectedNgheId, selectedLopId);
            if (selectedLopId) {
                await loadMonHoc(selectedLopId, selectedMonHocId);
            }
        }
    }
});

// CASCADE functions
document.getElementById('khoa_id').addEventListener('change', async function() {
    await loadNghe(this.value);
});

document.getElementById('nghe_id').addEventListener('change', async function() {
    await loadNienKhoa(this.value);
    await loadLop(this.value);
});

document.getElementById('lop_id').addEventListener('change', async function() {
    await loadMonHoc(this.value);
});

async function loadNghe(khoaId, selectedId = null) {
    const ngheSelect = document.getElementById('nghe_id');
    ngheSelect.innerHTML = '<option value="">-- Chọn nghề --</option>';
    
    if (!khoaId) return;
    
    try {
        const res = await fetch(`/admin/hop-dong/get-nghe-by-khoa?khoa_id=${khoaId}`);
        const data = await res.json();
        if (data.success) {
            data.data.forEach(n => {
                const opt = new Option(n.ten_nghe, n.nghe_id, false, selectedId && n.nghe_id == selectedId);
                ngheSelect.add(opt);
            });
        }
    } catch (e) {}
}

async function loadNienKhoa(ngheId, selectedId = null) {
    const nkSelect = document.getElementById('nien_khoa_id');
    nkSelect.innerHTML = '<option value="">-- Chọn niên khóa --</option>';
    
    if (!ngheId) return;
    
    try {
        const res = await fetch(`/admin/hop-dong/get-nien-khoa-by-nghe?nghe_id=${ngheId}`);
        const data = await res.json();
        if (data.success) {
            data.data.forEach(nk => {
                const opt = new Option(nk.ten_nien_khoa, nk.nien_khoa_id, false, selectedId && nk.nien_khoa_id == selectedId);
                nkSelect.add(opt);
            });
        }
    } catch (e) {}
}

async function loadLop(ngheId, selectedId = null) {
    const lopSelect = document.getElementById('lop_id');
    lopSelect.innerHTML = '<option value="">-- Chọn lớp --</option>';
    
    if (!ngheId) return;
    
    try {
        const res = await fetch(`/admin/hop-dong/get-lop-by-nghe?nghe_id=${ngheId}`);
        const data = await res.json();
        if (data.success) {
            data.data.forEach(l => {
                const opt = new Option(l.ten_lop, l.lop_id, false, selectedId && l.lop_id == selectedId);
                lopSelect.add(opt);
            });
        }
    } catch (e) {}
}

async function loadMonHoc(lopId, selectedId = null) {
    const mhSelect = document.getElementById('mon_hoc_id');
    mhSelect.innerHTML = '<option value="">-- Chọn môn học --</option>';
    
    if (!lopId) return;
    
    try {
        const res = await fetch(`/admin/hop-dong/get-mon-hoc-by-lop?lop_id=${lopId}`);
        const data = await res.json();
        if (data.success) {
            data.data.forEach(mh => {
                const opt = new Option(`${mh.ten_mon_hoc} (${mh.tong_so_tiet} tiết)`, mh.mon_hoc_id, false, selectedId && mh.mon_hoc_id == selectedId);
                mhSelect.add(opt);
            });
        }
    } catch (e) {}
}

// Tính tổng tiền
document.getElementById('tong_gio_mon_hoc').addEventListener('input', tinhTongTien);
document.getElementById('don_gia_gio').addEventListener('input', tinhTongTien);

function tinhTongTien() {
    const gio = parseFloat(document.getElementById('tong_gio_mon_hoc').value) || 0;
    const donGia = parseFloat(document.getElementById('don_gia_gio').value) || 0;
    const tongTien = gio * donGia;
    
    document.getElementById('tong_tien').value = tongTien;
    document.getElementById('tong_tien_display').value = tongTien.toLocaleString('vi-VN') + ' VNĐ';
}
</script>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>