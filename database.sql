-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th12 16, 2025 lúc 03:43 AM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `hdtg_db`
--

DELIMITER $$
--
-- Thủ tục
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_generate_so_hop_dong` (IN `p_nam` YEAR, OUT `p_so_hop_dong` VARCHAR(50))   BEGIN
    DECLARE v_counter INT;
    
    SELECT COALESCE(MAX(CAST(SUBSTRING_INDEX(so_hop_dong, '/', 1) AS UNSIGNED)), 0) + 1
    INTO v_counter
    FROM hop_dong
    WHERE nam_hop_dong = p_nam;
    
    SET p_so_hop_dong = CONCAT(LPAD(v_counter, 3, '0'), '/HĐ-CĐN');
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_get_don_gia` (IN `p_co_so_id` INT, IN `p_trinh_do_id` INT, IN `p_ngay_ap_dung` DATE, OUT `p_don_gia` DECIMAL(15,2))   BEGIN
    SELECT don_gia INTO p_don_gia
    FROM don_gia_gio_day
    WHERE co_so_id = p_co_so_id
      AND trinh_do_id = p_trinh_do_id
      AND ngay_ap_dung <= p_ngay_ap_dung
      AND (ngay_ket_thuc IS NULL OR ngay_ket_thuc >= p_ngay_ap_dung)
      AND is_active = 1
    ORDER BY ngay_ap_dung DESC
    LIMIT 1;
    
    IF p_don_gia IS NULL THEN
        SET p_don_gia = 0;
    END IF;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `activity_logs`
--

