<?php
/**
 * View: Form thêm Giảng viên (Admin)
 * File: views/admin/giang_vien/create.php
 */

if (!isLoggedIn()) { redirect('/login'); exit; }
$pageTitle = 'Thêm Giảng viên Mới';
require_once __DIR__ . '/../../layouts/header.php';
?>

<div class="container-fluid px-4">
    <h1 class="mt-4"><?= $pageTitle ?></h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="/admin/giang-vien">Quản lý Giảng viên</a></li>
        <li class="breadcrumb-item active">Thêm mới</li>
    </ol>

    <?php displayFlashMessage(); ?>

    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header"><i class="fas fa-plus-circle me-1"></i> Thông tin giảng viên mới</div>
                <div class="card-body">
                    <form method="POST" action="/admin/giang-vien/create" enctype="multipart/form-data" id="createGVForm">
                        
                        <!-- Nav Tabs -->
                        <ul class="nav nav-tabs mb-3" role="tablist">
                            <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#tab1">1. Thông tin cơ bản</a></li>
                            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab2">2. CCCD & Trình độ</a></li>
                            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab3">3. Liên hệ & Ngân hàng</a></li>
                            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab4">4. File đính kèm</a></li>
                        </ul>

                        <div class="tab-content">
                            <!-- TAB 1: Thông tin cơ bản -->
                            <div class="tab-pane fade show active" id="tab1">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Khoa <span class="text-danger">*</span></label>
                                            <select name="khoa_id" class="form-select" required>
                                                <option value="">-- Chọn khoa --</option>
                                                <?php foreach ($khoa_list as $k): ?>
                                                    <option value="<?= $k['khoa_id'] ?>"><?= htmlspecialchars($k['ten_khoa']) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Mã giảng viên</label>
                                            <input type="text" name="ma_giang_vien" class="form-control" placeholder="Tự động nếu để trống">
                                            <small class="text-muted">Để trống để tự động tạo mã</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Họ và tên <span class="text-danger">*</span></label>
                                    <input type="text" name="ten_giang_vien" class="form-control" required maxlength="100" placeholder="VD: Nguyễn Văn A">
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Giới tính</label>
                                            <select name="gioi_tinh" class="form-select">
                                                <option value="Nam" selected>Nam</option>
                                                <option value="Nữ">Nữ</option>
                                                <option value="Khác">Khác</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Ngày sinh</label>
                                            <input type="date" name="ngay_sinh" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Năm sinh</label>
                                            <input type="number" name="nam_sinh" class="form-control" min="1950" max="2010" placeholder="VD: 1985">
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Nơi sinh</label>
                                    <input type="text" name="noi_sinh" class="form-control" maxlength="200">
                                </div>
                            </div>

                            <!-- TAB 2: CCCD & Trình độ -->
                            <div class="tab-pane fade" id="tab2">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Số CCCD/CMND</label>
                                            <input type="text" name="so_cccd" class="form-control" maxlength="20">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Ngày cấp</label>
                                            <input type="date" name="ngay_cap_cccd" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Nơi cấp</label>
                                            <input type="text" name="noi_cap_cccd" class="form-control" value="Cục Cảnh sát Quản lý Hành chính về Trật tự xã hội" maxlength="200">
                                        </div>
                                    </div>
                                </div>

                                <hr>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Trình độ chuyên môn</label>
                                            <select name="trinh_do_id" class="form-select">
                                                <option value="">-- Chọn trình độ --</option>
                                                <?php foreach ($trinh_do_list as $td): ?>
                                                    <option value="<?= $td['trinh_do_id'] ?>"><?= htmlspecialchars($td['ten_trinh_do']) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Chuyên ngành đào tạo</label>
                                            <input type="text" name="chuyen_nganh_dao_tao" class="form-control" maxlength="200" placeholder="VD: Công nghệ thông tin">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="mb-3">
                                            <label class="form-label">Trường đào tạo</label>
                                            <input type="text" name="truong_dao_tao" class="form-control" maxlength="200" placeholder="VD: Đại học Bách Khoa">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Năm tốt nghiệp</label>
                                            <input type="number" name="nam_tot_nghiep" class="form-control" min="1970" max="2030">
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Chứng chỉ sư phạm</label>
                                    <input type="text" name="chung_chi_su_pham" class="form-control" maxlength="200" placeholder="VD: Chứng chỉ bồi dưỡng nghiệp vụ sư phạm">
                                </div>
                            </div>

                            <!-- TAB 3: Liên hệ & Ngân hàng -->
                            <div class="tab-pane fade" id="tab3">
                                <div class="mb-3">
                                    <label class="form-label">Địa chỉ thường trú</label>
                                    <textarea name="dia_chi" class="form-control" rows="2"></textarea>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Địa chỉ tạm trú</label>
                                    <textarea name="dia_chi_tam_tru" class="form-control" rows="2"></textarea>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Số điện thoại</label>
                                            <input type="text" name="so_dien_thoai" class="form-control" maxlength="20">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Email</label>
                                            <input type="email" name="email" class="form-control" maxlength="100">
                                        </div>
                                    </div>
                                </div>

                                <hr>
                                <h6>Thông tin ngân hàng</h6>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Số tài khoản</label>
                                            <input type="text" name="so_tai_khoan" class="form-control" maxlength="50">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Chủ tài khoản</label>
                                            <input type="text" name="chu_tai_khoan" class="form-control" maxlength="100">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Tên ngân hàng</label>
                                            <input type="text" name="ten_ngan_hang" class="form-control" maxlength="100" placeholder="VD: Vietcombank">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Chi nhánh</label>
                                            <input type="text" name="chi_nhanh_ngan_hang" class="form-control" maxlength="200">
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Mã số thuế</label>
                                    <input type="text" name="ma_so_thue" class="form-control" maxlength="20">
                                </div>
                            </div>

                            <!-- TAB 4: File đính kèm -->
                            <div class="tab-pane fade" id="tab4">
                                <div class="mb-3">
                                    <label class="form-label">File CCCD/CMND (PDF, JPG, PNG - tối đa 5MB)</label>
                                    <input type="file" name="file_cccd" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">File Bằng cấp (PDF, JPG, PNG - tối đa 5MB)</label>
                                    <input type="file" name="file_bang_cap" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">File Chứng chỉ (PDF, JPG, PNG - tối đa 5MB)</label>
                                    <input type="file" name="file_chung_chi" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Ghi chú</label>
                                    <textarea name="ghi_chu" class="form-control" rows="3"></textarea>
                                </div>

                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" checked>
                                        <label class="form-check-label" for="is_active"><strong>Kích hoạt giảng viên</strong></label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>
                        <div class="d-flex justify-content-between">
                            <a href="/admin/giang-vien" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Quay lại</a>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Lưu giảng viên</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>