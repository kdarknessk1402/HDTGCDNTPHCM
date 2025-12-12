# HỆ THỐNG QUẢN LÝ HỢP ĐỒNG THỈNH GIẢNG (HDTG)

Phiên bản: 0.0.1
Ngày: 12/12/2025

## GIỚI THIỆU

Hệ thống quản lý hợp đồng thỉnh giảng dành cho Cao đẳng Nghề TP.HCM.
Quản lý toàn bộ quy trình từ danh mục, giảng viên, hợp đồng đến báo cáo và thanh toán.

## YÊU CẦU HỆ THỐNG

- PHP >= 7.4
- MySQL >= 8.0
- Apache/Nginx Web Server
- Extensions: PDO, PDO_MySQL, mbstring

## CÀI ĐẶT

### 1. Cấu hình Database

```sql
-- Import file database.sql vào MySQL
mysql -u root -p hdtg_db < database.sql
```

### 2. Cấu hình ứng dụng

Chỉnh sửa file `/config/config.php`:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'hdtg_db');
define('DB_USER', 'root');
define('DB_PASS', '');
define('BASE_URL', 'http://localhost/hdtg_project');
```

### 3. Phân quyền thư mục

```bash
chmod 755 /uploads
chmod 755 /uploads/giang_vien
chmod 755 /uploads/hop_dong
chmod 755 /uploads/imports
chmod 755 /uploads/temp
```

### 4. Khởi động

Truy cập: `http://localhost/hdtg_project`

## TÀI KHOẢN DEMO

| Username          | Password    | Role          | Khoa   |
| ----------------- | ----------- | ------------- | ------ |
| admin             | admin123    | Admin         | -      |
| phongdaotao       | pdt123      | Phòng Đào tạo | -      |
| truongkhoa_cntt   | tk_cntt123  | Trưởng Khoa   | CNTT   |
| truongkhoa_co_khi | tk_cokhi123 | Trưởng Khoa   | Cơ khí |
| truongkhoa_oto    | tk_oto123   | Trưởng Khoa   | Ô tô   |
| truongkhoa_dien   | tk_dien123  | Trưởng Khoa   | Điện   |
| giaovu_cntt       | gv_cntt123  | Giáo vụ       | CNTT   |
| giaovu_cokhi      | gv_cokhi123 | Giáo vụ       | Cơ khí |
| giaovu_oto        | gv_oto123   | Giáo vụ       | Ô tô   |
| giaovu_dien       | gv_dien123  | Giáo vụ       | Điện   |

## CẤU TRÚC THƯ MỤC

```
/hdtg_project/
├── config/                 # Cấu hình
│   ├── config.php
│   └── database.php
├── controllers/            # Controllers
│   ├── Controller.php
│   └── AuthController.php
├── models/                 # Models
│   ├── Model.php
│   ├── User.php
│   ├── Khoa.php
│   ├── Nghe.php
│   ├── Lop.php
│   ├── MonHoc.php
│   ├── CoSo.php
│   ├── DonGiaGioDay.php
│   ├── GiangVien.php
│   ├── HopDong.php
│   ├── DanhMuc.php
│   └── NienKhoa.php
├── views/                  # Views
│   ├── layouts/
│   │   ├── header.php
│   │   └── footer.php
│   └── auth/
│       └── login.php
├── helpers/                # Helper functions
│   └── functions.php
├── public/                 # Assets
│   ├── css/
│   │   └── style.css
│   └── js/
│       └── main.js
├── danh-muc/              # CRUD Danh mục
│   ├── khoa.php
│   ├── nghe.php
│   ├── lop.php
│   ├── mon-hoc.php
│   ├── co-so.php
│   └── don-gia.php
├── giao-vu/               # CRUD Giáo vụ
│   ├── giang-vien.php
│   ├── giang-vien-edit.php
│   ├── hop-dong.php
│   └── hop-dong-form.php
├── api/                   # API endpoints
│   └── number-to-words.php
├── uploads/               # Upload files
│   ├── giang_vien/
│   ├── hop_dong/
│   ├── imports/
│   └── temp/
├── .htaccess
├── login.php
├── logout.php
├── index.php
└── README.md
```

## CHỨC NĂNG

### Admin

- ✅ Quản lý danh mục: Khoa, Nghề, Lớp, Môn học, Cơ sở, Đơn giá
- ✅ Quản lý giảng viên
- ✅ Quản lý hợp đồng
- 🔄 Quản lý người dùng (Đang phát triển)
- 🔄 Báo cáo thống kê (Đang phát triển)

### Giáo vụ

- ✅ Quản lý giảng viên (Thêm/Sửa/Xem)
- ✅ Quản lý hợp đồng (CRUD đầy đủ)
- 🔄 Import Excel giảng viên (Đang phát triển)
- 🔄 Export Excel báo cáo (Đang phát triển)
- 🔄 In hợp đồng Word (Đang phát triển)

### Trưởng Khoa

- 🔄 Xem hợp đồng theo khoa (Đang phát triển)
- 🔄 Báo cáo theo khoa (Đang phát triển)

### Phòng Đào tạo

- 🔄 Xem tổng hợp hợp đồng (Đang phát triển)
- 🔄 Báo cáo tổng hợp (Đang phát triển)

## DATABASE SCHEMA