CREATE TABLE `activity_logs` (
  `log_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL COMMENT 'ID user thực hiện',
  `action` varchar(100) NOT NULL COMMENT 'Hành động',
  `table_name` varchar(50) DEFAULT NULL COMMENT 'Tên bảng',
  `record_id` int(11) DEFAULT NULL COMMENT 'ID bản ghi',
  `description` text DEFAULT NULL COMMENT 'Mô tả chi tiết',
  `ip_address` varchar(45) DEFAULT NULL COMMENT 'IP address',
  `user_agent` varchar(255) DEFAULT NULL COMMENT 'User agent',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng ghi log hoạt động';

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cap_do_giang_day`
--

CREATE TABLE `cap_do_giang_day` (
  `cap_do_id` int(11) NOT NULL,
  `ma_cap_do` varchar(20) NOT NULL COMMENT 'Mã cấp độ',
  `ten_cap_do` varchar(50) NOT NULL COMMENT 'Tên cấp độ',
  `mo_ta` text DEFAULT NULL COMMENT 'Mô tả',
  `thu_tu` int(11) DEFAULT 0 COMMENT 'Thứ tự hiển thị',
  `is_active` tinyint(1) DEFAULT 1 COMMENT '1=Hoạt động, 0=Ngừng',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng Cấp độ giảng dạy';

--
-- Đang đổ dữ liệu cho bảng `cap_do_giang_day`
--

INSERT INTO `cap_do_giang_day` (`cap_do_id`, `ma_cap_do`, `ten_cap_do`, `mo_ta`, `thu_tu`, `is_active`, `created_at`) VALUES
(1, 'CD', 'Cao đẳng', 'Trình độ Cao đẳng', 1, 1, '2025-12-11 02:40:19'),
(2, 'TC', 'Trung cấp', 'Trình độ Trung cấp', 2, 1, '2025-12-11 02:40:19');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `co_so`
--

CREATE TABLE `co_so` (
  `co_so_id` int(11) NOT NULL,
  `ma_co_so` varchar(20) NOT NULL COMMENT 'Mã cơ sở',
  `ten_co_so` varchar(100) NOT NULL COMMENT 'Tên cơ sở',
  `dia_chi` text DEFAULT NULL COMMENT 'Địa chỉ',
  `so_dien_thoai` varchar(20) DEFAULT NULL COMMENT 'Số điện thoại',
  `email` varchar(100) DEFAULT NULL COMMENT 'Email',
  `nguoi_phu_trach` varchar(100) DEFAULT NULL COMMENT 'Người phụ trách',
  `thu_tu` int(11) DEFAULT 0 COMMENT 'Thứ tự hiển thị',
  `is_active` tinyint(1) DEFAULT 1 COMMENT '1=Hoạt động, 0=Ngừng',
  `created_by` int(11) DEFAULT NULL COMMENT 'Người tạo',
  `updated_by` int(11) DEFAULT NULL COMMENT 'Người cập nhật',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng Cơ sở';

--
-- Đang đổ dữ liệu cho bảng `co_so`
--

INSERT INTO `co_so` (`co_so_id`, `ma_co_so`, `ten_co_so`, `dia_chi`, `so_dien_thoai`, `email`, `nguoi_phu_trach`, `thu_tu`, `is_active`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 'CS1', 'Cơ sở 1 - Quận 3', '280/6 Võ Văn Tần, P.5, Q.3, TP.HCM', '028-3930-3838', 'coso1@tphcm.edu.vn', 'Nguyễn Văn A', 1, 1, NULL, NULL, '2025-12-12 06:29:25', '2025-12-12 06:29:25'),
(2, 'CS2', 'Cơ sở 2 - Quận 12', '456 Tô Ký, P.Trung Mỹ Tây, Q.12, TP.HCM', '028-3730-4949', 'coso2@tphcm.edu.vn', 'Trần Thị B', 2, 1, NULL, NULL, '2025-12-12 06:29:25', '2025-12-12 06:29:25'),
(3, 'CS3', 'Cơ sở 3 - Thủ Đức', '123 Phạm Văn Đồng, P.Hiệp Bình Chánh, TP.Thủ Đức, TP.HCM', '028-3897-5656', 'coso3@tphcm.edu.vn', 'Lê Văn C', 3, 1, NULL, NULL, '2025-12-12 06:29:25', '2025-12-12 06:29:25');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `don_gia_gio_day`
--

CREATE TABLE `don_gia_gio_day` (
  `don_gia_id` int(11) NOT NULL,
  `co_so_id` int(11) NOT NULL COMMENT 'ID cơ sở',
  `trinh_do_id` int(11) NOT NULL COMMENT 'ID trình độ',
  `don_gia` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Đơn giá (VNĐ)',
  `ngay_ap_dung` date NOT NULL COMMENT 'Ngày bắt đầu áp dụng',
  `ngay_ket_thuc` date DEFAULT NULL COMMENT 'Ngày kết thúc (NULL = vô thời hạn)',
  `ghi_chu` text DEFAULT NULL COMMENT 'Ghi chú',
  `is_active` tinyint(1) DEFAULT 1 COMMENT '1=Đang áp dụng, 0=Ngừng',
  `created_by` int(11) DEFAULT NULL COMMENT 'Người tạo',
  `updated_by` int(11) DEFAULT NULL COMMENT 'Người cập nhật',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng Đơn giá giờ dạy';

--
-- Đang đổ dữ liệu cho bảng `don_gia_gio_day`
--

INSERT INTO `don_gia_gio_day` (`don_gia_id`, `co_so_id`, `trinh_do_id`, `don_gia`, `ngay_ap_dung`, `ngay_ket_thuc`, `ghi_chu`, `is_active`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 250000.00, '2023-01-01', NULL, 'Đơn giá cho giảng viên Tiến sĩ tại Cơ sở 1', 1, NULL, NULL, '2025-12-12 06:57:32', '2025-12-12 06:57:32'),
(2, 1, 2, 200000.00, '2023-01-01', NULL, 'Đơn giá cho giảng viên Thạc sĩ tại Cơ sở 1', 1, NULL, NULL, '2025-12-12 06:57:32', '2025-12-12 06:57:32'),
(3, 1, 3, 150000.00, '2023-01-01', NULL, 'Đơn giá cho giảng viên Đại học tại Cơ sở 1', 1, NULL, NULL, '2025-12-12 06:57:32', '2025-12-12 06:57:32'),
(4, 2, 1, 230000.00, '2023-01-01', NULL, 'Đơn giá cho giảng viên Tiến sĩ tại Cơ sở 2', 1, NULL, NULL, '2025-12-12 06:57:32', '2025-12-12 06:57:32'),
(5, 2, 2, 180000.00, '2023-01-01', NULL, 'Đơn giá cho giảng viên Thạc sĩ tại Cơ sở 2', 1, NULL, NULL, '2025-12-12 06:57:32', '2025-12-12 06:57:32'),
(6, 2, 3, 140000.00, '2023-01-01', NULL, 'Đơn giá cho giảng viên Đại học tại Cơ sở 2', 1, NULL, NULL, '2025-12-12 06:57:32', '2025-12-12 06:57:32'),
(7, 3, 1, 240000.00, '2023-01-01', NULL, 'Đơn giá cho giảng viên Tiến sĩ tại Cơ sở 3', 1, NULL, NULL, '2025-12-12 06:57:32', '2025-12-12 06:57:32'),
(8, 3, 2, 190000.00, '2023-01-01', NULL, 'Đơn giá cho giảng viên Thạc sĩ tại Cơ sở 3', 1, NULL, NULL, '2025-12-12 06:57:32', '2025-12-12 06:57:32'),
(9, 3, 3, 145000.00, '2023-01-01', NULL, 'Đơn giá cho giảng viên Đại học tại Cơ sở 3', 1, NULL, NULL, '2025-12-12 06:57:32', '2025-12-12 06:57:32');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `giang_vien`
--

CREATE TABLE `giang_vien` (
  `giang_vien_id` int(11) NOT NULL,
  `khoa_id` int(11) NOT NULL COMMENT 'ID khoa quản lý',
  `ma_giang_vien` varchar(20) DEFAULT NULL COMMENT 'Mã giảng viên (tự động hoặc nhập)',
  `ten_giang_vien` varchar(100) NOT NULL COMMENT 'Họ và tên',
  `nam_sinh` year(4) DEFAULT NULL COMMENT 'Năm sinh',
  `gioi_tinh` enum('Nam','Nữ','Khác') DEFAULT 'Nam' COMMENT 'Giới tính',
  `ngay_sinh` date DEFAULT NULL COMMENT 'Ngày sinh đầy đủ',
  `noi_sinh` varchar(200) DEFAULT NULL COMMENT 'Nơi sinh',
  `so_cccd` varchar(20) DEFAULT NULL COMMENT 'Số CCCD/CMND',
  `ngay_cap_cccd` date DEFAULT NULL COMMENT 'Ngày cấp CCCD',
  `noi_cap_cccd` varchar(200) DEFAULT 'Cục Cảnh sát Quản lý Hành chính về Trật tự xã hội' COMMENT 'Nơi cấp CCCD',
  `trinh_do_id` int(11) DEFAULT NULL COMMENT 'ID trình độ chuyên môn',
  `chuyen_nganh_dao_tao` varchar(200) DEFAULT NULL COMMENT 'Chuyên ngành đào tạo',
  `truong_dao_tao` varchar(200) DEFAULT NULL COMMENT 'Trường đào tạo',
  `nam_tot_nghiep` year(4) DEFAULT NULL COMMENT 'Năm tốt nghiệp',
  `chung_chi_su_pham` varchar(200) DEFAULT NULL COMMENT 'Chứng chỉ sư phạm',
  `dia_chi` text DEFAULT NULL COMMENT 'Địa chỉ thường trú',
  `dia_chi_tam_tru` text DEFAULT NULL COMMENT 'Địa chỉ tạm trú',
  `so_dien_thoai` varchar(20) DEFAULT NULL COMMENT 'Số điện thoại',
  `email` varchar(100) DEFAULT NULL COMMENT 'Email',
  `so_tai_khoan` varchar(50) DEFAULT NULL COMMENT 'Số tài khoản',
  `ten_ngan_hang` varchar(100) DEFAULT NULL COMMENT 'Tên ngân hàng',
  `chi_nhanh_ngan_hang` varchar(200) DEFAULT NULL COMMENT 'Chi nhánh ngân hàng',
  `chu_tai_khoan` varchar(100) DEFAULT NULL COMMENT 'Chủ tài khoản',
  `ma_so_thue` varchar(20) DEFAULT NULL COMMENT 'Mã số thuế cá nhân',
  `file_cccd` varchar(255) DEFAULT NULL COMMENT 'File scan CCCD',
  `file_bang_cap` varchar(255) DEFAULT NULL COMMENT 'File scan bằng cấp',
  `file_chung_chi` varchar(255) DEFAULT NULL COMMENT 'File scan chứng chỉ',
  `ghi_chu` text DEFAULT NULL COMMENT 'Ghi chú',
  `is_active` tinyint(1) DEFAULT 1 COMMENT '1=Đang hoạt động, 0=Ngừng',
  `created_by` int(11) DEFAULT NULL COMMENT 'Người tạo (user_id)',
  `updated_by` int(11) DEFAULT NULL COMMENT 'Người cập nhật',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng Giảng viên thỉnh giảng';

--
-- Đang đổ dữ liệu cho bảng `giang_vien`
--

INSERT INTO `giang_vien` (`giang_vien_id`, `khoa_id`, `ma_giang_vien`, `ten_giang_vien`, `nam_sinh`, `gioi_tinh`, `ngay_sinh`, `noi_sinh`, `so_cccd`, `ngay_cap_cccd`, `noi_cap_cccd`, `trinh_do_id`, `chuyen_nganh_dao_tao`, `truong_dao_tao`, `nam_tot_nghiep`, `chung_chi_su_pham`, `dia_chi`, `dia_chi_tam_tru`, `so_dien_thoai`, `email`, `so_tai_khoan`, `ten_ngan_hang`, `chi_nhanh_ngan_hang`, `chu_tai_khoan`, `ma_so_thue`, `file_cccd`, `file_bang_cap`, `file_chung_chi`, `ghi_chu`, `is_active`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 1, 'CNTTGV001', 'Nguyễn Văn An', '1985', 'Nam', '1985-05-15', NULL, '001085012345', '2020-01-15', 'Cục Cảnh sát QLHC về TTXH', 2, 'Công nghệ phần mềm', 'Đại học Bách Khoa TP.HCM', '2010', NULL, '123 Nguyễn Văn Linh, Q.7, TP.HCM', NULL, '0901111111', 'nva@email.com', '0011223344', 'Vietcombank', NULL, 'NGUYEN VAN AN', NULL, NULL, NULL, NULL, NULL, 1, 3, NULL, '2025-12-12 06:57:32', '2025-12-12 06:57:32'),
(2, 1, 'CNTTGV002', 'Trần Thị Bình', '1988', 'Nữ', '1988-08-20', NULL, '001088067890', '2020-02-10', 'Cục Cảnh sát QLHC về TTXH', 3, 'Hệ thống thông tin', 'Đại học Khoa học Tự nhiên', '2011', NULL, '456 Lê Văn Việt, Q.9, TP.HCM', NULL, '0902222222', 'ttb@email.com', '0022334455', 'Techcombank', NULL, 'TRAN THI BINH', NULL, NULL, NULL, NULL, NULL, 1, 3, NULL, '2025-12-12 06:57:32', '2025-12-12 06:57:32'),
(3, 2, 'CKGV001', 'Lê Văn Cường', '1983', 'Nam', '1983-12-10', NULL, '001083098765', '2020-03-05', 'Cục Cảnh sát QLHC về TTXH', 2, 'Cơ khí chế tạo máy', 'Đại học Bách Khoa TP.HCM', '2008', NULL, '789 Lê Hồng Phong, Q.10, TP.HCM', NULL, '0903333333', 'lvc@email.com', '0033445566', 'VPBank', NULL, 'LE VAN CUONG', NULL, NULL, NULL, NULL, NULL, 1, 4, NULL, '2025-12-12 06:57:32', '2025-12-12 06:57:32'),
(4, 2, 'CKGV002', 'Phạm Thị Dung', '1990', 'Nữ', '1990-03-25', NULL, '001090034567', '2020-04-20', 'Cục Cảnh sát QLHC về TTXH', 3, 'Công nghệ hàn', 'Đại học Sư phạm Kỹ thuật', '2013', NULL, '321 Cách Mạng Tháng 8, Q.3, TP.HCM', NULL, '0904444444', 'ptd@email.com', '0044556677', 'ACB', NULL, 'PHAM THI DUNG', NULL, NULL, NULL, NULL, NULL, 1, 4, NULL, '2025-12-12 06:57:32', '2025-12-12 06:57:32'),
(5, 3, 'DTGV001', 'Hoàng Văn Em', '1986', 'Nam', '1986-07-18', NULL, '001086045678', '2020-05-15', 'Cục Cảnh sát QLHC về TTXH', 1, 'Kỹ thuật điện tử', 'Đại học Bách Khoa Hà Nội', '2009', NULL, '654 Phan Văn Trị, Gò Vấp, TP.HCM', NULL, '0905555555', 'hve@email.com', '0055667788', 'Vietinbank', NULL, 'HOANG VAN EM', NULL, NULL, NULL, NULL, NULL, 1, 5, NULL, '2025-12-12 06:57:32', '2025-12-12 06:57:32'),
(6, 3, 'DTGV002', 'Đỗ Thị Phương', '1992', 'Nữ', '1992-11-05', NULL, '001092078901', '2020-06-10', 'Cục Cảnh sát QLHC về TTXH', 2, 'Điện tử viễn thông', 'Đại học Bưu chính Viễn thông', '2015', NULL, '987 Quang Trung, Gò Vấp, TP.HCM', NULL, '0906666666', 'dtp@email.com', '0066778899', 'BIDV', NULL, 'DO THI PHUONG', NULL, NULL, NULL, NULL, NULL, 1, 5, NULL, '2025-12-12 06:57:32', '2025-12-12 06:57:32');

--
-- Bẫy `giang_vien`
--
DELIMITER $$
CREATE TRIGGER `trg_giang_vien_before_insert` BEFORE INSERT ON `giang_vien` FOR EACH ROW BEGIN
    DECLARE v_counter INT;
    DECLARE v_ma_khoa VARCHAR(20);
    
    IF NEW.ma_giang_vien IS NULL OR NEW.ma_giang_vien = '' THEN
        SELECT ma_khoa INTO v_ma_khoa FROM khoa WHERE khoa_id = NEW.khoa_id;
        SELECT COUNT(*) + 1 INTO v_counter FROM giang_vien WHERE khoa_id = NEW.khoa_id;
        SET NEW.ma_giang_vien = CONCAT(v_ma_khoa, 'GV', LPAD(v_counter, 3, '0'));
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `hop_dong`
--

CREATE TABLE `hop_dong` (
  `hop_dong_id` int(11) NOT NULL,
  `so_hop_dong` varchar(50) NOT NULL COMMENT 'Số hợp đồng (XXX/HĐ-CĐN)',
  `nam_hop_dong` year(4) NOT NULL COMMENT 'Năm hợp đồng',
  `ngay_hop_dong` date NOT NULL COMMENT 'Ngày ký hợp đồng',
  `thang_hop_dong` int(11) NOT NULL COMMENT 'Tháng hợp đồng (1-12)',
  `giang_vien_id` int(11) NOT NULL COMMENT 'ID giảng viên',
  `mon_hoc_id` int(11) NOT NULL COMMENT 'ID môn học',
  `nghe_id` int(11) NOT NULL COMMENT 'ID nghề',
  `lop_id` int(11) NOT NULL COMMENT 'ID lớp',
  `nien_khoa_id` int(11) NOT NULL COMMENT 'ID niên khóa',
  `cap_do_id` int(11) NOT NULL COMMENT 'ID cấp độ giảng dạy',
  `co_so_id` int(11) NOT NULL COMMENT 'ID cơ sở',
  `ngay_bat_dau` date NOT NULL COMMENT 'Ngày bắt đầu giảng dạy',
  `ngay_ket_thuc` date NOT NULL COMMENT 'Ngày kết thúc giảng dạy',
  `tong_gio_mon_hoc` int(11) NOT NULL COMMENT 'Tổng số giờ giảng dạy',
  `don_gia_gio` decimal(15,2) NOT NULL COMMENT 'Đơn giá 1 giờ (VNĐ)',
  `tong_tien` decimal(15,2) NOT NULL COMMENT 'Tổng tiền = don_gia_gio * tong_gio_mon_hoc',
  `tong_tien_chu` varchar(500) DEFAULT NULL COMMENT 'Tổng tiền bằng chữ',
  `da_thanh_toan` decimal(15,2) DEFAULT 0.00 COMMENT 'Số tiền đã thanh toán',
  `ngay_thanh_toan` date DEFAULT NULL COMMENT 'Ngày thanh toán',
  `hinh_thuc_thanh_toan` enum('Tiền mặt','Chuyển khoản','Khác') DEFAULT 'Chuyển khoản',
  `trang_thai` enum('Mới tạo','Đã duyệt','Đang thực hiện','Hoàn thành','Hủy') DEFAULT 'Mới tạo' COMMENT 'Trạng thái hợp đồng',
  `file_hop_dong` varchar(255) DEFAULT NULL COMMENT 'File hợp đồng đã ký',
  `file_bien_ban_giao_nhan` varchar(255) DEFAULT NULL COMMENT 'File biên bản giao nhận',
  `ghi_chu` text DEFAULT NULL COMMENT 'Ghi chú',
  `ly_do_huy` text DEFAULT NULL COMMENT 'Lý do hủy (nếu có)',
  `created_by` int(11) DEFAULT NULL COMMENT 'Người tạo (user_id Giáo vụ)',
  `approved_by` int(11) DEFAULT NULL COMMENT 'Người duyệt (user_id Trưởng khoa/Phòng ĐT)',
  `approved_at` datetime DEFAULT NULL COMMENT 'Ngày duyệt',
  `updated_by` int(11) DEFAULT NULL COMMENT 'Người cập nhật',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng Hợp đồng thỉnh giảng';

--
-- Bẫy `hop_dong`
--
DELIMITER $$
CREATE TRIGGER `trg_hop_dong_before_insert` BEFORE INSERT ON `hop_dong` FOR EACH ROW BEGIN
    DECLARE v_so_hop_dong VARCHAR(50);
    
    IF NEW.so_hop_dong IS NULL OR NEW.so_hop_dong = '' THEN
        CALL sp_generate_so_hop_dong(NEW.nam_hop_dong, v_so_hop_dong);
        SET NEW.so_hop_dong = v_so_hop_dong;
    END IF;
    
    SET NEW.tong_tien = NEW.don_gia_gio * NEW.tong_gio_mon_hoc;
    SET NEW.thang_hop_dong = MONTH(NEW.ngay_hop_dong);
    SET NEW.nam_hop_dong = YEAR(NEW.ngay_hop_dong);
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_hop_dong_before_update` BEFORE UPDATE ON `hop_dong` FOR EACH ROW BEGIN
    IF NEW.don_gia_gio <> OLD.don_gia_gio OR NEW.tong_gio_mon_hoc <> OLD.tong_gio_mon_hoc THEN
        SET NEW.tong_tien = NEW.don_gia_gio * NEW.tong_gio_mon_hoc;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `import_logs`
--

CREATE TABLE `import_logs` (
  `import_id` int(11) NOT NULL,
  `loai_import` varchar(50) NOT NULL COMMENT 'Loại import (khoa, nghe, lop, mon_hoc, giang_vien)',
  `file_name` varchar(255) NOT NULL COMMENT 'Tên file import',
  `file_path` varchar(500) DEFAULT NULL COMMENT 'Đường dẫn file',
  `so_dong_thanh_cong` int(11) DEFAULT 0 COMMENT 'Số dòng import thành công',
  `so_dong_loi` int(11) DEFAULT 0 COMMENT 'Số dòng lỗi',
  `chi_tiet_loi` text DEFAULT NULL COMMENT 'Chi tiết lỗi (JSON)',
  `trang_thai` enum('Đang xử lý','Thành công','Có lỗi','Thất bại') DEFAULT 'Đang xử lý',
  `created_by` int(11) DEFAULT NULL COMMENT 'Người thực hiện import',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng log import dữ liệu';

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `khoa`
--

CREATE TABLE `khoa` (
  `khoa_id` int(11) NOT NULL,
  `ma_khoa` varchar(20) NOT NULL COMMENT 'Mã khoa',
  `ten_khoa` varchar(100) NOT NULL COMMENT 'Tên khoa',
  `mo_ta` text DEFAULT NULL COMMENT 'Mô tả',
  `truong_khoa_id` int(11) DEFAULT NULL COMMENT 'ID trưởng khoa',
  `so_dien_thoai` varchar(20) DEFAULT NULL COMMENT 'Số điện thoại khoa',
  `email` varchar(100) DEFAULT NULL COMMENT 'Email khoa',
  `thu_tu` int(11) DEFAULT 0 COMMENT 'Thứ tự hiển thị',
  `is_active` tinyint(1) DEFAULT 1 COMMENT '1=Hoạt động, 0=Ngừng',
  `created_by` int(11) DEFAULT NULL COMMENT 'Người tạo',
  `updated_by` int(11) DEFAULT NULL COMMENT 'Người cập nhật',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng Khoa';

--
-- Đang đổ dữ liệu cho bảng `khoa`
--

INSERT INTO `khoa` (`khoa_id`, `ma_khoa`, `ten_khoa`, `mo_ta`, `truong_khoa_id`, `so_dien_thoai`, `email`, `thu_tu`, `is_active`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 'CNTT', 'Khoa Công nghệ Thông tin', NULL, NULL, NULL, NULL, 1, 1, NULL, NULL, '2025-12-11 02:40:19', '2025-12-11 02:40:19'),
(2, 'KT', 'Khoa Kế toán', NULL, NULL, NULL, NULL, 2, 1, NULL, NULL, '2025-12-11 02:40:19', '2025-12-11 02:40:19'),
(3, 'CK', 'Khoa Cơ khí', NULL, NULL, NULL, NULL, 3, 1, NULL, NULL, '2025-12-11 02:40:19', '2025-12-11 02:40:19'),
(4, 'DL', 'Khoa Du lịch', NULL, NULL, NULL, NULL, 4, 1, NULL, NULL, '2025-12-11 02:40:19', '2025-12-11 02:40:19');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `lop`
--

CREATE TABLE `lop` (
  `lop_id` int(11) NOT NULL,
  `nghe_id` int(11) NOT NULL COMMENT 'ID nghề',
  `nien_khoa_id` int(11) DEFAULT NULL COMMENT 'ID niên khóa',
  `ma_lop` varchar(20) NOT NULL COMMENT 'Mã lớp',
  `ten_lop` varchar(100) DEFAULT NULL COMMENT 'Tên lớp',
  `si_so` int(11) DEFAULT 0 COMMENT 'Sĩ số',
  `giao_vien_chu_nhiem` varchar(100) DEFAULT NULL COMMENT 'Giáo viên chủ nhiệm',
  `thu_tu` int(11) DEFAULT 0 COMMENT 'Thứ tự hiển thị',
  `is_active` tinyint(1) DEFAULT 1 COMMENT '1=Hoạt động, 0=Ngừng',
  `created_by` int(11) DEFAULT NULL COMMENT 'Người tạo',
  `updated_by` int(11) DEFAULT NULL COMMENT 'Người cập nhật',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng Lớp';

--
-- Đang đổ dữ liệu cho bảng `lop`
--

INSERT INTO `lop` (`lop_id`, `nghe_id`, `nien_khoa_id`, `ma_lop`, `ten_lop`, `si_so`, `giao_vien_chu_nhiem`, `thu_tu`, `is_active`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'CNTT01_K2023_01', 'Lập trình K2023 - Lớp 1', 35, NULL, 1, 1, NULL, NULL, '2025-12-12 06:57:32', '2025-12-12 06:57:32'),
(2, 1, 1, 'CNTT01_K2023_02', 'Lập trình K2023 - Lớp 2', 32, NULL, 2, 1, NULL, NULL, '2025-12-12 06:57:32', '2025-12-12 06:57:32'),
(3, 2, 2, 'CNTT02_K2023_01', 'Quản trị mạng K2023 - Lớp 1', 30, NULL, 1, 1, NULL, NULL, '2025-12-12 06:57:32', '2025-12-12 06:57:32'),
(4, 3, 3, 'CNTT03_K2023_01', 'Thiết kế đồ họa K2023 - Lớp 1', 28, NULL, 1, 1, NULL, NULL, '2025-12-12 06:57:32', '2025-12-12 06:57:32'),
(5, 4, 4, 'CK01_K2023_01', 'Cơ khí chế tạo K2023 - Lớp 1', 30, NULL, 1, 1, NULL, NULL, '2025-12-12 06:57:32', '2025-12-12 06:57:32'),
(6, 4, 4, 'CK01_K2023_02', 'Cơ khí chế tạo K2023 - Lớp 2', 28, NULL, 2, 1, NULL, NULL, '2025-12-12 06:57:32', '2025-12-12 06:57:32'),
(7, 5, 5, 'CK02_K2023_01', 'Hàn K2023 - Lớp 1', 25, NULL, 1, 1, NULL, NULL, '2025-12-12 06:57:32', '2025-12-12 06:57:32'),
(8, 6, 6, 'CK03_K2023_01', 'Sửa chữa ô tô K2023 - Lớp 1', 30, NULL, 1, 1, NULL, NULL, '2025-12-12 06:57:32', '2025-12-12 06:57:32'),
(9, 7, 7, 'DT01_K2023_01', 'Điện ô tô K2023 - Lớp 1', 28, NULL, 1, 1, NULL, NULL, '2025-12-12 06:57:32', '2025-12-12 06:57:32'),
(10, 8, 8, 'DT02_K2023_01', 'Điện công nghiệp K2023 - Lớp 1', 32, NULL, 1, 1, NULL, NULL, '2025-12-12 06:57:32', '2025-12-12 06:57:32'),
(11, 9, 9, 'DT03_K2023_01', 'Điện tử viễn thông K2023 - Lớp 1', 30, NULL, 1, 1, NULL, NULL, '2025-12-12 06:57:32', '2025-12-12 06:57:32');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `mon_hoc`
--

CREATE TABLE `mon_hoc` (
  `mon_hoc_id` int(11) NOT NULL,
  `nghe_id` int(11) NOT NULL COMMENT 'ID nghề',
  `nien_khoa_id` int(11) NOT NULL COMMENT 'ID niên khóa',
  `ma_mon_hoc` varchar(20) NOT NULL COMMENT 'Mã môn học',
  `ten_mon_hoc` varchar(200) NOT NULL COMMENT 'Tên môn học',
  `so_tin_chi` int(11) DEFAULT 0 COMMENT 'Số tín chỉ',
  `so_gio_ly_thuyet` int(11) DEFAULT 0 COMMENT 'Số giờ lý thuyết',
  `so_gio_thuc_hanh` int(11) DEFAULT 0 COMMENT 'Số giờ thực hành',
  `so_gio_chuan` int(11) NOT NULL DEFAULT 0 COMMENT 'Tổng số giờ chuẩn',
  `hoc_ky` int(11) DEFAULT NULL COMMENT 'Học kỳ (1,2,3...)',
  `mo_ta` text DEFAULT NULL COMMENT 'Mô tả',
  `thu_tu` int(11) DEFAULT 0 COMMENT 'Thứ tự hiển thị',
  `is_active` tinyint(1) DEFAULT 1 COMMENT '1=Hoạt động, 0=Ngừng',
  `created_by` int(11) DEFAULT NULL COMMENT 'Người tạo',
  `updated_by` int(11) DEFAULT NULL COMMENT 'Người cập nhật',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng Môn học';

--
-- Đang đổ dữ liệu cho bảng `mon_hoc`
--

INSERT INTO `mon_hoc` (`mon_hoc_id`, `nghe_id`, `nien_khoa_id`, `ma_mon_hoc`, `ten_mon_hoc`, `so_tin_chi`, `so_gio_ly_thuyet`, `so_gio_thuc_hanh`, `so_gio_chuan`, `hoc_ky`, `mo_ta`, `thu_tu`, `is_active`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'CNTT01_MH01', 'Lập trình C/C++', 4, 30, 30, 60, NULL, NULL, 1, 1, NULL, NULL, '2025-12-12 06:57:32', '2025-12-12 06:57:32'),
(2, 1, 1, 'CNTT01_MH02', 'Cơ sở dữ liệu', 3, 25, 20, 45, NULL, NULL, 2, 1, NULL, NULL, '2025-12-12 06:57:32', '2025-12-12 06:57:32'),
(3, 1, 1, 'CNTT01_MH03', 'Lập trình Web', 4, 30, 30, 60, NULL, NULL, 3, 1, NULL, NULL, '2025-12-12 06:57:32', '2025-12-12 06:57:32'),
(4, 2, 2, 'CNTT02_MH01', 'Mạng máy tính', 3, 25, 20, 45, NULL, NULL, 1, 1, NULL, NULL, '2025-12-12 06:57:32', '2025-12-12 06:57:32'),
(5, 2, 2, 'CNTT02_MH02', 'Quản trị hệ thống Windows Server', 4, 30, 30, 60, NULL, NULL, 2, 1, NULL, NULL, '2025-12-12 06:57:32', '2025-12-12 06:57:32'),
(6, 2, 2, 'CNTT02_MH03', 'Quản trị hệ thống Linux', 4, 30, 30, 60, NULL, NULL, 3, 1, NULL, NULL, '2025-12-12 06:57:32', '2025-12-12 06:57:32'),
(7, 3, 3, 'CNTT03_MH01', 'Photoshop cơ bản', 3, 15, 30, 45, NULL, NULL, 1, 1, NULL, NULL, '2025-12-12 06:57:32', '2025-12-12 06:57:32'),
(8, 3, 3, 'CNTT03_MH02', 'Illustrator', 3, 15, 30, 45, NULL, NULL, 2, 1, NULL, NULL, '2025-12-12 06:57:32', '2025-12-12 06:57:32'),
(9, 3, 3, 'CNTT03_MH03', 'Thiết kế UI/UX', 4, 20, 40, 60, NULL, NULL, 3, 1, NULL, NULL, '2025-12-12 06:57:32', '2025-12-12 06:57:32'),
(10, 4, 4, 'CK01_MH01', 'Vẽ kỹ thuật cơ khí', 3, 20, 25, 45, NULL, NULL, 1, 1, NULL, NULL, '2025-12-12 06:57:32', '2025-12-12 06:57:32'),
(11, 4, 4, 'CK01_MH02', 'Công nghệ gia công cơ khí', 4, 25, 35, 60, NULL, NULL, 2, 1, NULL, NULL, '2025-12-12 06:57:32', '2025-12-12 06:57:32'),
(12, 4, 4, 'CK01_MH03', 'Đo lường kỹ thuật', 3, 20, 25, 45, NULL, NULL, 3, 1, NULL, NULL, '2025-12-12 06:57:32', '2025-12-12 06:57:32'),
(13, 5, 5, 'CK02_MH01', 'Kỹ thuật hàn điện', 4, 20, 40, 60, NULL, NULL, 1, 1, NULL, NULL, '2025-12-12 06:57:32', '2025-12-12 06:57:32'),
(14, 5, 5, 'CK02_MH02', 'Hàn TIG/MIG', 4, 20, 40, 60, NULL, NULL, 2, 1, NULL, NULL, '2025-12-12 06:57:32', '2025-12-12 06:57:32'),
(15, 6, 6, 'CK03_MH01', 'Cấu tạo động cơ ô tô', 4, 30, 30, 60, NULL, NULL, 1, 1, NULL, NULL, '2025-12-12 06:57:32', '2025-12-12 06:57:32'),
(16, 6, 6, 'CK03_MH02', 'Hệ thống truyền lực', 3, 25, 20, 45, NULL, NULL, 2, 1, NULL, NULL, '2025-12-12 06:57:32', '2025-12-12 06:57:32'),
(17, 7, 7, 'DT01_MH01', 'Điện ô tô cơ bản', 3, 25, 20, 45, NULL, NULL, 1, 1, NULL, NULL, '2025-12-12 06:57:32', '2025-12-12 06:57:32'),
(18, 7, 7, 'DT01_MH02', 'Hệ thống đánh lửa', 3, 20, 25, 45, NULL, NULL, 2, 1, NULL, NULL, '2025-12-12 06:57:32', '2025-12-12 06:57:32'),
(19, 8, 8, 'DT02_MH01', 'Mạch điện tử công suất', 4, 30, 30, 60, NULL, NULL, 1, 1, NULL, NULL, '2025-12-12 06:57:32', '2025-12-12 06:57:32'),
(20, 8, 8, 'DT02_MH02', 'PLC và tự động hóa', 4, 25, 35, 60, NULL, NULL, 2, 1, NULL, NULL, '2025-12-12 06:57:32', '2025-12-12 06:57:32'),
(21, 9, 9, 'DT03_MH01', 'Kỹ thuật số', 3, 25, 20, 45, NULL, NULL, 1, 1, NULL, NULL, '2025-12-12 06:57:32', '2025-12-12 06:57:32'),
(22, 9, 9, 'DT03_MH02', 'Truyền thông số', 4, 30, 30, 60, NULL, NULL, 2, 1, NULL, NULL, '2025-12-12 06:57:32', '2025-12-12 06:57:32');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `nghe`
--

CREATE TABLE `nghe` (
  `nghe_id` int(11) NOT NULL,
  `khoa_id` int(11) NOT NULL COMMENT 'ID khoa',
  `ma_nghe` varchar(20) NOT NULL COMMENT 'Mã nghề',
  `ten_nghe` varchar(100) NOT NULL COMMENT 'Tên nghề',
  `mo_ta` text DEFAULT NULL COMMENT 'Mô tả',
  `so_nam_dao_tao` int(11) DEFAULT NULL COMMENT 'Số năm đào tạo',
  `thu_tu` int(11) DEFAULT 0 COMMENT 'Thứ tự hiển thị',
  `is_active` tinyint(1) DEFAULT 1 COMMENT '1=Hoạt động, 0=Ngừng',
  `created_by` int(11) DEFAULT NULL COMMENT 'Người tạo',
  `updated_by` int(11) DEFAULT NULL COMMENT 'Người cập nhật',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng Nghề';

--
-- Đang đổ dữ liệu cho bảng `nghe`
--

INSERT INTO `nghe` (`nghe_id`, `khoa_id`, `ma_nghe`, `ten_nghe`, `mo_ta`, `so_nam_dao_tao`, `thu_tu`, `is_active`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 1, 'CNTT01', 'Lập trình máy tính', 'Nghề Lập trình máy tính', 3, 1, 1, NULL, NULL, '2025-12-12 06:57:32', '2025-12-12 06:57:32'),
(2, 1, 'CNTT02', 'Quản trị mạng máy tính', 'Nghề Quản trị mạng máy tính', 3, 2, 1, NULL, NULL, '2025-12-12 06:57:32', '2025-12-12 06:57:32'),
(3, 1, 'CNTT03', 'Thiết kế đồ họa', 'Nghề Thiết kế đồ họa', 3, 3, 1, NULL, NULL, '2025-12-12 06:57:32', '2025-12-12 06:57:32'),
(4, 2, 'CK01', 'Cơ khí chế tạo', 'Nghề Cơ khí chế tạo', 3, 1, 1, NULL, NULL, '2025-12-12 06:57:32', '2025-12-12 06:57:32'),
(5, 2, 'CK02', 'Hàn', 'Nghề Hàn công nghiệp', 2, 2, 1, NULL, NULL, '2025-12-12 06:57:32', '2025-12-12 06:57:32'),
(6, 2, 'CK03', 'Sửa chữa và lắp ráp ô tô', 'Nghề Sửa chữa ô tô', 3, 3, 1, NULL, NULL, '2025-12-12 06:57:32', '2025-12-12 06:57:32'),
(7, 3, 'DT01', 'Điện ô tô', 'Nghề Điện ô tô', 2, 1, 1, NULL, NULL, '2025-12-12 06:57:32', '2025-12-12 06:57:32'),
(8, 3, 'DT02', 'Điện công nghiệp', 'Nghề Điện công nghiệp', 3, 2, 1, NULL, NULL, '2025-12-12 06:57:32', '2025-12-12 06:57:32'),
(9, 3, 'DT03', 'Điện tử viễn thông', 'Nghề Điện tử viễn thông', 3, 3, 1, NULL, NULL, '2025-12-12 06:57:32', '2025-12-12 06:57:32');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `nien_khoa`
--

CREATE TABLE `nien_khoa` (
  `nien_khoa_id` int(11) NOT NULL,
  `nghe_id` int(11) NOT NULL COMMENT 'ID nghề',
  `cap_do_id` int(11) NOT NULL COMMENT 'ID cấp độ',
  `ma_nien_khoa` varchar(20) NOT NULL COMMENT 'Mã niên khóa',
  `ten_nien_khoa` varchar(50) NOT NULL COMMENT 'Tên niên khóa (VD: 2023-2026)',
  `nam_bat_dau` year(4) NOT NULL COMMENT 'Năm bắt đầu',
  `nam_ket_thuc` year(4) NOT NULL COMMENT 'Năm kết thúc',
  `mo_ta` text DEFAULT NULL COMMENT 'Mô tả',
  `is_active` tinyint(1) DEFAULT 1 COMMENT '1=Hoạt động, 0=Ngừng',
  `created_by` int(11) DEFAULT NULL COMMENT 'Người tạo',
  `updated_by` int(11) DEFAULT NULL COMMENT 'Người cập nhật',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng Niên khóa';

--
-- Đang đổ dữ liệu cho bảng `nien_khoa`
--

INSERT INTO `nien_khoa` (`nien_khoa_id`, `nghe_id`, `cap_do_id`, `ma_nien_khoa`, `ten_nien_khoa`, `nam_bat_dau`, `nam_ket_thuc`, `mo_ta`, `is_active`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'NK_CNTT01_CD_2023', 'Niên khóa 2023-2025 - Lập trình máy tính (Cao đẳng', '2023', '2025', NULL, 1, NULL, NULL, '2025-12-12 06:57:32', '2025-12-12 06:57:32'),
(2, 2, 1, 'NK_CNTT02_CD_2023', 'Niên khóa 2023-2025 - Quản trị mạng (Cao đẳng)', '2023', '2025', NULL, 1, NULL, NULL, '2025-12-12 06:57:32', '2025-12-12 06:57:32'),
(3, 3, 1, 'NK_CNTT03_CD_2023', 'Niên khóa 2023-2025 - Thiết kế đồ họa (Cao đẳng)', '2023', '2025', NULL, 1, NULL, NULL, '2025-12-12 06:57:32', '2025-12-12 06:57:32'),
(4, 4, 2, 'NK_CK01_TC_2023', 'Niên khóa 2023-2026 - Cơ khí chế tạo (Trung cấp)', '2023', '2026', NULL, 1, NULL, NULL, '2025-12-12 06:57:32', '2025-12-12 06:57:32'),
(5, 5, 1, 'NK_CK02_CD_2023', 'Niên khóa 2023-2025 - Hàn (Cao đẳng)', '2023', '2025', NULL, 1, NULL, NULL, '2025-12-12 06:57:32', '2025-12-12 06:57:32'),
(6, 6, 1, 'NK_CK03_CD_2023', 'Niên khóa 2023-2025 - Sửa chữa ô tô (Cao đẳng)', '2023', '2025', NULL, 1, NULL, NULL, '2025-12-12 06:57:32', '2025-12-12 06:57:32'),
(7, 7, 1, 'NK_DT01_CD_2023', 'Niên khóa 2023-2025 - Điện ô tô (Cao đẳng)', '2023', '2025', NULL, 1, NULL, NULL, '2025-12-12 06:57:32', '2025-12-12 06:57:32'),
(8, 8, 2, 'NK_DT02_TC_2023', 'Niên khóa 2023-2026 - Điện công nghiệp (Trung cấp)', '2023', '2026', NULL, 1, NULL, NULL, '2025-12-12 06:57:32', '2025-12-12 06:57:32'),
(9, 9, 1, 'NK_DT03_CD_2023', 'Niên khóa 2023-2025 - Điện tử viễn thông (Cao đẳng', '2023', '2025', NULL, 1, NULL, NULL, '2025-12-12 06:57:32', '2025-12-12 06:57:32');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `roles`
--

CREATE TABLE `roles` (
  `role_id` int(11) NOT NULL,
  `role_name` varchar(50) NOT NULL COMMENT 'Tên vai trò',
  `role_display_name` varchar(100) NOT NULL COMMENT 'Tên hiển thị',
  `role_description` text DEFAULT NULL COMMENT 'Mô tả vai trò',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng vai trò người dùng';

--
-- Đang đổ dữ liệu cho bảng `roles`
--

INSERT INTO `roles` (`role_id`, `role_name`, `role_display_name`, `role_description`, `created_at`) VALUES
(1, 'Admin', 'Quản trị viên', 'Quản trị toàn bộ hệ thống, quản lý danh mục, đơn giá', '2025-12-11 02:40:19'),
(2, 'Phong_Dao_Tao', 'Phòng Đào tạo', 'Xem dashboard tổng hợp, xuất báo cáo, duyệt hợp đồng', '2025-12-11 02:40:19'),
(3, 'Truong_Khoa', 'Trưởng Khoa', 'Xem dashboard khoa, xuất báo cáo khoa', '2025-12-11 02:40:19'),
(4, 'Giao_Vu', 'Giáo vụ', 'Quản lý giảng viên, tạo hợp đồng, in hợp đồng', '2025-12-11 02:40:19');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `trinh_do_chuyen_mon`
--

CREATE TABLE `trinh_do_chuyen_mon` (
  `trinh_do_id` int(11) NOT NULL,
  `ma_trinh_do` varchar(20) NOT NULL COMMENT 'Mã trình độ',
  `ten_trinh_do` varchar(50) NOT NULL COMMENT 'Tên trình độ',
  `mo_ta` text DEFAULT NULL COMMENT 'Mô tả',
  `thu_tu` int(11) DEFAULT 0 COMMENT 'Thứ tự hiển thị (1=Đại học, 2=Thạc sĩ, 3=Tiến sĩ)',
  `is_active` tinyint(1) DEFAULT 1 COMMENT '1=Hoạt động, 0=Ngừng',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng Trình độ chuyên môn';

--
-- Đang đổ dữ liệu cho bảng `trinh_do_chuyen_mon`
--

INSERT INTO `trinh_do_chuyen_mon` (`trinh_do_id`, `ma_trinh_do`, `ten_trinh_do`, `mo_ta`, `thu_tu`, `is_active`, `created_at`) VALUES
(1, 'DH', 'Đại học', 'Bằng Đại học', 1, 1, '2025-12-11 02:40:19'),
(2, 'THS', 'Thạc sĩ', 'Bằng Thạc sĩ', 2, 1, '2025-12-11 02:40:19'),
(3, 'TS', 'Tiến sĩ', 'Bằng Tiến sĩ', 3, 1, '2025-12-11 02:40:19');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL COMMENT 'Tên đăng nhập',
  `password` varchar(255) NOT NULL COMMENT 'Mật khẩu (không hash theo yêu cầu)',
  `full_name` varchar(100) NOT NULL COMMENT 'Họ và tên',
  `email` varchar(100) DEFAULT NULL COMMENT 'Email',
  `phone` varchar(20) DEFAULT NULL COMMENT 'Số điện thoại',
  `role_id` int(11) NOT NULL COMMENT 'ID vai trò',
  `khoa_id` int(11) DEFAULT NULL COMMENT 'ID khoa (NULL cho Admin/Phòng ĐT)',
  `is_active` tinyint(1) DEFAULT 1 COMMENT '1=Hoạt động, 0=Khóa',
  `last_login` datetime DEFAULT NULL COMMENT 'Lần đăng nhập cuối',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng người dùng hệ thống';

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `full_name`, `email`, `phone`, `role_id`, `khoa_id`, `is_active`, `last_login`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin123', 'Nguyễn Văn An', 'admin@cdnhcm.edu.vn', '0901234567', 1, NULL, 1, '2025-12-12 08:08:20', '2025-12-11 02:40:19', '2025-12-12 01:08:20'),
(2, 'phongdaotao', '123456', 'Trần Thị Bình', 'phongdaotao@cdnhcm.edu.vn', '0901234568', 2, NULL, 1, NULL, '2025-12-11 02:40:19', '2025-12-11 02:40:19'),
(3, 'phohieuruong', '123456', 'Lê Văn Cường', 'phohieuruong@cdnhcm.edu.vn', '0901234569', 2, NULL, 1, NULL, '2025-12-11 02:40:19', '2025-12-11 02:40:19'),
(4, 'truongkhoa_cntt', '123456', 'Phạm Thị Dung', 'truongkhoa.cntt@cdnhcm.edu.vn', '0901234570', 3, 1, 1, NULL, '2025-12-11 02:40:19', '2025-12-11 02:40:19'),
(5, 'truongkhoa_kt', '123456', 'Hoàng Văn Em', 'truongkhoa.kt@cdnhcm.edu.vn', '0901234571', 3, 2, 1, NULL, '2025-12-11 02:40:19', '2025-12-11 02:40:19'),
(6, 'truongkhoa_ck', '123456', 'Võ Thị Phương', 'truongkhoa.ck@cdnhcm.edu.vn', '0901234572', 3, 3, 1, NULL, '2025-12-11 02:40:19', '2025-12-11 02:40:19'),
(7, 'truongkhoa_dl', '123456', 'Đỗ Văn Giang', 'truongkhoa.dl@cdnhcm.edu.vn', '0901234573', 3, 4, 1, NULL, '2025-12-11 02:40:19', '2025-12-11 02:40:19'),
(8, 'giaovu_cntt', '123456', 'Nguyễn Thị Hoa', 'giaovu.cntt@cdnhcm.edu.vn', '0901234574', 4, 1, 1, '2025-12-12 09:30:59', '2025-12-11 02:40:19', '2025-12-12 02:30:59'),
(9, 'giaovu_kt', '123456', 'Trần Văn Inh', 'giaovu.kt@cdnhcm.edu.vn', '0901234575', 4, 2, 1, NULL, '2025-12-11 02:40:19', '2025-12-11 02:40:19'),
(10, 'giaovu_ck', '123456', 'Lê Thị Kim', 'giaovu.ck@cdnhcm.edu.vn', '0901234576', 4, 3, 1, NULL, '2025-12-11 02:40:19', '2025-12-11 02:40:19'),
(11, 'giaovu_dl', '123456', 'Phạm Văn Long', 'giaovu.dl@cdnhcm.edu.vn', '0901234577', 4, 4, 1, NULL, '2025-12-11 02:40:19', '2025-12-11 02:40:19');

-- --------------------------------------------------------

--
-- Cấu trúc đóng vai cho view `v_hop_dong_chi_tiet`
-- (See below for the actual view)
--
CREATE TABLE `v_hop_dong_chi_tiet` (
`hop_dong_id` int(11)
,`so_hop_dong` varchar(50)
,`ngay_hop_dong` date
,`thang_hop_dong` int(11)
,`nam_hop_dong` year(4)
,`ten_giang_vien` varchar(100)
,`nam_sinh` year(4)
,`gioi_tinh` enum('Nam','Nữ','Khác')
,`so_cccd_cmnd` varchar(20)
,`ngay_cap_cccd` date
,`noi_cap_cccd` varchar(200)
,`trinh_do_chuyen_mon_gv` varchar(50)
,`chuyen_nganh_dao_tao_gv` varchar(200)
,`chung_chi_su_pham_gv` varchar(200)
,`dia_chi_gv` text
,`so_dt_gv` varchar(20)
,`email_gv` varchar(100)
,`so_tai_khoan_gv` varchar(50)
,`ten_ngan_hang` varchar(100)
,`chi_nhanh_ngan_hang` varchar(200)
,`ma_so_thue_gv` varchar(20)
,`ten_mon_hoc` varchar(200)
,`tong_gio_mon_hoc` int(11)
,`cap_do_giang_day` varchar(50)
,`nghe_giang_day` varchar(100)
,`ma_lop` varchar(20)
,`ten_lop` varchar(100)
,`ngay_bat_dau_giang_day` date
,`ngay_ket_thuc_giang_day` date
,`don_gia_1_gio` decimal(15,2)
,`so_tien` decimal(15,2)
,`so_tien_bang_chu` varchar(500)
,`ten_khoa` varchar(100)
,`ma_khoa` varchar(20)
,`ten_co_so` varchar(100)
,`ten_nien_khoa` varchar(50)
,`trang_thai` enum('Mới tạo','Đã duyệt','Đang thực hiện','Hoàn thành','Hủy')
,`da_thanh_toan` decimal(15,2)
,`ghi_chu` text
,`nguoi_tao` varchar(100)
,`nguoi_duyet` varchar(100)
,`ngay_duyet` datetime
,`created_at` timestamp
,`updated_at` timestamp
);

-- --------------------------------------------------------

--
-- Cấu trúc đóng vai cho view `v_thong_ke_giang_vien`
-- (See below for the actual view)
--
CREATE TABLE `v_thong_ke_giang_vien` (
`khoa_id` int(11)
,`ma_khoa` varchar(20)
,`ten_khoa` varchar(100)
,`so_luong_giang_vien` bigint(21)
,`so_gv_dang_hoat_dong` bigint(21)
,`so_gv_dai_hoc` bigint(21)
,`so_gv_thac_si` bigint(21)
,`so_gv_tien_si` bigint(21)
);

-- --------------------------------------------------------

--
-- Cấu trúc đóng vai cho view `v_thong_ke_hop_dong`
-- (See below for the actual view)
--
CREATE TABLE `v_thong_ke_hop_dong` (
`khoa_id` int(11)
,`ten_khoa` varchar(100)
,`nam` int(4)
,`thang` int(2)
,`so_luong_hop_dong` bigint(21)
,`so_luong_giang_vien` bigint(21)
,`tong_so_gio` decimal(32,0)
,`tong_chi_phi` decimal(37,2)
,`da_thanh_toan` decimal(37,2)
,`con_no` decimal(38,2)
);

-- --------------------------------------------------------

--
-- Cấu trúc cho view `v_hop_dong_chi_tiet`
--
DROP TABLE IF EXISTS `v_hop_dong_chi_tiet`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_hop_dong_chi_tiet`  AS SELECT `hd`.`hop_dong_id` AS `hop_dong_id`, `hd`.`so_hop_dong` AS `so_hop_dong`, `hd`.`ngay_hop_dong` AS `ngay_hop_dong`, `hd`.`thang_hop_dong` AS `thang_hop_dong`, `hd`.`nam_hop_dong` AS `nam_hop_dong`, `gv`.`ten_giang_vien` AS `ten_giang_vien`, `gv`.`nam_sinh` AS `nam_sinh`, `gv`.`gioi_tinh` AS `gioi_tinh`, `gv`.`so_cccd` AS `so_cccd_cmnd`, `gv`.`ngay_cap_cccd` AS `ngay_cap_cccd`, `gv`.`noi_cap_cccd` AS `noi_cap_cccd`, `td`.`ten_trinh_do` AS `trinh_do_chuyen_mon_gv`, `gv`.`chuyen_nganh_dao_tao` AS `chuyen_nganh_dao_tao_gv`, `gv`.`chung_chi_su_pham` AS `chung_chi_su_pham_gv`, `gv`.`dia_chi` AS `dia_chi_gv`, `gv`.`so_dien_thoai` AS `so_dt_gv`, `gv`.`email` AS `email_gv`, `gv`.`so_tai_khoan` AS `so_tai_khoan_gv`, `gv`.`ten_ngan_hang` AS `ten_ngan_hang`, `gv`.`chi_nhanh_ngan_hang` AS `chi_nhanh_ngan_hang`, `gv`.`ma_so_thue` AS `ma_so_thue_gv`, `mh`.`ten_mon_hoc` AS `ten_mon_hoc`, `hd`.`tong_gio_mon_hoc` AS `tong_gio_mon_hoc`, `cd`.`ten_cap_do` AS `cap_do_giang_day`, `n`.`ten_nghe` AS `nghe_giang_day`, `l`.`ma_lop` AS `ma_lop`, `l`.`ten_lop` AS `ten_lop`, `hd`.`ngay_bat_dau` AS `ngay_bat_dau_giang_day`, `hd`.`ngay_ket_thuc` AS `ngay_ket_thuc_giang_day`, `hd`.`don_gia_gio` AS `don_gia_1_gio`, `hd`.`tong_tien` AS `so_tien`, `hd`.`tong_tien_chu` AS `so_tien_bang_chu`, `k`.`ten_khoa` AS `ten_khoa`, `k`.`ma_khoa` AS `ma_khoa`, `cs`.`ten_co_so` AS `ten_co_so`, `nk`.`ten_nien_khoa` AS `ten_nien_khoa`, `hd`.`trang_thai` AS `trang_thai`, `hd`.`da_thanh_toan` AS `da_thanh_toan`, `hd`.`ghi_chu` AS `ghi_chu`, `u1`.`full_name` AS `nguoi_tao`, `u2`.`full_name` AS `nguoi_duyet`, `hd`.`approved_at` AS `ngay_duyet`, `hd`.`created_at` AS `created_at`, `hd`.`updated_at` AS `updated_at` FROM (((((((((((`hop_dong` `hd` left join `giang_vien` `gv` on(`hd`.`giang_vien_id` = `gv`.`giang_vien_id`)) left join `trinh_do_chuyen_mon` `td` on(`gv`.`trinh_do_id` = `td`.`trinh_do_id`)) left join `mon_hoc` `mh` on(`hd`.`mon_hoc_id` = `mh`.`mon_hoc_id`)) left join `cap_do_giang_day` `cd` on(`hd`.`cap_do_id` = `cd`.`cap_do_id`)) left join `nghe` `n` on(`hd`.`nghe_id` = `n`.`nghe_id`)) left join `khoa` `k` on(`n`.`khoa_id` = `k`.`khoa_id`)) left join `lop` `l` on(`hd`.`lop_id` = `l`.`lop_id`)) left join `co_so` `cs` on(`hd`.`co_so_id` = `cs`.`co_so_id`)) left join `nien_khoa` `nk` on(`hd`.`nien_khoa_id` = `nk`.`nien_khoa_id`)) left join `users` `u1` on(`hd`.`created_by` = `u1`.`user_id`)) left join `users` `u2` on(`hd`.`approved_by` = `u2`.`user_id`)) ;

-- --------------------------------------------------------

--
-- Cấu trúc cho view `v_thong_ke_giang_vien`
--
DROP TABLE IF EXISTS `v_thong_ke_giang_vien`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_thong_ke_giang_vien`  AS SELECT `k`.`khoa_id` AS `khoa_id`, `k`.`ma_khoa` AS `ma_khoa`, `k`.`ten_khoa` AS `ten_khoa`, count(`gv`.`giang_vien_id`) AS `so_luong_giang_vien`, count(case when `gv`.`is_active` = 1 then 1 end) AS `so_gv_dang_hoat_dong`, count(case when `td`.`ten_trinh_do` = 'Đại học' then 1 end) AS `so_gv_dai_hoc`, count(case when `td`.`ten_trinh_do` = 'Thạc sĩ' then 1 end) AS `so_gv_thac_si`, count(case when `td`.`ten_trinh_do` = 'Tiến sĩ' then 1 end) AS `so_gv_tien_si` FROM ((`khoa` `k` left join `giang_vien` `gv` on(`k`.`khoa_id` = `gv`.`khoa_id`)) left join `trinh_do_chuyen_mon` `td` on(`gv`.`trinh_do_id` = `td`.`trinh_do_id`)) GROUP BY `k`.`khoa_id`, `k`.`ma_khoa`, `k`.`ten_khoa` ;

-- --------------------------------------------------------

--
-- Cấu trúc cho view `v_thong_ke_hop_dong`
--
DROP TABLE IF EXISTS `v_thong_ke_hop_dong`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_thong_ke_hop_dong`  AS SELECT `k`.`khoa_id` AS `khoa_id`, `k`.`ten_khoa` AS `ten_khoa`, year(`hd`.`ngay_hop_dong`) AS `nam`, month(`hd`.`ngay_hop_dong`) AS `thang`, count(`hd`.`hop_dong_id`) AS `so_luong_hop_dong`, count(distinct `hd`.`giang_vien_id`) AS `so_luong_giang_vien`, sum(`hd`.`tong_gio_mon_hoc`) AS `tong_so_gio`, sum(`hd`.`tong_tien`) AS `tong_chi_phi`, sum(`hd`.`da_thanh_toan`) AS `da_thanh_toan`, sum(`hd`.`tong_tien` - `hd`.`da_thanh_toan`) AS `con_no` FROM ((`hop_dong` `hd` left join `nghe` `n` on(`hd`.`nghe_id` = `n`.`nghe_id`)) left join `khoa` `k` on(`n`.`khoa_id` = `k`.`khoa_id`)) GROUP BY `k`.`khoa_id`, `k`.`ten_khoa`, year(`hd`.`ngay_hop_dong`), month(`hd`.`ngay_hop_dong`) ;

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_action` (`action`),
  ADD KEY `idx_table_record` (`table_name`,`record_id`);

--
-- Chỉ mục cho bảng `cap_do_giang_day`
--
ALTER TABLE `cap_do_giang_day`
  ADD PRIMARY KEY (`cap_do_id`),
  ADD UNIQUE KEY `ma_cap_do` (`ma_cap_do`),
  ADD UNIQUE KEY `ten_cap_do` (`ten_cap_do`),
  ADD KEY `idx_active` (`is_active`);

--
-- Chỉ mục cho bảng `co_so`
--
ALTER TABLE `co_so`
  ADD PRIMARY KEY (`co_so_id`),
  ADD UNIQUE KEY `ma_co_so` (`ma_co_so`),
  ADD KEY `idx_ma_co_so` (`ma_co_so`),
  ADD KEY `idx_active` (`is_active`);

--
-- Chỉ mục cho bảng `don_gia_gio_day`
--
ALTER TABLE `don_gia_gio_day`
  ADD PRIMARY KEY (`don_gia_id`),
  ADD KEY `idx_co_so` (`co_so_id`),
  ADD KEY `idx_trinh_do` (`trinh_do_id`),
  ADD KEY `idx_ngay_ap_dung` (`ngay_ap_dung`),
  ADD KEY `idx_active` (`is_active`);

--
-- Chỉ mục cho bảng `giang_vien`
--
ALTER TABLE `giang_vien`
  ADD PRIMARY KEY (`giang_vien_id`),
  ADD UNIQUE KEY `ma_giang_vien` (`ma_giang_vien`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `idx_khoa` (`khoa_id`),
  ADD KEY `idx_ma_gv` (`ma_giang_vien`),
  ADD KEY `idx_ten` (`ten_giang_vien`),
  ADD KEY `idx_cccd` (`so_cccd`),
  ADD KEY `idx_trinh_do` (`trinh_do_id`),
  ADD KEY `idx_active` (`is_active`),
  ADD KEY `idx_giang_vien_khoa_active` (`khoa_id`,`is_active`);

--
-- Chỉ mục cho bảng `hop_dong`
--
ALTER TABLE `hop_dong`
  ADD PRIMARY KEY (`hop_dong_id`),
  ADD UNIQUE KEY `so_hop_dong` (`so_hop_dong`),
  ADD KEY `nien_khoa_id` (`nien_khoa_id`),
  ADD KEY `cap_do_id` (`cap_do_id`),
  ADD KEY `co_so_id` (`co_so_id`),
  ADD KEY `approved_by` (`approved_by`),
  ADD KEY `idx_so_hop_dong` (`so_hop_dong`),
  ADD KEY `idx_nam` (`nam_hop_dong`),
  ADD KEY `idx_ngay` (`ngay_hop_dong`),
  ADD KEY `idx_giang_vien` (`giang_vien_id`),
  ADD KEY `idx_mon_hoc` (`mon_hoc_id`),
  ADD KEY `idx_nghe` (`nghe_id`),
  ADD KEY `idx_lop` (`lop_id`),
  ADD KEY `idx_trang_thai` (`trang_thai`),
  ADD KEY `idx_created_by` (`created_by`),
  ADD KEY `idx_ngay_bat_dau` (`ngay_bat_dau`),
  ADD KEY `idx_ngay_ket_thuc` (`ngay_ket_thuc`),
  ADD KEY `idx_hop_dong_khoa_thang` (`ngay_hop_dong`,`trang_thai`);

--
-- Chỉ mục cho bảng `import_logs`
--
ALTER TABLE `import_logs`
  ADD PRIMARY KEY (`import_id`),
  ADD KEY `idx_loai` (`loai_import`),
  ADD KEY `idx_created_by` (`created_by`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Chỉ mục cho bảng `khoa`
--
ALTER TABLE `khoa`
  ADD PRIMARY KEY (`khoa_id`),
  ADD UNIQUE KEY `ma_khoa` (`ma_khoa`),
  ADD KEY `idx_ma_khoa` (`ma_khoa`),
  ADD KEY `idx_active` (`is_active`),
  ADD KEY `idx_thu_tu` (`thu_tu`);

--
-- Chỉ mục cho bảng `lop`
--
ALTER TABLE `lop`
  ADD PRIMARY KEY (`lop_id`),
  ADD UNIQUE KEY `unique_ma_lop` (`nghe_id`,`ma_lop`),
  ADD KEY `idx_nghe` (`nghe_id`),
  ADD KEY `idx_ma_lop` (`ma_lop`),
  ADD KEY `idx_nien_khoa` (`nien_khoa_id`),
  ADD KEY `idx_active` (`is_active`);

--
-- Chỉ mục cho bảng `mon_hoc`
--
ALTER TABLE `mon_hoc`
  ADD PRIMARY KEY (`mon_hoc_id`),
  ADD UNIQUE KEY `unique_ma_mon` (`nghe_id`,`nien_khoa_id`,`ma_mon_hoc`),
  ADD KEY `idx_nghe` (`nghe_id`),
  ADD KEY `idx_nien_khoa` (`nien_khoa_id`),
  ADD KEY `idx_ma_mon` (`ma_mon_hoc`),
  ADD KEY `idx_active` (`is_active`),
  ADD KEY `idx_mon_hoc_nghe_nk` (`nghe_id`,`nien_khoa_id`,`is_active`);

--
-- Chỉ mục cho bảng `nghe`
--
ALTER TABLE `nghe`
  ADD PRIMARY KEY (`nghe_id`),
  ADD UNIQUE KEY `unique_ma_nghe` (`khoa_id`,`ma_nghe`),
  ADD KEY `idx_khoa` (`khoa_id`),
  ADD KEY `idx_ma_nghe` (`ma_nghe`),
  ADD KEY `idx_active` (`is_active`);

--
-- Chỉ mục cho bảng `nien_khoa`
--
ALTER TABLE `nien_khoa`
  ADD PRIMARY KEY (`nien_khoa_id`),
  ADD UNIQUE KEY `unique_nien_khoa` (`nghe_id`,`cap_do_id`,`ten_nien_khoa`),
  ADD KEY `idx_nghe` (`nghe_id`),
  ADD KEY `idx_cap_do` (`cap_do_id`),
  ADD KEY `idx_active` (`is_active`);

--
-- Chỉ mục cho bảng `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`role_id`),
  ADD UNIQUE KEY `role_name` (`role_name`),
  ADD KEY `idx_role_name` (`role_name`);

--
-- Chỉ mục cho bảng `trinh_do_chuyen_mon`
--
ALTER TABLE `trinh_do_chuyen_mon`
  ADD PRIMARY KEY (`trinh_do_id`),
  ADD UNIQUE KEY `ma_trinh_do` (`ma_trinh_do`),
  ADD UNIQUE KEY `ten_trinh_do` (`ten_trinh_do`),
  ADD KEY `idx_active` (`is_active`),
  ADD KEY `idx_thu_tu` (`thu_tu`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `idx_username` (`username`),
  ADD KEY `idx_role` (`role_id`),
  ADD KEY `idx_khoa` (`khoa_id`),
  ADD KEY `idx_active` (`is_active`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `cap_do_giang_day`
--
ALTER TABLE `cap_do_giang_day`
  MODIFY `cap_do_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `co_so`
--
ALTER TABLE `co_so`
  MODIFY `co_so_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `don_gia_gio_day`
--
ALTER TABLE `don_gia_gio_day`
  MODIFY `don_gia_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT cho bảng `giang_vien`
--
ALTER TABLE `giang_vien`
  MODIFY `giang_vien_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT cho bảng `hop_dong`
--
ALTER TABLE `hop_dong`
  MODIFY `hop_dong_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `import_logs`
--
ALTER TABLE `import_logs`
  MODIFY `import_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `khoa`
--
ALTER TABLE `khoa`
  MODIFY `khoa_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `lop`
--
ALTER TABLE `lop`
  MODIFY `lop_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT cho bảng `mon_hoc`
--
ALTER TABLE `mon_hoc`
  MODIFY `mon_hoc_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT cho bảng `nghe`
--
ALTER TABLE `nghe`
  MODIFY `nghe_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT cho bảng `nien_khoa`
--
ALTER TABLE `nien_khoa`
  MODIFY `nien_khoa_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT cho bảng `roles`
--
ALTER TABLE `roles`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `trinh_do_chuyen_mon`
--
ALTER TABLE `trinh_do_chuyen_mon`
  MODIFY `trinh_do_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `fk_activity_logs_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `don_gia_gio_day`
--
ALTER TABLE `don_gia_gio_day`
  ADD CONSTRAINT `don_gia_gio_day_ibfk_1` FOREIGN KEY (`co_so_id`) REFERENCES `co_so` (`co_so_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `don_gia_gio_day_ibfk_2` FOREIGN KEY (`trinh_do_id`) REFERENCES `trinh_do_chuyen_mon` (`trinh_do_id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `giang_vien`
--
ALTER TABLE `giang_vien`
  ADD CONSTRAINT `giang_vien_ibfk_1` FOREIGN KEY (`khoa_id`) REFERENCES `khoa` (`khoa_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `giang_vien_ibfk_2` FOREIGN KEY (`trinh_do_id`) REFERENCES `trinh_do_chuyen_mon` (`trinh_do_id`),
  ADD CONSTRAINT `giang_vien_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `hop_dong`
--
ALTER TABLE `hop_dong`
  ADD CONSTRAINT `hop_dong_ibfk_1` FOREIGN KEY (`giang_vien_id`) REFERENCES `giang_vien` (`giang_vien_id`),
  ADD CONSTRAINT `hop_dong_ibfk_2` FOREIGN KEY (`mon_hoc_id`) REFERENCES `mon_hoc` (`mon_hoc_id`),
  ADD CONSTRAINT `hop_dong_ibfk_3` FOREIGN KEY (`nghe_id`) REFERENCES `nghe` (`nghe_id`),
  ADD CONSTRAINT `hop_dong_ibfk_4` FOREIGN KEY (`lop_id`) REFERENCES `lop` (`lop_id`),
  ADD CONSTRAINT `hop_dong_ibfk_5` FOREIGN KEY (`nien_khoa_id`) REFERENCES `nien_khoa` (`nien_khoa_id`),
  ADD CONSTRAINT `hop_dong_ibfk_6` FOREIGN KEY (`cap_do_id`) REFERENCES `cap_do_giang_day` (`cap_do_id`),
  ADD CONSTRAINT `hop_dong_ibfk_7` FOREIGN KEY (`co_so_id`) REFERENCES `co_so` (`co_so_id`),
  ADD CONSTRAINT `hop_dong_ibfk_8` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `hop_dong_ibfk_9` FOREIGN KEY (`approved_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `import_logs`
--
ALTER TABLE `import_logs`
  ADD CONSTRAINT `import_logs_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `lop`
--
ALTER TABLE `lop`
  ADD CONSTRAINT `lop_ibfk_1` FOREIGN KEY (`nghe_id`) REFERENCES `nghe` (`nghe_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lop_ibfk_2` FOREIGN KEY (`nien_khoa_id`) REFERENCES `nien_khoa` (`nien_khoa_id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `mon_hoc`
--
ALTER TABLE `mon_hoc`
  ADD CONSTRAINT `mon_hoc_ibfk_1` FOREIGN KEY (`nghe_id`) REFERENCES `nghe` (`nghe_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `mon_hoc_ibfk_2` FOREIGN KEY (`nien_khoa_id`) REFERENCES `nien_khoa` (`nien_khoa_id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `nghe`
--
ALTER TABLE `nghe`
  ADD CONSTRAINT `nghe_ibfk_1` FOREIGN KEY (`khoa_id`) REFERENCES `khoa` (`khoa_id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `nien_khoa`
--
ALTER TABLE `nien_khoa`
  ADD CONSTRAINT `nien_khoa_ibfk_1` FOREIGN KEY (`nghe_id`) REFERENCES `nghe` (`nghe_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `nien_khoa_ibfk_2` FOREIGN KEY (`cap_do_id`) REFERENCES `cap_do_giang_day` (`cap_do_id`);

--
-- Các ràng buộc cho bảng `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
