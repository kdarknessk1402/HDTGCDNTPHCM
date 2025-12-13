# KẾ HOẠCH HOÀN THIỆN HỆ THỐNG QUẢN LÝ HỢP ĐỒNG GIẢNG VIÊN

## 📊 TỔNG QUAN TIẾN ĐỘ

### ✅ ĐÃ HOÀN THÀNH

1. **Database**: Hoàn chỉnh với 17 bảng, stored procedures, triggers
2. **Authentication**: Login/Logout, session management
3. **Layout**: Header, Footer, Navigation responsive
4. **Functions**: 40+ utility functions
5. **Models**: User, Khoa, Nghe (đã có)
6. **Controllers**: AdminKhoaController, AdminNgheController (đã có)

---

## 🎯 PHASE 1: ADMIN - QUẢN TRỊ HỆ THỐNG (Ưu tiên cao)

### Module 1: NGHỀ ✅ (Đã có Model + Controller)

**Cần làm tiếp:**

- [ ] `views/admin/nghe/index.php` - Danh sách nghề
- [ ] `views/admin/nghe/create.php` - Form thêm nghề
- [ ] `views/admin/nghe/edit.php` - Form sửa nghề

**Tính năng:**

- CRUD đầy đủ
- Filter theo khoa, trạng thái
- Kiểm tra ràng buộc trước khi xóa
- AJAX toggle trạng thái

---

### Module 2: NIÊN KHÓA (Ưu tiên cao)

**Cần tạo:**

- [ ] `models/NienKhoa.php`
- [ ] `controllers/AdminNienKhoaController.php`
- [ ] `views/admin/nien_khoa/index.php`
- [ ] `views/admin/nien_khoa/create.php`
- [ ] `views/admin/nien_khoa/edit.php`

**Quan hệ database:**

```
nien_khoa
├── nghe_id (FK → nghe)
├── cap_do_id (FK → cap_do_giang_day)
└── Được sử dụng bởi: lop, mon_hoc, hop_dong
```

**Tính năng:**

- CRUD niên khóa
- Chọn nghề → tự động lấy khoa
- Chọn cấp độ (Cao đẳng/Trung cấp)
- Validate năm bắt đầu < năm kết thúc
- Kiểm tra trùng: (nghe_id, cap_do_id, ten_nien_khoa)

---

### Module 3: LỚP

**Cần tạo:**

- [ ] `models/Lop.php`
- [ ] `controllers/AdminLopController.php`
- [ ] `views/admin/lop/index.php`
- [ ] `views/admin/lop/create.php`
- [ ] `views/admin/lop/edit.php`

**Quan hệ database:**

```
lop
├── nghe_id (FK → nghe)
├── nien_khoa_id (FK → nien_khoa)
└── Được sử dụng bởi: hop_dong
```

**Tính năng:**

- CRUD lớp học
- Chọn khoa → lọc nghề → lọc niên khóa (cascade dropdown)
- Quản lý sĩ số
- Giao viên chủ nhiệm (optional)

---

### Module 4: MÔN HỌC (Quan trọng)

**Cần tạo:**

- [ ] `models/MonHoc.php`
- [ ] `controllers/AdminMonHocController.php`
- [ ] `views/admin/mon_hoc/index.php`
- [ ] `views/admin/mon_hoc/create.php`
- [ ] `views/admin/mon_hoc/edit.php`

**Quan hệ database:**

```
mon_hoc
├── nghe_id (FK → nghe)
├── nien_khoa_id (FK → nien_khoa)
└── Được sử dụng bởi: hop_dong
```

**Tính năng:**

- CRUD môn học
- Cascade: Khoa → Nghề → Niên khóa
- Quản lý: Số tín chỉ, giờ lý thuyết, giờ thực hành, giờ chuẩn
- Học kỳ (1,2,3...)
- Tính tổng giờ chuẩn tự động

---

### Module 5: CƠ SỞ

**Cần tạo:**

- [ ] `models/CoSo.php`
- [ ] `controllers/AdminCoSoController.php`
- [ ] `views/admin/co_so/index.php`
- [ ] `views/admin/co_so/create.php`
- [ ] `views/admin/co_so/edit.php`

**Tính năng:**

