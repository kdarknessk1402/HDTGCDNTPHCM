<?php
/**
 * View: Dashboard Giáo vụ
 * File: views/dashboard/giao_vu.php
 */

if (!isLoggedIn()) { redirect('/login'); exit; }
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Dashboard Giáo vụ</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Dashboard</li>
    </ol>

    <!-- Stats -->
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white mb-4">
                <div class="card-body">
                    <h4><?= number_format($stats['hd_tao_boi_toi']) ?></h4>
                    <div>HĐ tôi đã tạo</div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white mb-4">
                <div class="card-body">
                    <h4><?= number_format($stats['hd_thang_nay']) ?></h4>
                    <div>HĐ tháng này</div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card bg-warning text-white mb-4">
                <div class="card-body">
                    <h4><?= number_format($stats['tong_giang_vien']) ?></h4>
                    <div>Tổng giảng viên</div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card bg-danger text-white mb-4">
                <div class="card-body">
                    <h4><?= number_format($stats['hd_moi_tao']) ?></h4>
                    <div>HĐ mới tạo</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <i class="fas fa-bolt me-1"></i> Quick Actions
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <a href="/admin/hop-dong/create" class="btn btn-success btn-lg w-100 mb-3">
                        <i class="fas fa-plus-circle"></i> Tạo hợp đồng mới
                    </a>
                </div>
                <div class="col-md-6">
                    <a href="/admin/giang-vien/create" class="btn btn-primary btn-lg w-100 mb-3">
                        <i class="fas fa-user-plus"></i> Thêm giảng viên
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Hợp đồng của tôi -->
    <div class="card mb-4">
        <div class="card-header"><i class="fas fa-list me-1"></i> Hợp đồng tôi đã tạo (10 gần nhất)</div>
        <div class="card-body">
            <?php if (empty($hop_dong_cua_toi)): ?>
                <div class="alert alert-info"><i class="fas fa-info-circle"></i> Chưa có hợp đồng nào.</div>
            <?php else: ?>
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Số HĐ</th>
                            <th>Ngày</th>
                            <th>Giảng viên</th>
                            <th>Môn học</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($hop_dong_cua_toi as $hd): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($hd['so_hop_dong']) ?></strong></td>
                                <td><?= date('d/m/Y', strtotime($hd['ngay_hop_dong'])) ?></td>
                                <td><?= htmlspecialchars($hd['ten_giang_vien']) ?></td>
                                <td><small><?= htmlspecialchars($hd['ten_mon_hoc']) ?></small></td>
                                <td><?= number_format($hd['tong_tien'], 0, ',', '.') ?></td>
                                <td>
                                    <?php
                                    $badge = [
                                        'Mới tạo' => 'bg-secondary',
                                        'Đã duyệt' => 'bg-info',
                                        'Đang thực hiện' => 'bg-primary',
                                        'Hoàn thành' => 'bg-success',
                                        'Hủy' => 'bg-danger'
                                    ];
                                    ?>
                                    <span class="badge <?= $badge[$hd['trang_thai']] ?? 'bg-secondary' ?>">
                                        <?= $hd['trang_thai'] ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="/admin/hop-dong/edit/<?= $hd['hop_dong_id'] ?>" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

    <!-- Danh sách giảng viên -->
    <div class="card mb-4">
        <div class="card-header"><i class="fas fa-users me-1"></i> Danh sách giảng viên</div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm table-striped">
                    <thead>
                        <tr>
                            <th>Mã GV</th>
                            <th>Tên giảng viên</th>
                            <th>Điện thoại</th>
                            <th>Email</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($giang_vien_list as $gv): ?>
                            <tr>
                                <td><?= htmlspecialchars($gv['ma_giang_vien']) ?></td>
                                <td><?= htmlspecialchars($gv['ten_giang_vien']) ?></td>
                                <td><?= htmlspecialchars($gv['so_dien_thoai']) ?></td>
                                <td><small><?= htmlspecialchars($gv['email']) ?></small></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>