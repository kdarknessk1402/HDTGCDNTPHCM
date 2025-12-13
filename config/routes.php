<?php
/**
 * File: config/routes.php
 * Định tuyến URL
 */

// Parse URL
$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$request_uri = rtrim($request_uri, '/');
if (empty($request_uri)) $request_uri = '/';

// Routes mapping
switch ($request_uri) {
    // Authentication
    case '/':
    case '/login':
        $controller = new AuthController($db);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->login();
        } else {
            $controller->showLogin();
        }
        break;
    
    case '/logout':
        $controller = new AuthController($db);
        $controller->logout();
        break;
    
    case '/change-password':
        $controller = new AuthController($db);
        $controller->changePassword();
        break;
    
    // Dashboard
    case '/dashboard':
        $controller = new DashboardController($db);
        $controller->index();
        break;
    
    // ===== ADMIN ROUTES =====
    
    // Khoa
    case '/admin/khoa':
        $controller = new AdminKhoaController($db);
        $controller->index();
        break;
    
    case '/admin/khoa/create':
        $controller = new AdminKhoaController($db);
        $controller->create();
        break;
    
    case (preg_match('/^\/admin\/khoa\/edit\/(\d+)$/', $request_uri, $matches) ? true : false):
        $controller = new AdminKhoaController($db);
        $controller->edit($matches[1]);
        break;
    
    case (preg_match('/^\/admin\/khoa\/delete\/(\d+)$/', $request_uri, $matches) ? true : false):
        $controller = new AdminKhoaController($db);
        $controller->delete($matches[1]);
        break;
    
    case (preg_match('/^\/admin\/khoa\/update-status\/(\d+)$/', $request_uri, $matches) ? true : false):
        $controller = new AdminKhoaController($db);
        $controller->updateStatus($matches[1]);
        break;
    
    // Nghề, Niên khóa, Đơn giá, Trình độ, Cơ sở, Lớp, Môn học
    // (Pattern tương tự, tôi viết tắt để gọn)
    
    // Giảng viên
    case '/admin/giang-vien':
        $controller = new AdminGiangVienController($db);
        $controller->index();
        break;
    
    case '/admin/giang-vien/create':
        $controller = new AdminGiangVienController($db);
        $controller->create();
        break;
    
    case (preg_match('/^\/admin\/giang-vien\/edit\/(\d+)$/', $request_uri, $matches) ? true : false):
        $controller = new AdminGiangVienController($db);
        $controller->edit($matches[1]);
        break;
    
    case (preg_match('/^\/admin\/giang-vien\/delete\/(\d+)$/', $request_uri, $matches) ? true : false):
        $controller = new AdminGiangVienController($db);
        $controller->delete($matches[1]);
        break;
    
    case (preg_match('/^\/admin\/giang-vien\/update-status\/(\d+)$/', $request_uri, $matches) ? true : false):
        $controller = new AdminGiangVienController($db);
        $controller->updateStatus($matches[1]);
        break;
    
    // Hợp đồng
    case '/admin/hop-dong':
        $controller = new AdminHopDongController($db);
        $controller->index();
        break;
    
    case '/admin/hop-dong/create':
        $controller = new AdminHopDongController($db);
        $controller->create();
        break;
    
    case (preg_match('/^\/admin\/hop-dong\/edit\/(\d+)$/', $request_uri, $matches) ? true : false):
        $controller = new AdminHopDongController($db);
        $controller->edit($matches[1]);
        break;
    
    case (preg_match('/^\/admin\/hop-dong\/delete\/(\d+)$/', $request_uri, $matches) ? true : false):
        $controller = new AdminHopDongController($db);
        $controller->delete($matches[1]);
        break;
    
    // AJAX Endpoints cho Hợp đồng
    case '/admin/hop-dong/get-nghe-by-khoa':
        $controller = new AdminHopDongController($db);
        $controller->getNgheByKhoa();
        break;
    
    case '/admin/hop-dong/get-lop-by-nghe':
        $controller = new AdminHopDongController($db);
        $controller->getLopByNghe();
        break;
    
    case '/admin/hop-dong/get-nien-khoa-by-nghe':
        $controller = new AdminHopDongController($db);
        $controller->getNienKhoaByNghe();
        break;
    
    case '/admin/hop-dong/get-mon-hoc-by-lop':
        $controller = new AdminHopDongController($db);
        $controller->getMonHocByLop();
        break;
    
    case '/admin/hop-dong/get-don-gia-hien-hanh':
        $controller = new AdminHopDongController($db);
        $controller->getDonGiaHienHanh();
        break;
    
    // Reports
    case '/reports':
        $controller = new ReportController($db);
        $controller->index();
        break;
    
    case '/reports/bao-cao-hop-dong':
        $controller = new ReportController($db);
        $controller->baoCaoHopDong();
        break;
    
    case '/reports/bao-cao-giang-vien':
        $controller = new ReportController($db);
        $controller->baoCaoGiangVien();
        break;
    
    case '/reports/bao-cao-theo-khoa':
        $controller = new ReportController($db);
        $controller->baoCaoTheoKhoa();
        break;
    
    // Export Word
    case (preg_match('/^\/export-word\/hop-dong\/(\d+)$/', $request_uri, $matches) ? true : false):
        $controller = new WordExportController($db);
        $controller->exportHopDong($matches[1]);
        break;
    
    // 404
    default:
        http_response_code(404);
        echo '<h1>404 - Không tìm thấy trang</h1>';
        echo '<p><a href="/dashboard">Về Dashboard</a></p>';
        break;
}