- CRUD cơ sở
- Quản lý thông tin liên hệ
- Người phụ trách

---

### Module 6: ĐƠN GIÁ GIỜ DẠY (Quan trọng)

**Cần tạo:**

- [ ] `models/DonGia.php`
- [ ] `controllers/AdminDonGiaController.php`
- [ ] `views/admin/don_gia/index.php`
- [ ] `views/admin/don_gia/create.php`
- [ ] `views/admin/don_gia/edit.php`

**Quan hệ database:**

```
don_gia_gio_day
├── co_so_id (FK → co_so)
├── trinh_do_id (FK → trinh_do_chuyen_mon)
└── Được sử dụng bởi: hop_dong (tính tiền)
```

**Tính năng:**

- CRUD đơn giá theo cơ sở + trình độ
- Quản lý thời gian áp dụng (từ ngày → đến ngày)
- Hiển thị đơn giá hiện hành
- Lịch sử đơn giá

---

### Module 7: TRÌNH ĐỘ CHUYÊN MÔN

**Cần tạo:**

- [ ] `models/TrinhDo.php`
- [ ] `controllers/AdminTrinhDoController.php`
- [ ] `views/admin/trinh_do/index.php`
- [ ] `views/admin/trinh_do/create.php`
- [ ] `views/admin/trinh_do/edit.php`

**Tính năng:**

- CRUD trình độ (Tiến sĩ, Thạc sĩ, Đại học...)
- Thứ tự hiển thị

---

### Module 8: QUẢN LÝ USERS (Quan trọng)

**Cần tạo:**

- [ ] `controllers/AdminUsersController.php`
- [ ] `views/admin/users/index.php`
- [ ] `views/admin/users/create.php`
- [ ] `views/admin/users/edit.php`

**Tính năng:**

- CRUD users
- Phân quyền: Admin, Phòng Đào tạo, Trưởng khoa, Giáo vụ
- Gán khoa cho Trưởng khoa/Giáo vụ
- Reset password
- Khóa/Mở khóa tài khoản

---

## 🎯 PHASE 2: TRƯỞNG KHOA

### Module 1: QUẢN LÝ GIẢNG VIÊN (Quan trọng nhất)

**Cần tạo:**

- [ ] `models/GiangVien.php`
- [ ] `controllers/TruongKhoaGiangVienController.php`
- [ ] `views/truongkhoa/giang_vien/index.php`
- [ ] `views/truongkhoa/giang_vien/create.php`
- [ ] `views/truongkhoa/giang_vien/edit.php`
- [ ] `views/truongkhoa/giang_vien/detail.php`

**Tính năng:**

- CRUD giảng viên của khoa mình
- Upload file: CCCD, Bằng cấp, Chứng chỉ
- Quản lý thông tin đầy đủ (29 fields)
- Mã giảng viên tự động: `{MA_KHOA}GV{STT}`
- Tìm kiếm, filter

---

### Module 2: DUYỆT HỢP ĐỒNG

**Cần tạo:**

- [ ] `controllers/TruongKhoaHopDongController.php`
- [ ] `views/truongkhoa/hop_dong/index.php`
- [ ] `views/truongkhoa/hop_dong/detail.php`

**Tính năng:**

- Xem danh sách hợp đồng chờ duyệt
- Xem chi tiết hợp đồng
- Duyệt/Từ chối hợp đồng
- Ghi chú khi từ chối

---

### Module 3: BÁO CÁO THỐNG KÊ KHOA

**Cần tạo:**

- [ ] `controllers/TruongKhoaBaoCaoController.php`
- [ ] `views/truongkhoa/bao_cao/index.php`

**Tính năng:**

- Thống kê giảng viên theo trình độ
- Thống kê hợp đồng theo tháng/năm
- Thống kê tổng giờ dạy
- Export Excel

---

## 🎯 PHASE 3: GIÁO VỤ

### Module 1: QUẢN LÝ HỢP ĐỒNG (Core của hệ thống)

**Cần tạo:**

- [ ] `models/HopDong.php`
- [ ] `controllers/GiaoVuHopDongController.php`
- [ ] `views/giaovu/hop_dong/index.php`
- [ ] `views/giaovu/hop_dong/create.php` (Form phức tạp)
- [ ] `views/giaovu/hop_dong/edit.php`
- [ ] `views/giaovu/hop_dong/detail.php`

