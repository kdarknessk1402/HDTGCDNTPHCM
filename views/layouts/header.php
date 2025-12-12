<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Hệ thống Quản lý Hợp đồng Thỉnh giảng'; ?></title>
    
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link href="<?php echo BASE_URL; ?>/public/css/style.css" rel="stylesheet">
    
    <!-- DataTables CSS (nếu cần) -->
    <?php if (isset($useDataTables) && $useDataTables): ?>
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <?php endif; ?>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?php echo BASE_URL; ?>">
                <i class="bi bi-file-earmark-text"></i>
                Hợp đồng Thỉnh giảng
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <?php if (isLoggedIn()): ?>
                        <?php $currentUser = getCurrentUser(); ?>
                        <?php $roleName = $currentUser['role_name'] ?? ''; ?>
                        
                        <!-- Dashboard cho tất cả -->
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($_SERVER['REQUEST_URI'] == BASE_URL . '/' || $_SERVER['REQUEST_URI'] == BASE_URL . '/index.php') ? 'active' : ''; ?>" 
                               href="<?php echo BASE_URL; ?>">
                                <i class="bi bi-speedometer2"></i> Dashboard
                            </a>
                        </li>
                        
                        <!-- Menu cho Admin -->
                        <?php if ($roleName === 'Admin'): ?>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-folder"></i> Danh mục
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/danh-muc/khoa.php">
                                        <i class="bi bi-building"></i> Khoa
                                    </a></li>
                                    <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/danh-muc/nghe.php">
                                        <i class="bi bi-briefcase"></i> Nghề
                                    </a></li>
                                    <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/danh-muc/lop.php">
                                        <i class="bi bi-people"></i> Lớp
                                    </a></li>
                                    <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/danh-muc/mon-hoc.php">
                                        <i class="bi bi-book"></i> Môn học
                                    </a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/danh-muc/co-so.php">
                                        <i class="bi bi-geo-alt"></i> Cơ sở
                                    </a></li>
                                    <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/danh-muc/don-gia.php">
                                        <i class="bi bi-cash-stack"></i> Đơn giá giờ dạy
                                    </a></li>
                                </ul>
                            </li>
                        <?php endif; ?>
                        
                        <!-- Menu cho Giáo vụ -->
                        <?php if ($roleName === 'Giao_Vu'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo BASE_URL; ?>/giang-vien/index.php">
                                    <i class="bi bi-person-badge"></i> Giảng viên
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo BASE_URL; ?>/hop-dong/index.php">
                                    <i class="bi bi-file-earmark-text"></i> Hợp đồng
                                </a>
                            </li>
                        <?php endif; ?>
                        
                        <!-- Báo cáo cho Admin, Phòng ĐT, Trưởng Khoa -->
                        <?php if (in_array($roleName, ['Admin', 'Phong_Dao_Tao', 'Truong_Khoa'])): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo BASE_URL; ?>/bao-cao/index.php">
                                    <i class="bi bi-file-earmark-bar-graph"></i> Báo cáo
                                </a>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>
                </ul>
                
                <!-- User menu -->
                <?php if (isLoggedIn()): ?>
                    <ul class="navbar-nav">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle"></i>
                                <?php echo htmlspecialchars($currentUser['full_name']); ?>
                                <span class="badge bg-info"><?php echo htmlspecialchars($currentUser['role_display_name']); ?></span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <span class="dropdown-item-text">
                                        <strong><?php echo htmlspecialchars($currentUser['full_name']); ?></strong><br>
                                        <small class="text-muted"><?php echo htmlspecialchars($currentUser['email'] ?? ''); ?></small>
                                    </span>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <?php if (isset($currentUser['ten_khoa'])): ?>
                                <li>
                                    <span class="dropdown-item-text">
                                        <i class="bi bi-building"></i> <?php echo htmlspecialchars($currentUser['ten_khoa']); ?>
                                    </span>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <?php endif; ?>
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/profile.php">
                                    <i class="bi bi-person"></i> Thông tin cá nhân
                                </a></li>
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/change-password.php">
                                    <i class="bi bi-key"></i> Đổi mật khẩu
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="<?php echo BASE_URL; ?>/logout.php">
                                    <i class="bi bi-box-arrow-right"></i> Đăng xuất
                                </a></li>
                            </ul>
                        </li>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    
    <!-- Main Content Container -->
    <div class="container-fluid mt-4">
        <?php 
        // Hiển thị thông báo flash message
        $flashMessage = getFlashMessage();
        if ($flashMessage): 
        ?>
        <div class="alert alert-<?php echo $flashMessage['type']; ?> alert-dismissible fade show" role="alert">
            <?php echo $flashMessage['message']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>