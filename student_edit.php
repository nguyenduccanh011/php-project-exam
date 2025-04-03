<?php
require_once 'includes/auth_check.php';
require_once 'includes/db_connection.php';

$maSV = $_GET['id'] ?? null;

if (!$maSV) {
    header('Location: students_index.php?message=Mã sinh viên không hợp lệ.&type=danger');
    exit();
}

try {
    // Lấy thông tin sinh viên cần sửa
    $sql = "SELECT * FROM SinhVien WHERE MaSV = :masv";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':masv' => $maSV]);
    $student = $stmt->fetch();

    if (!$student) {
        header('Location: students_index.php?message=Không tìm thấy sinh viên.&type=warning');
        exit();
    }

    // Lấy danh sách ngành học
    $nganhHocStmt = $pdo->query("SELECT MaNganh, TenNganh FROM NganhHoc ORDER BY TenNganh");
    $nganhHocList = $nganhHocStmt->fetchAll();

} catch (PDOException $e) {
    error_log("Student Edit Fetch Error: " . $e->getMessage());
    die("Lỗi: Không thể tải thông tin sinh viên hoặc ngành học.");
}

include 'templates/header.php';
?>

<h2 class="mb-4">Hiệu chỉnh Thông tin Sinh viên: <?php echo htmlspecialchars($student['HoTen']); ?></h2>

<form action="student_update.php" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="masv" value="<?php echo htmlspecialchars($student['MaSV']); ?>">

    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="hoten" class="form-label">Họ Tên (*)</label>
            <input type="text" class="form-control" id="hoten" name="hoten" required value="<?php echo htmlspecialchars($student['HoTen']); ?>">
        </div>
        <div class="col-md-6 mb-3">
            <label for="gioitinh" class="form-label">Giới Tính</label>
            <select class="form-select" id="gioitinh" name="gioitinh">
                <option value="Nam" <?php echo ($student['GioiTinh'] == 'Nam') ? 'selected' : ''; ?>>Nam</option>
                <option value="Nữ" <?php echo ($student['GioiTinh'] == 'Nữ') ? 'selected' : ''; ?>>Nữ</option>
                <option value="Khác" <?php echo ($student['GioiTinh'] == 'Khác') ? 'selected' : ''; ?>>Khác</option>
            </select>
        </div>
    </div>
     <div class="row">
        <div class="col-md-6 mb-3">
            <label for="ngaysinh" class="form-label">Ngày Sinh</label>
            <input type="date" class="form-control" id="ngaysinh" name="ngaysinh" value="<?php echo htmlspecialchars($student['NgaySinh']); ?>">
        </div>
         <div class="col-md-6 mb-3">
            <label for="manganh" class="form-label">Ngành Học (*)</label>
            <select class="form-select" id="manganh" name="manganh" required>
                <option value="" disabled>-- Chọn Ngành Học --</option>
                <?php foreach ($nganhHocList as $nganh): ?>
                    <option value="<?php echo htmlspecialchars($nganh['MaNganh']); ?>" <?php echo ($student['MaNganh'] == $nganh['MaNganh']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($nganh['TenNganh']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
     <div class="row align-items-center">
         <div class="col-md-6 mb-3">
            <label for="hinh" class="form-label">Đổi Hình ảnh (Để trống nếu không đổi)</label>
            <input type="file" class="form-control" id="hinh" name="hinh" accept="image/*">
        </div>
        <div class="col-md-6 mb-3">
             <?php if (!empty($student['Hinh']) && file_exists($student['Hinh'])): ?>
                <label class="form-label">Hình hiện tại:</label><br>
                <img src="<?php echo htmlspecialchars($student['Hinh']); ?>" alt="Hình <?php echo htmlspecialchars($student['HoTen']); ?>" class="img-thumbnail student-img">
                <input type="hidden" name="current_hinh" value="<?php echo htmlspecialchars($student['Hinh']); ?>">
            <?php else: ?>
                 <small>Chưa có hình ảnh.</small>
            <?php endif; ?>
        </div>
    </div>
     <div class="row">
        <div class="col-md-6 mb-3">
            <label for="password" class="form-label">Mật khẩu mới (Để trống nếu không đổi)</label>
            <input type="password" class="form-control" id="password" name="password">
        </div>
         <div class="col-md-6 mb-3">
            <label for="password_confirm" class="form-label">Xác nhận Mật khẩu mới</label>
            <input type="password" class="form-control" id="password_confirm" name="password_confirm">
        </div>
    </div>

    <div class="mt-3">
        <button type="submit" class="btn btn-success">Cập nhật Sinh Viên</button>
        <a href="students_index.php" class="btn btn-secondary">Hủy bỏ</a>
    </div>
</form>

<?php include 'templates/footer.php'; ?>