# DANH SÁCH TÀI KHOẢN HỆ THỐNG

## 🔐 THÔNG TIN ĐĂNG NHẬP

### 1️⃣ ADMIN (Quản trị viên)

| Username | Password   | Họ tên        | Email               | Vai trò                |
| -------- | ---------- | ------------- | ------------------- | ---------------------- |
| `admin`  | `admin123` | Nguyễn Văn An | admin@cdnhcm.edu.vn | Quản trị toàn hệ thống |

**Quyền hạn:**

- ✅ Quản lý tất cả danh mục (Khoa, Nghề, Lớp, Môn học, Cơ sở)
- ✅ Quản lý đơn giá giờ dạy
- ✅ Import danh mục từ Excel
- ✅ Dashboard tổng hợp toàn trường
- ✅ Xuất báo cáo toàn trường

---

### 2️⃣ PHÒNG ĐÀO TẠO / PHÓ HIỆU TRƯỞNG

| Username       | Password | Họ tên        | Email                      | Vai trò         |
| -------------- | -------- | ------------- | -------------------------- | --------------- |
| `phongdaotao`  | `123456` | Trần Thị Bình | phongdaotao@cdnhcm.edu.vn  | Phòng Đào tạo   |
| `phohieuruong` | `123456` | Lê Văn Cường  | phohieuruong@cdnhcm.edu.vn | Phó Hiệu trưởng |

**Quyền hạn:**

- ✅ Dashboard tổng hợp toàn trường (3 tháng gần nhất)
- ✅ Lọc theo: Khoa, Tháng, Giảng viên, Lớp, Nghề, Giá trị HĐ
- ✅ Xuất báo cáo Excel
- ✅ Duyệt hợp đồng

---

### 3️⃣ TRƯỞNG KHOA (4 khoa)

| Username          | Password | Họ tên        | Khoa                | Email                         |
| ----------------- | -------- | ------------- | ------------------- | ----------------------------- |
| `truongkhoa_cntt` | `123456` | Phạm Thị Dung | Công nghệ Thông tin | truongkhoa.cntt@cdnhcm.edu.vn |
| `truongkhoa_kt`   | `123456` | Hoàng Văn Em  | Kế toán             | truongkhoa.kt@cdnhcm.edu.vn   |
| `truongkhoa_ck`   | `123456` | Võ Thị Phương | Cơ khí              | truongkhoa.ck@cdnhcm.edu.vn   |
| `truongkhoa_dl`   | `123456` | Đỗ Văn Giang  | Du lịch             | truongkhoa.dl@cdnhcm.edu.vn   |

**Quyền hạn:**

- ✅ Dashboard của KHOA MÌNH (3 tháng gần nhất)
- ✅ Lọc theo: Giảng viên, Lớp, Tháng, Nghề, Giá trị HĐ
- ✅ Xuất báo cáo khoa
- ❌ **KHÔNG** xem được dữ liệu khoa khác

---

### 4️⃣ GIÁO VỤ (4 khoa)

| Username      | Password | Họ tên         | Khoa                | Email                     |
| ------------- | -------- | -------------- | ------------------- | ------------------------- |
| `giaovu_cntt` | `123456` | Nguyễn Thị Hoa | Công nghệ Thông tin | giaovu.cntt@cdnhcm.edu.vn |
| `giaovu_kt`   | `123456` | Trần Văn Inh   | Kế toán             | giaovu.kt@cdnhcm.edu.vn   |
| `giaovu_ck`   | `123456` | Lê Thị Kim     | Cơ khí              | giaovu.ck@cdnhcm.edu.vn   |
| `giaovu_dl`   | `123456` | Phạm Văn Long  | Du lịch             | giaovu.dl@cdnhcm.edu.vn   |

**Quyền hạn:**

- ✅ Quản lý Giảng viên (CRUD) của KHOA MÌNH
- ✅ Import giảng viên từ Excel
- ✅ Tạo hợp đồng thỉnh giảng
- ✅ In hợp đồng theo mẫu Word
- ❌ **KHÔNG** xem được giảng viên khoa khác

---

## 📋 TỔNG HỢP

| Vai trò      | Số lượng     | Scope         |
| ------------ | ------------ | ------------- |
| Admin        | 1            | Toàn hệ thống |
| Phòng ĐT/PHT | 2            | Toàn trường   |
| Trưởng Khoa  | 4            | Từng khoa     |
| Giáo vụ      | 4            | Từng khoa     |
| **TỔNG**     | **11 users** | -             |

---

## 🏢 DANH SÁCH KHOA

| Mã khoa | Tên khoa            | Trưởng khoa   | Giáo vụ        |
| ------- | ------------------- | ------------- | -------------- |
| CNTT    | Công nghệ Thông tin | Phạm Thị Dung | Nguyễn Thị Hoa |
| KT      | Kế toán             | Hoàng Văn Em  | Trần Văn Inh   |
| CK      | Cơ khí              | Võ Thị Phương | Lê Thị Kim     |
| DL      | Du lịch             | Đỗ Văn Giang  | Phạm Văn Long  |

---

## 🔒 LƯU Ý BẢO MẬT

1. **Mật khẩu mặc định**: Tất cả tài khoản (trừ admin) đều có mật khẩu `123456`
2. **Admin password**: `admin123`
3. **Khuyến nghị**: Đổi mật khẩu ngay sau lần đăng nhập đầu tiên
4. **Password không hash**: Theo yêu cầu dùng nội bộ, password lưu plain text

---

## 📝 GHI CHÚ

- Tất cả email đều theo domain: `chưa có domain`
- Số điện thoại mẫu: `0901234567` → `0901234577`
- Tất cả tài khoản đều ở trạng thái ACTIVE (`is_active = 1`)
- Để test đầy đủ chức năng, nên đăng nhập thử từng loại tài khoản

---

**Ngày tạo**: 12/12/2025
**Database version**: 1.0
