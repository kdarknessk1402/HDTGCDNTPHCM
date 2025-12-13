<?php
/**
 * View: Dashboard Admin
 * File: views/dashboard/admin.php
 */

if (!isLoggedIn()) { redirect('/login'); exit; }
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Dashboard Admin</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Dashboard</li>
    </ol>

    <!-- Stats Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white mb-4">
                <div class="card-body">
                    <h4><?= number_format($stats['tong_giang_vien']) ?></h4>
                    <div>Giảng viên</div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="/admin/giang-vien">Xem chi tiết</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white mb-4">
                <div class="card-body">
                    <h4><?= number_format($stats['tong_hop_dong']) ?></h4>
                    <div>Hợp đồng</div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="/admin/hop-dong">Xem chi tiết</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card bg-warning text-white mb-4">
                <div class="card-body">
                    <h4><?= number_format($stats['tong_khoa']) ?></h4>
                    <div>Khoa</div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="/admin/khoa">Xem chi tiết</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card bg-danger text-white mb-4">
                <div class="card-body">
                    <h4><?= number_format($stats['tong_lop']) ?></h4>
                    <div>Lớp học</div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="/admin/lop">Xem chi tiết</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Hợp đồng theo trạng thái -->
    <div class="row">
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header"><i class="fas fa-chart-pie me-1"></i> Hợp đồng theo trạng thái</div>
                <div class="card-body">
                    <canvas id="chartTrangThai" width="100%" height="50"></canvas>
                </div>
            </div>
        </div>

        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header"><i class="fas fa-chart-bar me-1"></i> Hợp đồng 6 tháng gần nhất</div>
                <div class="card-body">
                    <canvas id="chartMonth" width="100%" height="50"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Giảng viên -->
    <div class="card mb-4">
        <div class="card-header"><i class="fas fa-award me-1"></i> Top 5 Giảng viên có nhiều hợp đồng nhất</div>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th width="5%">Top</th>
                        <th width="15%">Mã GV</th>
                        <th width="25%">Tên giảng viên</th>
                        <th width="20%">Khoa</th>
                        <th width="10%">Số HĐ</th>
                        <th width="10%">Tổng giờ</th>
                        <th width="15%">Tổng tiền</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($top_giang_vien as $index => $gv): ?>
                        <tr>
                            <td class="text-center">
                                <?php
                                $badges = ['🥇', '🥈', '🥉', '4', '5'];
                                echo $badges[$index] ?? ($index + 1);
                                ?>
                            </td>
                            <td><?= htmlspecialchars($gv['ma_giang_vien']) ?></td>
                            <td><?= htmlspecialchars($gv['ten_giang_vien']) ?></td>
                            <td><?= htmlspecialchars($gv['ten_khoa']) ?></td>
                            <td class="text-center"><strong><?= $gv['so_hop_dong'] ?></strong></td>
                            <td class="text-center"><?= $gv['tong_gio'] ?></td>
                            <td class="text-end"><?= number_format($gv['tong_tien'], 0, ',', '.') ?> đ</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// Chart Trạng thái
new Chart(document.getElementById('chartTrangThai'), {
    type: 'pie',
    data: {
        labels: ['Mới tạo', 'Đã duyệt', 'Đang thực hiện', 'Hoàn thành'],
        datasets: [{
            data: [
                <?= $stats['hd_moi_tao'] ?>,
                <?= $stats['hd_da_duyet'] ?>,
                <?= $stats['hd_dang_thuc_hien'] ?>,
                <?= $stats['hd_hoan_thanh'] ?>
            ],
            backgroundColor: ['#6c757d', '#0dcaf0', '#0d6efd', '#198754']
        }]
    }
});

// Chart Tháng
new Chart(document.getElementById('chartMonth'), {
    type: 'bar',
    data: {
        labels: [<?php echo implode(',', array_map(function($d) { return "'" . $d['thang'] . "'"; }, $chart_data)); ?>],
        datasets: [{
            label: 'Số hợp đồng',
            data: [<?php echo implode(',', array_column($chart_data, 'so_luong')); ?>],
            backgroundColor: '#0d6efd'
        }]
    },
    options: {
        scales: { y: { beginAtZero: true } }
    }
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>