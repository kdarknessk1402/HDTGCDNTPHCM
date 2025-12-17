# HƯỚNG DẪN CÀI ĐẶT CHI TIẾT

## YÊU CẦU HỆ THỐNG

- **PHP**: >= 7.4 (khuyến nghị 8.0+)
- **MySQL**: >= 5.7 hoặc MariaDB >= 10.3
- **Apache**: 2.4+ với mod_rewrite
- **Composer**: Latest version
- **Extensions PHP cần thiết**:
  - pdo_mysql
  - mbstring
  - xml
  - zip
  - gd (cho xử lý ảnh)

## BƯỚC 1: CHUẨN BỊ

### 1.1. Tạo thư mục project

```bash
mkdir lecturers-management
cd lecturers-management
```

### 1.2. Copy files vào project

Giải nén hoặc copy toàn bộ files theo cấu trúc:

```
lecturers-management/
├── config/
│   ├── database.php
│   ├── functions.php
│   └── routes.php
├── controllers/
│   └── (9 controller files)
├── models/
│   └── (10 model files)
├── views/
│   └── (32 view files)
├── public/
│   ├── index.php
│   └── .htaccess (rename từ htaccess.txt)
├── uploads/
│   ├── giang_vien/
│   └── hop_dong/
├── composer.json
└── README.md
```

## BƯỚC 2: CÀI ĐẶT DEPENDENCIES

### 2.1. Install Composer packages

```bash
composer install
```

Sẽ cài đặt:

- phpoffice/phpspreadsheet (xuất Excel)
- phpoffice/phpword (xuất Word)

### 2.2. Kiểm tra cài đặt

```bash
composer show
```

## BƯỚC 3: CẤU HÌNH DATABASE

### 3.1. Tạo database

```sql
CREATE DATABASE lecturers_management
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;
```

### 3.2. Import database

```bash
mysql -u root -p lecturers_management < database.sql
mysql -u root -p lecturers_management < activity_logs_table.sql
```

Hoặc dùng phpMyAdmin:

1. Vào phpMyAdmin
2. Chọn database `lecturers_management`
3. Tab "Import"
4. Chọn file `database.sql` → Execute
5. Chọn file `activity_logs_table.sql` → Execute

### 3.3. Cấu hình kết nối

Mở file `config/database.php`:

```php
$host = 'localhost';
$dbname = 'lecturers_management';
$username = 'root';      // Thay bằng username MySQL của bạn
$password = '';          // Thay bằng password MySQL của bạn
```

### 3.4. Test kết nối

```bash
php -r "require 'config/database.php'; echo 'Connected successfully!';"
```

## BƯỚC 4: CẤU HÌNH APACHE

### 4.1. Tạo Virtual Host (khuyến nghị)

**Windows (xampp/wampp):**
Mở `C:\xampp\apache\conf\extra\httpd-vhosts.conf`:

```apache
<VirtualHost *:80>
    DocumentRoot "C:/xampp/htdocs/lecturers-management/public"
    ServerName lecturers.local

    <Directory "C:/xampp/htdocs/lecturers-management/public">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog "logs/lecturers-error.log"
    CustomLog "logs/lecturers-access.log" common
</VirtualHost>
```

**Linux/Mac:**

```bash
sudo nano /etc/apache2/sites-available/lecturers.conf
```

```apache
<VirtualHost *:80>
    DocumentRoot "/var/www/lecturers-management/public"
    ServerName lecturers.local

    <Directory "/var/www/lecturers-management/public">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/lecturers-error.log
    CustomLog ${APACHE_LOG_DIR}/lecturers-access.log combined
</VirtualHost>
```

Enable site:

```bash
sudo a2ensite lecturers
sudo a2enmod rewrite
sudo systemctl restart apache2
```

### 4.2. Cập nhật hosts file

**Windows:** `C:\Windows\System32\drivers\etc\hosts`
**Linux/Mac:** `/etc/hosts`

