# php-project-exam

# Dự án: Website Đăng Ký Học Phần (PHP & phpMyAdmin)

Dự án này là một bài tập xây dựng ứng dụng web bằng PHP và MySQL (quản lý qua phpMyAdmin) cho phép sinh viên thực hiện đăng ký học phần trực tuyến.

## Mô tả

Website cung cấp các chức năng chính bao gồm quản lý thông tin sinh viên (Thêm, Sửa, Xóa, Xem), xem danh sách học phần, thực hiện đăng ký học phần qua cơ chế giỏ hàng, và xem lại các học phần đã đăng ký. Hệ thống yêu cầu sinh viên đăng nhập để thực hiện đăng ký.

## Yêu cầu Chức năng (Dựa trên file Test 01 .docx)

1.  **Quản lý Sinh Viên (5.0 điểm)** [Câu 1]
    * **Trang danh sách (Index):** Hiển thị danh sách sinh viên. [source: 5] [Image 3]
    * **Phân trang:** Hiển thị 4 sinh viên trên mỗi trang. [source: 10]
    * **Trang thêm mới (Create):** Form để nhập thông tin và thêm sinh viên mới vào CSDL (bao gồm upload hình ảnh và hash mật khẩu). [source: 6] [Image 4]
    * **Trang sửa (Edit):** Form hiển thị thông tin sinh viên hiện có và cho phép cập nhật (bao gồm đổi ảnh, đổi mật khẩu). [source: 7] [Image 5]
    * **Chức năng xóa (Delete):** Cho phép xóa sinh viên (có thể kèm trang xác nhận). [source: 8] [Image 6]
    * **Trang chi tiết (Details):** Hiển thị đầy đủ thông tin của một sinh viên. [source: 9] [Image 7]

2.  **Hiển thị Học phần (0.5 điểm)** [Câu 2]
    * Tạo trang liệt kê danh sách các học phần mà sinh viên có thể đăng ký (ví dụ: các học phần còn chỗ). [source: 10] [Image 8]
    * Có nút "Đăng ký" cho mỗi học phần để thêm vào giỏ hàng.

3.  **Đăng nhập (1.0 điểm)** [Câu 3]
    * Tạo trang đăng nhập cho sinh viên. [Image 9]
    * Username là `MaSV`.
    * Password được lưu trong bảng `SinhVien` (cần được hash). [source: 11]

4.  **Giỏ hàng Đăng ký (1.0 điểm)** [Câu 4]
    * Hiển thị trang "Giỏ hàng" liệt kê các học phần sinh viên đã chọn để đăng ký. [source: 12] [Image 10]
    * Hiển thị tổng số học phần và tổng số tín chỉ đã chọn.
    * Cho phép **xóa từng học phần** ra khỏi giỏ hàng. [source: 13] [Image 11]
    * Cho phép **xóa toàn bộ giỏ hàng** (Xóa đăng ký). [source: 14] [Image 12]

5.  **Lưu Đăng ký (1.0 điểm)** [Câu 5]
    * Xử lý chức năng "Lưu đăng ký" từ trang giỏ hàng. [source: 15] [Image 13]
    * Lưu thông tin vào bảng `DangKy` (ngày đăng ký, mã sinh viên).
    * Lưu thông tin chi tiết các học phần đã đăng ký vào bảng `ChiTietDangKy`.
    * Hiển thị thông báo đăng ký thành công. [source: 16] [Image 14]
    * Kiểm tra kết quả dữ liệu được lưu trong CSDL. [source: 17] [Image 15]

6.  **Quản lý Số lượng Học phần (1.0 điểm)** [Câu 6]
    * Thêm cột `SoLuongDuKien` (hoặc tên tương tự) vào bảng `HocPhan`. [source: 18]
    * Khi sinh viên "Lưu đăng ký" thành công, số lượng dự kiến của các học phần tương ứng phải được **giảm đi 1**. [source: 19]
    * (Nên có) Chỉ hiển thị các học phần còn chỗ (`SoLuongDuKien > 0`) trên trang danh sách học phần.
    * (Nên có) Kiểm tra lại số lượng trước khi thực hiện lưu đăng ký để tránh trường hợp nhiều người đăng ký cùng lúc khi chỉ còn 1 chỗ.