### Bảng chính

- `users` - Người dùng hệ thống
- `roles` - Vai trò
- `khoa` - Khoa
- `nghe` - Nghề
- `lop` - Lớp
- `mon_hoc` - Môn học
- `co_so` - Cơ sở
- `don_gia_gio_day` - Đơn giá giờ dạy
- `giang_vien` - Giảng viên
- `hop_dong` - Hợp đồng thỉnh giảng
- `nien_khoa` - Niên khóa
- `cap_do_giang_day` - Cấp độ giảng dạy
- `trinh_do_chuyen_mon` - Trình độ chuyên môn

### Views

- `v_hop_dong_chi_tiet` - View chi tiết hợp đồng
- `v_thong_ke_hop_dong_theo_khoa` - Thống kê theo khoa
- `v_thong_ke_giang_vien` - Thống kê giảng viên

### Stored Procedures

- `sp_tao_hop_dong` - Tạo hợp đồng tự động
- `sp_tinh_tong_tien_hop_dong` - Tính tổng tiền

### Triggers

- `trg_auto_update_tong_tien` - Tự động cập nhật tổng tiền
- `trg_check_ngay_bat_dau_ket_thuc` - Kiểm tra ngày hợp lệ
- `trg_update_hop_dong_timestamp` - Cập nhật timestamp

## TECHNOLOGY STACK

### Backend

- PHP 7.4+
- PDO (MySQL)
- Session-based Authentication

### Frontend

- Bootstrap 5.3
- Bootstrap Icons 1.10
- jQuery 3.7

### Future Integration

- PHPWord (In hợp đồng)
- PHPSpreadsheet (Import/Export Excel)
- DataTables (Bảng động)

## BẢO MẬT

- Password: Plain text (chỉ dùng nội bộ)
- SQL Injection: Protected (PDO Prepared Statements)
- XSS: Sanitized (htmlspecialchars)
- CSRF: Chưa implement
- Session timeout: 24 giờ

## LƯU Ý QUAN TRỌNG

⚠️ **TÊN CỘT DATABASE CHÍNH XÁC**

Tất cả tên cột trong code đã được verify 100% với database:

**users table:**

- user_id, username, password, full_name, email, phone
- role_id, khoa_id, is_active, last_login
- created_at, updated_at

**khoa table:**

- khoa_id, ma_khoa, ten_khoa, mo_ta
- truong_khoa_id, so_dien_thoai, email, thu_tu
- is_active, created_by, updated_by, created_at, updated_at

**nghe table:**

- nghe_id, khoa_id, ma_nghe, ten_nghe, mo_ta
- so_nam_dao_tao, thu_tu, is_active
- created_by, updated_by, created_at, updated_at

**lop table:**

- lop_id, nghe_id, nien_khoa_id, ma_lop, ten_lop
- si_so, giao_vien_chu_nhiem, thu_tu, is_active
- created_by, updated_by, created_at, updated_at

**mon_hoc table:**

- mon_hoc_id, nghe_id, nien_khoa_id, ma_mon_hoc, ten_mon_hoc
- so_tiet_ly_thuyet, so_tiet_thuc_hanh, tong_so_tiet
- mo_ta, thu_tu, is_active
- created_by, updated_by, created_at, updated_at

**co_so table:**

- co_so_id, ma_co_so, ten_co_so, dia_chi
- so_dien_thoai, email, nguoi_phu_trach, mo_ta
- thu_tu, is_active, created_by, updated_by, created_at, updated_at

**don_gia_gio_day table:**

- don_gia_id, co_so_id, trinh_do_id, don_gia
- nam_ap_dung, tu_ngay, den_ngay, mo_ta, is_active
- created_by, updated_by, created_at, updated_at

**giang_vien table:**

- giang_vien_id, khoa_id, ma_giang_vien, ten_giang_vien
- nam_sinh, gioi_tinh, ngay_sinh, noi_sinh
- so_cccd, ngay_cap_cccd, noi_cap_cccd
- trinh_do_id, chuyen_nganh_dao_tao, truong_dao_tao, nam_tot_nghiep
- chung_chi_su_pham, dia_chi, dia_chi_tam_tru
- so_dien_thoai, email
- so_tai_khoan, ten_ngan_hang, chi_nhanh_ngan_hang, chu_tai_khoan
- ma_so_thue, file_cccd, file_bang_cap, file_chung_chi
- ghi_chu, is_active, created_by, updated_by, created_at, updated_at

**hop_dong table:**

- hop_dong_id, so_hop_dong, nam_hop_dong, ngay_hop_dong, thang_hop_dong
- giang_vien_id, mon_hoc_id, nghe_id, lop_id, nien_khoa_id, cap_do_id, co_so_id
- ngay_bat_dau, ngay_ket_thuc
- tong_gio_mon_hoc, don_gia_gio, tong_tien, tong_tien_chu
- da_thanh_toan, ngay_thanh_toan, hinh_thuc_thanh_toan
- trang_thai, file_hop_dong, file_bien_ban_giao_nhan
- ghi_chu, ly_do_huy
- created_by, updated_by, created_at, updated_at

## HỖ TRỢ

- Email: không có support nha
- Hotline: chưa có

## LICENSE

Copyright © 2025 Cao đẳng Nghề TP.HCM
All rights reserved.
