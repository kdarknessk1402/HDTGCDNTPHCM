# HỆ THỐNG QUẢN LÝ GIẢNG VIÊN THỈNH GIẢNG

Hệ thống quản lý hợp đồng giảng viên thỉnh giảng cho Trường Cao đẳng Nghề TP.HCM

## TÍNH NĂNG

### 1. QUẢN LÝ DANH MỤC

- ✅ Khoa
- ✅ Nghề
- ✅ Niên khóa
- ✅ Lớp
- ✅ Môn học
- ✅ Đơn giá giờ dạy
- ✅ Trình độ chuyên môn
- ✅ Cơ sở

### 2. QUẢN LÝ GIẢNG VIÊN

- ✅ 29 fields thông tin đầy đủ
- ✅ Upload 3 files (CCCD, Bằng cấp, Chứng chỉ)
- ✅ Quản lý theo khoa
- ✅ Thống kê hợp đồng

### 3. QUẢN LÝ HỢP ĐỒNG

- ✅ Cascade 5 cấp: Khoa → Nghề → Lớp → Môn học
- ✅ Tính tiền tự động (Giờ × Đơn giá)
- ✅ Đơn giá tự động theo Cơ sở × Trình độ
- ✅ Workflow: Mới tạo → Đã duyệt → Đang thực hiện → Hoàn thành
- ✅ Upload files hợp đồng

### 4. DASHBOARD 4 VAI TRÒ

- ✅ **Admin**: Toàn bộ hệ thống, charts, top GV
- ✅ **Phòng Đào tạo**: Tổng hợp, duyệt HĐ, thống kê khoa
- ✅ **Trưởng Khoa**: Chỉ khoa của mình
- ✅ **Giáo vụ**: Quản lý HĐ, quick actions

### 5. BÁO CÁO & XUẤT DỮ LIỆU

- ✅ Báo cáo Hợp đồng (Excel)
- ✅ Báo cáo Giảng viên (Excel)
- ✅ Báo cáo theo Khoa (Excel)
- ✅ Export Word: In hợp đồng chính thức

### 6. AUTHENTICATION

- ✅ Login/Logout
- ✅ Bcrypt password hashing
- ✅ Đổi mật khẩu
- ✅ 4 vai trò phân quyền

## CẤU TRÚC THƯ MỤC

```
project/
├── config/
│   ├── database.php          # Kết nối DB
│   ├── functions.php         # 40+ helper functions
│   └── routes.php            # Routing
├── controllers/              # 15+ Controllers
│   ├── AuthController.php
│   ├── DashboardController.php
│   ├── AdminKhoaController.php
│   ├── AdminGiangVienController.php
│   ├── AdminHopDongController.php
│   ├── ReportController.php
│   └── WordExportController.php
├── models/                   # 10+ Models
│   ├── Khoa.php
│   ├── GiangVien.php
│   ├── HopDong.php
│   └── ...
├── views/
│   ├── layouts/
│   │   ├── header.php
│   │   └── footer.php
│   ├── auth/
│   │   ├── login.php
│   │   └── change_password.php
│   ├── dashboard/
│   │   ├── admin.php
│   │   ├── phong_dao_tao.php
│   │   ├── truong_khoa.php
│   │   └── giao_vu.php
│   ├── admin/
│   │   ├── khoa/
│   │   ├── giang_vien/
│   │   ├── hop_dong/
│   │   └── ...
│   └── reports/
│       └── index.php
├── public/
│   ├── index.php             # Entry point
│   └── .htaccess
├── uploads/
│   ├── giang_vien/
│   └── hop_dong/
└── vendor/                   # Composer packages

```

## YÊU CẦU HỆ THỐNG

- PHP >= 7.4
- MySQL >= 5.7
- Apache/Nginx
- Composer

## DEPENDENCIES

```json
{
  "phpoffice/phpspreadsheet": "^1.29",
  "phpoffice/phpword": "^1.1"
}
```

## CÀI ĐẶT

### 1. Clone project

```bash
git clone [repo-url]
cd project
```

### 2. Install dependencies

```bash
composer install
```

### 3. Cấu hình database

- Tạo database: `lecturers_management`
- Import file: `database.sql`
- Cập nhật `config/database.php`:

```php
$host = 'localhost';
$dbname = 'lecturers_management';
$username = 'root';
$password = '';
```

### 4. Cấu hình Apache

```apache
<VirtualHost *:80>
    DocumentRoot "/path/to/project/public"
    ServerName lecturers.local

    <Directory "/path/to/project/public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

### 5. Tạo thư mục uploads

```bash
mkdir -p uploads/giang_vien
mkdir -p uploads/hop_dong
chmod -R 755 uploads
```

### 6. Truy cập

- URL: http://lecturers.local
- Hoặc: http://localhost/project/public

## TÀI KHOẢN MẶC ĐỊNH

| Vai trò       | Username   | Password   |
| ------------- | ---------- | ---------- |
| Admin         | admin      | admin123   |
| Phòng Đào tạo | phongdt    | phongdt123 |
| Trưởng Khoa   | truongkhoa | tk123      |
| Giáo vụ       | giaovu     | gv123      |

## CÔNG NGHỆ SỬ DỤNG

- **Backend**: PHP 7.4+ (MVC Pattern)
- **Database**: MySQL (17 tables, views, triggers, stored procedures)
- **Frontend**: Bootstrap 5, Chart.js
- **Export**: PhpSpreadsheet (Excel), PhpWord (Word)
- **Security**: Bcrypt password hashing, CSRF protection

## TỔNG SỐ FILES ĐÃ TẠO

**TỔNG: 53 FILES**

- Controllers: 9 files
- Models: 10 files
- Views: 32 files
- Config: 2 files

## LIÊN HỆ

Trường Cao đẳng Nghề TP.HCM
© 2025 - Phiên bản 1.0