7.  **Giao diện Responsive (0.5 điểm)** [Câu 7]
    * Giao diện website cần có khả năng hiển thị tốt trên các kích thước màn hình khác nhau (desktop, tablet, mobile). [source: 19] (Gợi ý: Sử dụng CSS Framework như Bootstrap).

## Cơ sở dữ liệu

* **Tên Database:** `Test1` [source: 1, 3]
* **Hệ quản trị CSDL:** MySQL / MariaDB (Quản lý qua phpMyAdmin)
* **Script tạo bảng và dữ liệu mẫu:** Có sẵn trong file "Test 01 .docx".
    * **Lưu ý 1:** Script gốc viết theo cú pháp T-SQL (SQL Server). Cần **chuyển đổi** sang cú pháp MySQL để chạy được trong phpMyAdmin (ví dụ: bỏ `go`, thay `nvarchar` bằng `VARCHAR` hoặc `TEXT` với `utf8mb4_unicode_ci`, thay `int identity(1,1)` bằng `INT AUTO_INCREMENT`).
    * **Lưu ý 2:** Cần **thêm cột `Password`** (ví dụ: `VARCHAR(255)`) vào bảng `SinhVien` để lưu mật khẩu đã hash. [source: 11]
    * **Lưu ý 3:** Cần **thêm cột `SoLuongDuKien`** (ví dụ: `INT`) vào bảng `HocPhan`. [source: 18]
* **Sơ đồ quan hệ:** [Image 1]
* **Bảng:**
    * `NganhHoc` (MaNganh PK, TenNganh)
    * `SinhVien` (MaSV PK, HoTen, GioiTinh, NgaySinh, Hinh, MaNganh FK, Password)
    * `HocPhan` (MaHP PK, TenHP, SoTinChi, SoLuongDuKien)
    * `DangKy` (MaDK PK AI, NgayDK, MaSV FK)
    * `ChiTietDangKy` (MaDK FK, MaHP FK, PRIMARY KEY(MaDK, MaHP))

## Dữ liệu Mẫu

* Script có sẵn các lệnh `INSERT` dữ liệu mẫu. [source: 3]
* **Yêu cầu:** Sinh viên cần đổi thông tin và hình ảnh của ít nhất một sinh viên trong bảng `SinhVien` thành thông tin của chính mình. [source: 2]
* Cần tạo hash cho mật khẩu của các sinh viên mẫu để có thể đăng nhập.

## Môi trường & Cài đặt

* **Yêu cầu:** Web server hỗ trợ PHP và MySQL (ví dụ: XAMPP, WAMP, MAMP).
* **Các bước chính:**
    1.  Cài đặt XAMPP (hoặc tương đương).
    2.  Khởi động Apache và MySQL.
    3.  Truy cập phpMyAdmin (`http://localhost/phpmyadmin`).
    4.  Tạo database `Test1` với collation `utf8mb4_unicode_ci`.
    5.  Chạy script SQL (đã được **chuyển đổi sang MySQL** và **bổ sung cột Password, SoLuongDuKien**) trong tab SQL của database `Test1`.
    6.  Cập nhật dữ liệu mẫu (thông tin cá nhân, hash mật khẩu) trong bảng `SinhVien` qua phpMyAdmin.
    7.  Đặt code PHP của dự án vào thư mục web server (ví dụ: `htdocs/php-project-exam`).
    8.  Truy cập ứng dụng qua trình duyệt (ví dụ: `http://localhost/php-project-exam/login.php`).

## Công nghệ sử dụng

* **Backend:** PHP (>= 7.x)
* **Database:** MySQL / MariaDB
* **Frontend:** HTML, CSS, JavaScript
* **Framework/Library (Gợi ý):** Bootstrap 5 (cho responsive và UI components)
* **Quản lý CSDL:** phpMyAdmin

Database:
-- Sử dụng database Test1
USE Test1;

