<?php
/**
 * View: Reports Index
 * File: views/reports/index.php
 */

if (!isLoggedIn()) { redirect('/login'); exit; }
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container-fluid px-4">
    <h1 class="mt-4"><?= $pageTitle ?></h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item active">Báo cáo</li>
    </ol>

    <!-- Báo cáo Hợp đồng -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <i class="fas fa-file-contract me-1"></i> Báo cáo Hợp đồng
        </div>
        <div class="card-body">
            <form method="POST" action="/reports/bao-cao-hop-dong" target="_blank">
                <div class="row">
                    <?php if (!empty($khoa_list)): ?>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Khoa</label>
                                <select name="khoa_id" class="form-select">
                                    <option value="">-- Tất cả --</option>
                                    <?php foreach ($khoa_list as $k): ?>
                                        <option value="<?= $k['khoa_id'] ?>"><?= htmlspecialchars($k['ten_khoa']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Giảng viên</label>
                            <select name="giang_vien_id" class="form-select">
                                <option value="">-- Tất cả --</option>
                                <?php foreach ($giang_vien_list as $gv): ?>
                                    <option value="<?= $gv['giang_vien_id'] ?>"><?= htmlspecialchars($gv['ten_giang_vien']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Trạng thái</label>
                            <select name="trang_thai" class="form-select">
                                <option value="">-- Tất cả --</option>
                                <option value="Mới tạo">Mới tạo</option>
                                <option value="Đã duyệt">Đã duyệt</option>
                                <option value="Đang thực hiện">Đang thực hiện</option>
                                <option value="Hoàn thành">Hoàn thành</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Từ ngày</label>
                            <input type="date" name="tu_ngay" class="form-control">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Đến ngày</label>
                            <input type="date" name="den_ngay" class="form-control" value="<?= date('Y-m-d') ?>">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Định dạng</label>
                            <select name="format" class="form-select">
                                <option value="excel">Excel (.xlsx)</option>
                                <option value="pdf">PDF</option>
                            </select>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-download"></i> Xuất báo cáo
                </button>
            </form>
        </div>
    </div>

    <!-- Báo cáo Giảng viên -->
    <div class="card mb-4">
        <div class="card-header bg-success text-white">
            <i class="fas fa-users me-1"></i> Báo cáo Giảng viên
        </div>
        <div class="card-body">
            <form method="POST" action="/reports/bao-cao-giang-vien" target="_blank">
                <div class="row">
                    <?php if (!empty($khoa_list)): ?>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Khoa</label>
                                <select name="khoa_id" class="form-select">
                                    <option value="">-- Tất cả --</option>
                                    <?php foreach ($khoa_list as $k): ?>
                                        <option value="<?= $k['khoa_id'] ?>"><?= htmlspecialchars($k['ten_khoa']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Trạng thái</label>
                            <select name="is_active" class="form-select">
                                <option value="">-- Tất cả --</option>
                                <option value="1" selected>Hoạt động</option>
                                <option value="0">Ngừng</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Định dạng</label>
                            <select name="format" class="form-select">
                                <option value="excel">Excel (.xlsx)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-success">
                    <i class="fas fa-download"></i> Xuất báo cáo
                </button>
            </form>
        </div>
    </div>

    <!-- Báo cáo theo Khoa -->
    <?php if (in_array(getUserRole(), ['Admin', 'Phong_Dao_Tao'])): ?>
        <div class="card mb-4">
            <div class="card-header bg-warning text-white">
                <i class="fas fa-chart-bar me-1"></i> Báo cáo tổng hợp theo Khoa
            </div>
            <div class="card-body">
                <form method="POST" action="/reports/bao-cao-theo-khoa" target="_blank">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Tháng</label>
                                <select name="thang" class="form-select">
                                    <?php for ($m = 1; $m <= 12; $m++): ?>
                                        <option value="<?= $m ?>" <?= ($m == date('m')) ? 'selected' : '' ?>>Tháng <?= $m ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Năm</label>
                                <select name="nam" class="form-select">
                                    <?php foreach ($nam_hoc_list as $y): ?>
                                        <option value="<?= $y ?>" <?= ($y == date('Y')) ? 'selected' : '' ?>><?= $y ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Định dạng</label>
                                <select name="format" class="form-select">
                                    <option value="excel">Excel (.xlsx)</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-download"></i> Xuất báo cáo
                    </button>
                </form>
            </div>
        </div>
    <?php endif; ?>

    <!-- Quick Stats -->
    <div class="row">
        <div class="col-md-4">
            <div class="card border-primary mb-3">
                <div class="card-header bg-primary text-white">
                    <i class="fas fa-info-circle"></i> Hướng dẫn
                </div>
                <div class="card-body">
                    <ul class="mb-0">
                        <li>Chọn bộ lọc phù hợp</li>
                        <li>Chọn định dạng xuất (Excel/PDF)</li>
                        <li>Click "Xuất báo cáo"</li>
                        <li>File sẽ tự động download</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-success mb-3">
                <div class="card-header bg-success text-white">
                    <i class="fas fa-check-circle"></i> Excel
                </div>
                <div class="card-body">
                    <ul class="mb-0">
                        <li>Định dạng chuẩn .xlsx</li>
                        <li>Có thể chỉnh sửa</li>
                        <li>Tính toán tự động</li>
                        <li>Mở bằng Excel/LibreOffice</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-danger mb-3">
                <div class="card-header bg-danger text-white">
                    <i class="fas fa-file-pdf"></i> PDF
                </div>
                <div class="card-body">
                    <ul class="mb-0">
                        <li>Định dạng cố định</li>
                        <li>In ấn chính thức</li>
                        <li>Bảo mật cao</li>
                        <li>Mở mọi thiết bị</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>