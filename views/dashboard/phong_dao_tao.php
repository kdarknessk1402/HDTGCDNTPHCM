<?php
/**
 * View: Dashboard Phòng Đào tạo
 * File: views/dashboard/phong_dao_tao.php
 */

if (!isLoggedIn()) { redirect('/login'); exit; }
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Dashboard Phòng Đào tạo</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Dashboard</li>
    </ol>

    <!-- Stats tháng này -->
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white mb-4">
                <div class="card-body">
                    <h4><?= number_format($stats['tong_giang_vien']) ?></h4>
                    <div>Tổng giảng viên</div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white mb-4">
                <div class="card-body">
                    <h4><?= number_format($stats['tong_hop_dong_thang']) ?></h4>
                    <div>HĐ tháng này</div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card bg-warning text-white mb-4">
                <div class="card-body">
                    <h4><?= number_format($stats['tong_gio_thang']) ?></h4>
                    <div>Tổng giờ tháng này</div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card bg-info text-white mb-4">
                <div class="card-body">
                    <h4><?= number_format($stats['tong_tien_thang'], 0, ',', '.') ?></h4>
                    <div>Tổng tiền tháng này</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Hợp đồng chờ duyệt -->
    <div class="card mb-4">
        <div class="card-header bg-warning">
            <i class="fas fa-clock me-1"></i> Hợp đồng chờ duyệt (<?= count($hop_dong_cho_duyet) ?>)
        </div>
        <div class="card-body">
            <?php if (empty($hop_dong_cho_duyet)): ?>
                <div class="alert alert-success"><i class="fas fa-check"></i> Không có hợp đồng chờ duyệt.</div>
            <?php else: ?>
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Số HĐ</th>
                            <th>Ngày HĐ</th>
                            <th>Giảng viên</th>
                            <th>Môn học</th>
                            <th>Tổng tiền</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($hop_dong_cho_duyet as $hd): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($hd['so_hop_dong']) ?></strong></td>
                                <td><?= date('d/m/Y', strtotime($hd['ngay_hop_dong'])) ?></td>
                                <td><?= htmlspecialchars($hd['ten_giang_vien']) ?></td>
                                <td><?= htmlspecialchars($hd['ten_mon_hoc']) ?></td>
                                <td><?= number_format($hd['tong_tien'], 0, ',', '.') ?></td>
                                <td>
                                    <a href="/admin/hop-dong/edit/<?= $hd['hop_dong_id'] ?>" class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye"></i> Xem
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

    <!-- Thống kê theo khoa -->
    <div class="card mb-4">
        <div class="card-header"><i class="fas fa-chart-bar me-1"></i> Thống kê theo Khoa</div>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Khoa</th>
                        <th class="text-center">Số GV</th>
                        <th class="text-center">Số HĐ</th>
                        <th class="text-end">Tổng tiền</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($thong_ke_khoa as $k): ?>
                        <tr>
                            <td><?= htmlspecialchars($k['ten_khoa']) ?></td>
                            <td class="text-center"><?= $k['so_giang_vien'] ?></td>
                            <td class="text-center"><strong><?= $k['so_hop_dong'] ?></strong></td>
                            <td class="text-end"><?= number_format($k['tong_tien'], 0, ',', '.') ?> đ</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Chart 12 tháng -->
    <div class="card mb-4">
        <div class="card-header"><i class="fas fa-chart-line me-1"></i> Biểu đồ hợp đồng 12 tháng</div>
        <div class="card-body">
            <canvas id="chartYear" width="100%" height="40"></canvas>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
new Chart(document.getElementById('chartYear'), {
    type: 'line',
    data: {
        labels: [<?php echo implode(',', array_map(function($d) { return "'" . $d['thang'] . "'"; }, $chart_data)); ?>],
        datasets: [{
            label: 'Số hợp đồng',
            data: [<?php echo implode(',', array_column($chart_data, 'so_luong')); ?>],
            borderColor: '#0d6efd',
            tension: 0.1
        }]
    }
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>