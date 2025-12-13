<?php
/**
 * View: Form thêm Hợp đồng (Admin)
 * File: views/admin/hop_dong/create.php
 * PHẦN 1: HTML FORM
 */

if (!isLoggedIn()) { redirect('/login'); exit; }
$pageTitle = 'Thêm Hợp đồng Mới';
require_once __DIR__ . '/../../layouts/header.php';
?>

<div class="container-fluid px-4">
    <h1 class="mt-4"><?= $pageTitle ?></h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="/admin/hop-dong">Quản lý Hợp đồng</a></li>
        <li class="breadcrumb-item active">Thêm mới</li>
    </ol>

    <?php displayFlashMessage(); ?>

    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header"><i class="fas fa-plus-circle me-1"></i> Thông tin hợp đồng mới</div>
                <div class="card-body">
                    <form method="POST" action="/admin/hop-dong/create" enctype="multipart/form-data" id="createHDForm">
                        
                        <!-- Tab navigation -->
                        <ul class="nav nav-tabs mb-3" role="tablist">
                            <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#tab1">1. Thông tin HĐ</a></li>
                            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab2">2. Lớp & Môn học</a></li>
                            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab3">3. Thời gian & Đơn giá</a></li>
                            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab4">4. Thanh toán</a></li>
                        </ul>

                        <div class="tab-content">
                            <!-- TAB 1: Thông tin hợp đồng -->
                            <div class="tab-pane fade show active" id="tab1">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Số hợp đồng</label>
                                            <input type="text" name="so_hop_dong" class="form-control" placeholder="Tự động nếu để trống">
                                            <small class="text-muted">Để trống để tự động tạo số</small>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Ngày hợp đồng <span class="text-danger">*</span></label>
                                            <input type="date" name="ngay_hop_dong" id="ngay_hop_dong" class="form-control" value="<?= date('Y-m-d') ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Giảng viên <span class="text-danger">*</span></label>
                                            <select name="giang_vien_id" id="giang_vien_id" class="form-select" required>
                                                <option value="">-- Chọn giảng viên --</option>
                                                <?php foreach ($giang_vien_list as $gv): ?>
                                                    <option value="<?= $gv['giang_vien_id'] ?>" data-trinh-do="<?= $gv['trinh_do_id'] ?>">
                                                        <?= htmlspecialchars($gv['ten_giang_vien']) ?> (<?= htmlspecialchars($gv['ma_giang_vien']) ?>)
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> <strong>Hướng dẫn:</strong>
                                    <ul class="mb-0 mt-2">
                                        <li>Bước 1: Chọn Giảng viên và Khoa</li>
                                        <li>Bước 2: Chọn Nghề → Lớp → Môn học</li>
                                        <li>Bước 3: Chọn Cơ sở → Hệ thống tự động lấy đơn giá</li>
                                    </ul>
                                </div>
                            </div>

                            <!-- TAB 2: Cascade 5 cấp -->
                            <div class="tab-pane fade" id="tab2">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label class="form-label">Khoa <span class="text-danger">*</span></label>
                                            <select name="khoa_id" id="khoa_id" class="form-select" required>
                                                <option value="">-- Chọn khoa --</option>
                                                <?php foreach ($khoa_list as $k): ?>
                                                    <option value="<?= $k['khoa_id'] ?>"><?= htmlspecialchars($k['ten_khoa']) ?></option>
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
                                    <select name="cap_do_id" id="cap_do_id" class="form-select" required>
                                        <option value="">-- Chọn cấp độ --</option>
                                        <?php foreach ($cap_do_list as $cd): ?>
                                            <option value="<?= $cd['cap_do_id'] ?>"><?= htmlspecialchars($cd['ten_cap_do']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <!-- TAB 3: Thời gian & Đơn giá -->
                            <div class="tab-pane fade" id="tab3">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Cơ sở <span class="text-danger">*</span></label>
                                            <select name="co_so_id" id="co_so_id" class="form-select" required>
                                                <option value="">-- Chọn cơ sở --</option>
                                                <?php foreach ($co_so_list as $cs): ?>
                                                    <option value="<?= $cs['co_so_id'] ?>"><?= htmlspecialchars($cs['ten_co_so']) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Ngày bắt đầu <span class="text-danger">*</span></label>
                                            <input type="date" name="ngay_bat_dau" class="form-control" required>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Ngày kết thúc <span class="text-danger">*</span></label>
                                            <input type="date" name="ngay_ket_thuc" class="form-control" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Tổng giờ giảng dạy <span class="text-danger">*</span></label>
                                            <input type="number" name="tong_gio_mon_hoc" id="tong_gio_mon_hoc" class="form-control" min="1" required>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Đơn giá/giờ (VNĐ) <span class="text-danger">*</span></label>
                                            <input type="number" name="don_gia_gio" id="don_gia_gio" class="form-control" min="0" readonly required>
                                            <small class="text-muted">Tự động lấy theo Cơ sở × Trình độ</small>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Tổng tiền (VNĐ)</label>
                                            <input type="text" id="tong_tien_display" class="form-control" readonly>
                                            <input type="hidden" name="tong_tien" id="tong_tien">
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Tổng tiền bằng chữ</label>
                                    <input type="text" name="tong_tien_chu" id="tong_tien_chu" class="form-control" readonly>
                                </div>
                            </div>

                            <!-- TAB 4: Thanh toán -->
                            <div class="tab-pane fade" id="tab4">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Hình thức thanh toán</label>
                                            <select name="hinh_thuc_thanh_toan" class="form-select">
                                                <option value="Chuyển khoản" selected>Chuyển khoản</option>
                                                <option value="Tiền mặt">Tiền mặt</option>
                                                <option value="Khác">Khác</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Đã thanh toán (VNĐ)</label>
                                            <input type="number" name="da_thanh_toan" class="form-control" value="0" min="0">
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Ngày thanh toán</label>
                                            <input type="date" name="ngay_thanh_toan" class="form-control">
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">File hợp đồng (PDF, DOCX - tối đa 10MB)</label>
                                    <input type="file" name="file_hop_dong" class="form-control" accept=".pdf,.doc,.docx">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">File biên bản giao nhận (PDF, DOCX - tối đa 10MB)</label>
                                    <input type="file" name="file_bien_ban_giao_nhan" class="form-control" accept=".pdf,.doc,.docx">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Ghi chú</label>
                                    <textarea name="ghi_chu" class="form-control" rows="3"></textarea>
                                </div>
                            </div>
                        </div>

                        <hr>
                        <div class="d-flex justify-content-between">
                            <a href="/admin/hop-dong" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Quay lại</a>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Lưu hợp đồng</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- PHẦN 2: JAVASCRIPT CASCADE + TÍNH TIỀN TỰ ĐỘNG -->

<script>
let trinhDoGiangVien = null;

// Lấy trình độ khi chọn giảng viên
document.getElementById('giang_vien_id').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    trinhDoGiangVien = selectedOption.dataset.trinhDo;
    
    // Nếu đã chọn cơ sở, tự động lấy đơn giá
    if (document.getElementById('co_so_id').value) {
        layDonGiaHienHanh();
    }
});