Thêm dòng:

```
127.0.0.1    lecturers.local
```

### 4.3. Kiểm tra mod_rewrite

```bash
# Linux/Mac
apache2ctl -M | grep rewrite

# Windows (XAMPP)
httpd -M | findstr rewrite
```

Nếu chưa có, enable:

```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

## BƯỚC 5: PHÂN QUYỀN THƯ MỤC

### Linux/Mac:

```bash
chmod -R 755 lecturers-management/
chmod -R 777 lecturers-management/uploads/
chown -R www-data:www-data lecturers-management/
```

### Windows:

- Right click folder `uploads` → Properties → Security
- Edit → Add → Everyone → Full Control

## BƯỚC 6: KIỂM TRA CÀI ĐẶT

### 6.1. Truy cập hệ thống

Mở trình duyệt:

```
http://lecturers.local
```

Hoặc nếu không dùng virtual host:

```
http://localhost/lecturers-management/public
```

### 6.2. Đăng nhập với tài khoản demo

| Vai trò       | Username   | Password   |
| ------------- | ---------- | ---------- |
| Admin         | admin      | admin123   |
| Phòng Đào tạo | phongdt    | phongdt123 |
| Trưởng Khoa   | truongkhoa | tk123      |
| Giáo vụ       | giaovu     | gv123      |

### 6.3. Kiểm tra chức năng

✅ Đăng nhập thành công
✅ Dashboard hiển thị đúng
✅ Tạo/Sửa/Xóa danh mục
✅ Upload files
✅ Export Excel
✅ Export Word

## BƯỚC 7: BẢO MẬT (PRODUCTION)

### 7.1. Đổi mật khẩu tất cả users

```sql
UPDATE users SET password = '$2y$10$NEW_HASH_HERE' WHERE username = 'admin';
```

### 7.2. Disable debug mode

Trong `config/database.php`, comment dòng:

```php
// PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
```

### 7.3. Set quyền strict cho uploads

```bash
chmod 755 uploads/
```

### 7.4. Enable HTTPS

Sử dụng Let's Encrypt hoặc SSL certificate khác

## TROUBLESHOOTING

### Lỗi "404 Not Found"

- Kiểm tra file `.htaccess` trong `/public`
- Enable mod_rewrite: `sudo a2enmod rewrite`
- Kiểm tra `AllowOverride All` trong VirtualHost

### Lỗi "Permission denied" khi upload

```bash
chmod -R 777 uploads/
```

### Lỗi "Class not found"

```bash
composer dump-autoload
```

### Lỗi kết nối database

- Kiểm tra MySQL đã chạy: `sudo systemctl status mysql`
- Kiểm tra username/password trong `config/database.php`
- Test: `mysql -u root -p`

### Lỗi "Vendor not found"

```bash
composer install
```

### Excel/Word export không hoạt động

```bash
composer require phpoffice/phpspreadsheet
composer require phpoffice/phpword
```

## NÂNG CẤP

### Update packages

```bash
composer update
```

### Backup database

```bash
mysqldump -u root -p lecturers_management > backup_$(date +%Y%m%d).sql
```

### Restore database

```bash
mysql -u root -p lecturers_management < backup_20250113.sql
```

## HỖ TRỢ

Nếu gặp vấn đề:

1. Kiểm tra error log: `/logs/lecturers-error.log`
2. Kiểm tra PHP error: `tail -f /var/log/apache2/error.log`
3. Enable debug: Uncomment `dd()` trong code

## PRODUCTION CHECKLIST

- [ ] Đổi tất cả passwords
- [ ] Disable error display
- [ ] Enable HTTPS
- [ ] Set strict permissions
- [ ] Backup database định kỳ
- [ ] Monitor error logs
- [ ] Update packages thường xuyên
- [ ] Scan security vulnerabilities

---

**Chúc bạn cài đặt thành công! 🎉**
