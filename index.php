<?php
/**
 * Dashboard - Trang chủ
 * File: /index.php
 */

session_start();
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/helpers/functions.php';

// Kiểm tra đăng nhập
if (!isLoggedIn()) {
    header('Location: ' . BASE_URL . '/login.php');
    exit;
}

$currentUser = getCurrentUser();
$pageTitle = 'Dashboard - Trang chủ';

// Include header
include __DIR__ . '/views/layouts/header.php';
?>

<div class="row">
    <div class="col-12">
        <h2><i class="bi bi-speedometer2"></i> Dashboard</h2>
        <p class="text-muted">Xin chào, <strong><?php echo htmlspecialchars($currentUser['full_name']); ?></strong> - <?php echo htmlspecialchars($currentUser['role_display_name']); ?></p>
        <hr>
    </div>
</div>

<div class="row">
    <!-- Thống kê -->
    <div class="col-md-3">
        <div class="card text-white bg-primary mb-3">
            <div class="card-body">
                <h5 class="card-title"><i class="bi bi-file-earmark-text"></i> Hợp đồng</h5>
                <h2 class="mb-0">0</h2>
                <small>Tổng số hợp đồng</small>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card text-white bg-success mb-3">
            <div class="card-body">
                <h5 class="card-title"><i class="bi bi-person-badge"></i> Giảng viên</h5>
                <h2 class="mb-0">0</h2>
                <small>Giảng viên thỉnh giảng</small>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card text-white bg-info mb-3">
            <div class="card-body">
                <h5 class="card-title"><i class="bi bi-clock-history"></i> Giờ dạy</h5>
                <h2 class="mb-0">0</h2>
                <small>Tổng giờ dạy (3 tháng)</small>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card text-white bg-warning mb-3">
            <div class="card-body">
                <h5 class="card-title"><i class="bi bi-cash-stack"></i> Chi phí</h5>
                <h2 class="mb-0">0đ</h2>
                <small>Tổng chi phí (3 tháng)</small>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-list-check"></i> Hợp đồng gần đây</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> Chưa có dữ liệu hợp đồng
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0"><i class="bi bi-info-circle"></i> Hướng dẫn sử dụng</h5>
            </div>
            <div class="card-body">
                <?php if ($currentUser['role_name'] === 'Admin'): ?>
                    <h6><i class="bi bi-star-fill text-warning"></i> Chức năng Admin:</h6>
                    <ul>
                        <li>Quản lý Danh mục: Khoa, Nghề, Lớp, Môn học, Cơ sở</li>
                        <li>Quản lý Đơn giá giờ dạy</li>
                        <li>Import dữ liệu từ Excel</li>
                        <li>Xem Dashboard tổng hợp toàn trường</li>
                    </ul>
                <?php elseif ($currentUser['role_name'] === 'Phong_Dao_Tao'): ?>
                    <h6><i class="bi bi-briefcase text-info"></i> Chức năng Phòng Đào tạo:</h6>
                    <ul>
                        <li>Xem Dashboard tổng hợp toàn trường (3 tháng)</li>
                        <li>Lọc và tìm kiếm hợp đồng theo nhiều tiêu chí</li>
                        <li>Xuất báo cáo Excel</li>
                        <li>Duyệt hợp đồng</li>
                    </ul>
                <?php elseif ($currentUser['role_name'] === 'Truong_Khoa'): ?>
                    <h6><i class="bi bi-building text-success"></i> Chức năng Trưởng Khoa:</h6>
                    <ul>
                        <li>Xem Dashboard của khoa <?php echo htmlspecialchars($currentUser['ten_khoa'] ?? ''); ?></li>
                        <li>Lọc và tìm kiếm hợp đồng trong khoa</li>
                        <li>Xuất báo cáo khoa</li>
                    </ul>
                <?php elseif ($currentUser['role_name'] === 'Giao_Vu'): ?>
                    <h6><i class="bi bi-person-workspace text-primary"></i> Chức năng Giáo vụ:</h6>
                    <ul>
                        <li>Quản lý Giảng viên của khoa <?php echo htmlspecialchars($currentUser['ten_khoa'] ?? ''); ?></li>
                        <li>Import giảng viên từ Excel</li>
                        <li>Tạo hợp đồng thỉnh giảng mới</li>
                        <li>In hợp đồng theo mẫu Word</li>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
// Include footer
include __DIR__ . '/views/layouts/footer.php';
?>