// CASCADE 1: Khoa → Nghề
document.getElementById('khoa_id').addEventListener('change', async function() {
    const khoaId = this.value;
    const ngheSelect = document.getElementById('nghe_id');
    const nienKhoaSelect = document.getElementById('nien_khoa_id');
    const lopSelect = document.getElementById('lop_id');
    const monHocSelect = document.getElementById('mon_hoc_id');
    
    // Reset
    ngheSelect.innerHTML = '<option value="">-- Chọn nghề --</option>';
    nienKhoaSelect.innerHTML = '<option value="">-- Chọn nghề trước --</option>';
    lopSelect.innerHTML = '<option value="">-- Chọn nghề trước --</option>';
    monHocSelect.innerHTML = '<option value="">-- Chọn lớp trước --</option>';
    
    if (!khoaId) return;
    
    try {
        const res = await fetch(`/admin/hop-dong/get-nghe-by-khoa?khoa_id=${khoaId}`);
        const data = await res.json();
        if (data.success && data.data.length > 0) {
            data.data.forEach(n => {
                ngheSelect.add(new Option(`${n.ten_nghe}`, n.nghe_id));
            });
        }
    } catch (e) { console.error(e); }
});

// CASCADE 2: Nghề → Niên khóa + Lớp
document.getElementById('nghe_id').addEventListener('change', async function() {
    const ngheId = this.value;
    const nienKhoaSelect = document.getElementById('nien_khoa_id');
    const lopSelect = document.getElementById('lop_id');
    const monHocSelect = document.getElementById('mon_hoc_id');
    
    // Reset
    nienKhoaSelect.innerHTML = '<option value="">-- Chọn niên khóa --</option>';
    lopSelect.innerHTML = '<option value="">-- Chọn lớp --</option>';
    monHocSelect.innerHTML = '<option value="">-- Chọn lớp trước --</option>';
    
    if (!ngheId) return;
    
    // Load niên khóa
    try {
        const res = await fetch(`/admin/hop-dong/get-nien-khoa-by-nghe?nghe_id=${ngheId}`);
        const data = await res.json();
        if (data.success && data.data.length > 0) {
            data.data.forEach(nk => {
                nienKhoaSelect.add(new Option(`${nk.ten_nien_khoa}`, nk.nien_khoa_id));
            });
        }
    } catch (e) { console.error(e); }
    
    // Load lớp
    try {
        const res = await fetch(`/admin/hop-dong/get-lop-by-nghe?nghe_id=${ngheId}`);
        const data = await res.json();
        if (data.success && data.data.length > 0) {
            data.data.forEach(l => {
                lopSelect.add(new Option(`${l.ten_lop}`, l.lop_id));
            });
        }
    } catch (e) { console.error(e); }
});