**Form tạo hợp đồng (Cascade complex):**

```
1. Chọn Giảng viên → load thông tin
2. Chọn Cơ sở → load đơn giá theo trình độ GV
3. Chọn Khoa → Nghề → Niên khóa → Môn học (cascade)
4. Chọn Lớp (theo nghề + niên khóa)
5. Chọn Cấp độ (Cao đẳng/Trung cấp)
6. Nhập: Ngày bắt đầu, Ngày kết thúc, Tổng giờ
7. Tính tự động: Tổng tiền = Đơn giá × Tổng giờ
8. Chuyển số thành chữ (Tổng tiền chữ)
```

**Tính năng:**

- CRUD hợp đồng (chỉ của khoa mình)
- Số hợp đồng tự động: `001/HĐ-CĐN`, `002/HĐ-CĐN`...
- Gửi duyệt lên Trưởng khoa
- In hợp đồng (Word template)
- Trạng thái: Nháp → Chờ duyệt → Đã duyệt → Từ chối

---

### Module 2: IN HỢP ĐỒNG (Quan trọng)

**Cần tạo:**

- [ ] `controllers/PrintHopDongController.php`
- [ ] `templates/hop_dong_template.docx` (Word template)
- [ ] Library: PHPWord hoặc tương tự

**Tính năng:**

- Load template Word
- Replace merge fields với dữ liệu
- Export ra file .docx để download

**Merge fields:**

```
{SO_HOP_DONG}, {NGAY_HOP_DONG}, {TEN_GIANG_VIEN},
{NAM_SINH}, {SO_CCCD}, {DIA_CHI}, {MON_HOC},
{TEN_LOP}, {TONG_GIO}, {DON_GIA}, {TONG_TIEN},
{TONG_TIEN_CHU}...
```

---

### Module 3: IMPORT EXCEL

**Cần tạo:**

- [ ] `controllers/ImportController.php`
- [ ] `views/giaovu/import/index.php`
- [ ] Library: PhpSpreadsheet

**Tính năng:**

- Import Giảng viên (Excel)
- Import Môn học (Excel)
- Import Lớp (Excel)
- Validate dữ liệu
- Hiển thị lỗi, thành công
- Log import

---

## 🎯 PHASE 4: PHÒNG ĐÀO TẠO

### Module 1: DUYỆT HỢP ĐỒNG CẤP CAO

**Cần tạo:**

- [ ] `controllers/PhongDaoTaoHopDongController.php`
- [ ] `views/phongdaotao/hop_dong/index.php`
- [ ] `views/phongdaotao/hop_dong/detail.php`

**Tính năng:**

- Xem tất cả hợp đồng đã được Trưởng khoa duyệt
- Duyệt cấp cao
- Từ chối + ghi chú

---

### Module 2: BÁO CÁO TỔNG HỢP

**Cần tạo:**

- [ ] `controllers/PhongDaoTaoBaoCaoController.php`
- [ ] `views/phongdaotao/bao_cao/index.php`

**Tính năng:**

- Báo cáo tổng hợp toàn trường
- Thống kê theo khoa, nghề, tháng, năm
- Export Excel

---

## 🎯 PHASE 5: CHỨC NĂNG CHUNG

### 1. DASHBOARD

**Cần tạo:**

- [ ] `views/dashboard/admin.php`
- [ ] `views/dashboard/truongkhoa.php`
- [ ] `views/dashboard/giaovu.php`
- [ ] `views/dashboard/phongdaotao.php`

**Nội dung:**

- Thống kê nhanh (cards)
- Biểu đồ (Chart.js)
- Danh sách nhanh

---

### 2. TÌM KIẾM NÂNG CAO

**Cần làm:**

- [ ] Form search với nhiều điều kiện
- [ ] AJAX search suggestions
- [ ] Pagination

---

### 3. PHÂN TRANG

**Cần tạo:**

- [ ] `includes/Pagination.php` class
- [ ] Style pagination Bootstrap

---

### 4. EXPORT EXCEL/PDF

**Cần:**

