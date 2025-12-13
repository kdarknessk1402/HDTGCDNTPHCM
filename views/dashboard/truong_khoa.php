<?php
/**
 * View: Dashboard Trưởng Khoa
 * File: views/dashboard/truong_khoa.php
 */

if (!isLoggedIn()) { redirect('/login'); exit; }
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Dashboard Trưởng Khoa</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Dashboard - <?= htmlspecialchars($user['ten_khoa']) ?></li>
    </ol>

    <!-- Stats khoa -->
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white mb-4">
                <div class="card-body">
                    <h4><?= number_format($stats['tong_giang_vien']) ?></h4>
                    <div>Giảng viên khoa</div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white mb-4">
                <div class="card-body">
                    <h4><?= number_format($stats['tong_nghe']) ?></h4>
                    <div>Nghề đào tạo</div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card bg-warning text-white mb-4">
                <div class="card-body">
                    <h4><?= number_format($stats['tong_lop']) ?></h4>
                    <div>Lớp học</div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card bg-info text-white mb-4">
                <div class="card-body">
                    <h4><?= number_format($stats['tong_hop_dong_thang']) ?></h4>
                    <div>HĐ tháng này</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Giảng viên khoa -->
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header"><i class="fas fa-users me-1"></i> Giảng viên khoa (<?= count($giang_vien_khoa) ?>)</div>
                <div class="card-body">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Mã GV</th>
                                <th>Tên giảng viên</th>
                                <th>Điện thoại</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($giang_vien_khoa as $gv): ?>
                                <tr>
                                    <td><?= htmlspecialchars($gv['ma_giang_vien']) ?></td>
                                    <td><?= htmlspecialchars($gv['ten_giang_vien']) ?></td>
                                    <td><?= htmlspecialchars($gv['so_dien_thoai']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Hợp đồng gần đây -->
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header"><i class="fas fa-file-contract me-1"></i> Hợp đồng gần đây</div>
                <div class="card-body">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Số HĐ</th>
                                <th>Giảng viên</th>
                                <th>Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($hop_dong_gan_day as $hd): ?>
                                <tr>
                                    <td><?= htmlspecialchars($hd['so_hop_dong']) ?></td>
                                    <td><small><?= htmlspecialchars($hd['ten_giang_vien']) ?></small></td>
                                    <td><span class="badge bg-info"><?= $hd['trang_thai'] ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart -->
    <div class="card mb-4">
        <div class="card-header"><i class="fas fa-chart-area me-1"></i> Hợp đồng 6 tháng gần nhất</div>
        <div class="card-body">
            <canvas id="chartKhoa" width="100%" height="40"></canvas>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
new Chart(document.getElementById('chartKhoa'), {
    type: 'line',
    data: {
        labels: [<?php echo implode(',', array_map(function($d) { return "'" . $d['thang'] . "'"; }, $chart_data)); ?>],
        datasets: [{
            label: 'Số hợp đồng',
            data: [<?php echo implode(',', array_column($chart_data, 'so_luong')); ?>],
            borderColor: '#198754',
            backgroundColor: 'rgba(25, 135, 84, 0.1)',
            fill: true
        }]
    }
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>