-- Tạo bảng NganhHoc
CREATE TABLE NganhHoc (
    MaNganh CHAR(4) PRIMARY KEY,
    TenNganh VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci -- Sử dụng VARCHAR thay nvarchar và định nghĩa charset
);

-- Tạo bảng SinhVien
CREATE TABLE SinhVien (
    MaSV CHAR(10) PRIMARY KEY,
    HoTen VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    GioiTinh VARCHAR(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    NgaySinh DATE,
    Hinh VARCHAR(255), -- Đường dẫn tới file hình
    MaNganh CHAR(4),
    Password VARCHAR(255) NOT NULL, -- Thêm cột password (sẽ được hash)
    FOREIGN KEY (MaNganh) REFERENCES NganhHoc(MaNganh)
);

-- Tạo bảng HocPhan
CREATE TABLE HocPhan (
    MaHP CHAR(6) PRIMARY KEY,
    TenHP VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    SoTinChi INT,
    SoLuongDuKien INT DEFAULT 50 -- Thêm cột số lượng dự kiến, ví dụ mặc định 50
);

-- Tạo bảng DangKy
CREATE TABLE DangKy (
    MaDK INT AUTO_INCREMENT PRIMARY KEY, -- Sử dụng AUTO_INCREMENT cho khóa tự tăng
    NgayDK DATE,
    MaSV CHAR(10),
    FOREIGN KEY (MaSV) REFERENCES SinhVien(MaSV)
);

-- Tạo bảng ChiTietDangKy
CREATE TABLE ChiTietDangKy (
    MaDK INT,
    MaHP CHAR(6),
    PRIMARY KEY (MaDK, MaHP), -- Khóa chính gồm 2 cột
    FOREIGN KEY (MaDK) REFERENCES DangKy(MaDK),
    FOREIGN KEY (MaHP) REFERENCES HocPhan(MaHP)
);

-- Chèn dữ liệu mẫu
INSERT INTO NganhHoc(MaNganh, TenNganh) VALUES('CNTT', 'Công nghệ thông tin');
INSERT INTO NganhHoc(MaNganh, TenNganh) VALUES('QTKD', 'Quản trị kinh doanh');

-- Lưu ý: Cần hash password trước khi insert. Ví dụ dùng password '123456'
-- Chạy lệnh này trong PHP để lấy hash: echo password_hash('123456', PASSWORD_DEFAULT);
-- Ví dụ hash: '$2y$10$xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx' (thay bằng hash thực tế)
INSERT INTO SinhVien(MaSV, HoTen, GioiTinh, NgaySinh, Hinh, MaNganh, Password) VALUES('0123456789', 'Nguyễn Văn A', 'Nam', '2000-12-02', 'images/sv1.jpg', 'CNTT', '$2y$10$xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx'); -- Thay đổi hình và thông tin của bạn, và hash password
INSERT INTO SinhVien(MaSV, HoTen, GioiTinh, NgaySinh, Hinh, MaNganh, Password) VALUES('9876543210', 'Nguyễn Thị B', 'Nữ', '2000-03-07', 'images/sv2.jpg', 'QTKD', '$2y$10$yyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyy'); -- Thay đổi hình và thông tin của bạn, và hash password

INSERT INTO HocPhan(MaHP, TenHP, SoTinChi, SoLuongDuKien) VALUES('CNTT01', 'Lập trình C', 3, 40);
INSERT INTO HocPhan(MaHP, TenHP, SoTinChi, SoLuongDuKien) VALUES('CNTT02', 'Cơ sở dữ liệu', 2, 30);
INSERT INTO HocPhan(MaHP, TenHP, SoTinChi, SoLuongDuKien) VALUES('QTKD01', 'Kinh tế vi mô', 2, 25);
-- Sửa mã học phần từ QTDK02 thành QTKD02 (nếu QTKD là mã ngành đúng)
INSERT INTO HocPhan(MaHP, TenHP, SoTinChi, SoLuongDuKien) VALUES('QTKD02', 'Xác suất thống kê 1', 3, 35);