// CASCADE 3: Lớp → Môn học
document.getElementById('lop_id').addEventListener('change', async function() {
    const lopId = this.value;
    const monHocSelect = document.getElementById('mon_hoc_id');
    
    monHocSelect.innerHTML = '<option value="">-- Chọn môn học --</option>';
    
    if (!lopId) return;
    
    try {
        const res = await fetch(`/admin/hop-dong/get-mon-hoc-by-lop?lop_id=${lopId}`);
        const data = await res.json();
        if (data.success && data.data.length > 0) {
            data.data.forEach(mh => {
                monHocSelect.add(new Option(`${mh.ten_mon_hoc} (${mh.tong_so_tiet} tiết)`, mh.mon_hoc_id));
            });
        }
    } catch (e) { console.error(e); }
});

// Lấy đơn giá khi chọn Cơ sở
document.getElementById('co_so_id').addEventListener('change', function() {
    if (trinhDoGiangVien) {
        layDonGiaHienHanh();
    } else {
        alert('Vui lòng chọn Giảng viên trước!');
        this.value = '';
    }
});

// Hàm lấy đơn giá hiện hành
async function layDonGiaHienHanh() {
    const coSoId = document.getElementById('co_so_id').value;
    const ngayHD = document.getElementById('ngay_hop_dong').value;
    
    if (!coSoId || !trinhDoGiangVien || !ngayHD) return;
    
    try {
        const res = await fetch(`/admin/hop-dong/get-don-gia-hien-hanh?co_so_id=${coSoId}&trinh_do_id=${trinhDoGiangVien}&ngay=${ngayHD}`);
        const data = await res.json();
        
        if (data.success) {
            document.getElementById('don_gia_gio').value = data.don_gia;
            tinhTongTien();
        } else {
            alert('Không tìm thấy đơn giá cho Cơ sở này và Trình độ Giảng viên!');
            document.getElementById('don_gia_gio').value = '';
        }
    } catch (e) {
        console.error(e);
    }
}

// Tính tổng tiền khi thay đổi giờ
document.getElementById('tong_gio_mon_hoc').addEventListener('input', tinhTongTien);

// Hàm tính tổng tiền
function tinhTongTien() {
    const gio = parseFloat(document.getElementById('tong_gio_mon_hoc').value) || 0;
    const donGia = parseFloat(document.getElementById('don_gia_gio').value) || 0;
    const tongTien = gio * donGia;
    
    document.getElementById('tong_tien').value = tongTien;
    document.getElementById('tong_tien_display').value = tongTien.toLocaleString('vi-VN') + ' VNĐ';
    
    // Chuyển sang chữ (đơn giản)
    if (tongTien > 0) {
        document.getElementById('tong_tien_chu').value = docSo(tongTien) + ' đồng';
    }
}

// Hàm đọc số sang chữ (đơn giản)
function docSo(so) {
    const donVi = ['', 'nghìn', 'triệu', 'tỷ'];
    const chu = ['không', 'một', 'hai', 'ba', 'bốn', 'năm', 'sáu', 'bảy', 'tám', 'chín'];
    
    if (so === 0) return 'Không';
    
    let ketQua = '';
    let donViIndex = 0;
    
    while (so > 0) {
        const phan = so % 1000;
        if (phan > 0) {
            const tram = Math.floor(phan / 100);
            const chuc = Math.floor((phan % 100) / 10);
            const donvi = phan % 10;
            
            let chuoi = '';
            if (tram > 0) chuoi += chu[tram] + ' trăm ';
            if (chuc > 1) chuoi += chu[chuc] + ' mươi ';
            else if (chuc === 1) chuoi += 'mười ';
            if (donvi > 0) chuoi += chu[donvi] + ' ';
            
            ketQua = chuoi + donVi[donViIndex] + ' ' + ketQua;
        }
        
        so = Math.floor(so / 1000);
        donViIndex++;
    }
    
    return ketQua.trim().charAt(0).toUpperCase() + ketQua.trim().slice(1);
}
</script>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>