- [ ] PhpSpreadsheet (Excel)
- [ ] TCPDF/mPDF (PDF)
- [ ] Functions: `exportToExcel()`, `exportToPDF()`

---

### 5. HỆ THỐNG THÔNG BÁO

**Cần tạo:**

- [ ] `notifications` table
- [ ] `models/Notification.php`
- [ ] Realtime notification (AJAX polling hoặc WebSocket)

---

## 📦 THƯ VIỆN CẦN CÀI ĐẶT

```bash
composer require phpoffice/phpword          # Xử lý Word
composer require phpoffice/phpspreadsheet   # Xử lý Excel
composer require tecnickcom/tcpdf           # Tạo PDF
```

---

## 🔧 CẤU TRÚC THƯ MỤC HOÀN CHỈNH

```
/
├── config/
│   └── database.php
├── controllers/
│   ├── AdminKhoaController.php ✅
│   ├── AdminNgheController.php ✅
│   ├── AdminNienKhoaController.php
│   ├── AdminLopController.php
│   ├── AdminMonHocController.php
│   ├── AdminCoSoController.php
│   ├── AdminDonGiaController.php
│   ├── AdminTrinhDoController.php
│   ├── AdminUsersController.php
│   ├── TruongKhoaGiangVienController.php
│   ├── TruongKhoaHopDongController.php
│   ├── GiaoVuHopDongController.php
│   └── ...
├── models/
│   ├── User.php ✅
│   ├── Khoa.php ✅
│   ├── Nghe.php ✅
│   ├── NienKhoa.php
│   ├── Lop.php
│   ├── MonHoc.php
│   ├── CoSo.php
│   ├── DonGia.php
│   ├── TrinhDo.php
│   ├── GiangVien.php
│   └── HopDong.php
├── views/
│   ├── layouts/
│   │   ├── header.php ✅
│   │   └── footer.php ✅
│   ├── dashboard/
│   ├── admin/
│   │   ├── khoa/ ✅
│   │   ├── nghe/
│   │   ├── nien_khoa/
│   │   ├── lop/
│   │   ├── mon_hoc/
│   │   └── ...
│   ├── truongkhoa/
│   │   ├── giang_vien/
│   │   └── hop_dong/
│   ├── giaovu/
│   │   ├── hop_dong/
│   │   └── import/
│   └── phongdaotao/
├── includes/
│   ├── functions.php ✅
│   └── Pagination.php
├── assets/
│   ├── css/
│   ├── js/
│   └── images/
├── uploads/
│   ├── cccd/
│   ├── bang_cap/
│   └── chung_chi/
├── templates/
│   └── hop_dong_template.docx
└── index.php

```

---

## ⚡ THỨ TỰ ƯU TIÊN PHÁT TRIỂN

### SPRINT 1 (Tuần 1-2): Admin Module

1. Niên khóa ⭐⭐⭐
2. Lớp ⭐⭐⭐
3. Môn học ⭐⭐⭐
4. Cơ sở ⭐⭐
5. Đơn giá ⭐⭐⭐
6. Users ⭐⭐

### SPRINT 2 (Tuần 3-4): Trưởng khoa + Giáo vụ

1. Quản lý Giảng viên ⭐⭐⭐⭐⭐
2. Quản lý Hợp đồng ⭐⭐⭐⭐⭐
3. In hợp đồng ⭐⭐⭐⭐
4. Duyệt hợp đồng ⭐⭐⭐⭐

### SPRINT 3 (Tuần 5): Hoàn thiện

1. Dashboard cho tất cả vai trò ⭐⭐⭐
2. Báo cáo thống kê ⭐⭐⭐
3. Import Excel ⭐⭐
4. Export Excel/PDF ⭐⭐

---

## 🎯 KẾT LUẬN

**Tổng số file cần tạo:** ~150 files
**Thời gian ước tính:** 5-6 tuần cho 1 developer

**Module quan trọng nhất:**

1. 🔥 Quản lý Hợp đồng (Giáo vụ)
2. 🔥 Quản lý Giảng viên (Trưởng khoa)
3. 🔥 Đơn giá giờ dạy (Admin)

---

**Bạn muốn tôi bắt đầu làm module nào trước?**
