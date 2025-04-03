<?php
require_once 'includes/auth_check.php';
require_once 'includes/db_connection.php';

// Lấy danh sách ngành học để tạo dropdown
try {
    $nganhHocStmt = $pdo->query("SELECT MaNganh, TenNganh FROM NganhHoc ORDER BY TenNganh");
    $nganhHocList = $nganhHocStmt->fetchAll();
} catch (PDOException $e) {
    error_log("Fetch NganhHoc Error: " . $e->getMessage());
    die("Lỗi: Không thể tải danh sách ngành học.");
}

include 'templates/header.php';
?>

<h2 class="mb-4">Thêm Sinh Viên Mới</h2>

<form action="student_store.php" method="POST" enctype="multipart/form-data">
    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="masv" class="form-label">Mã Sinh Viên (*)</label>
            <input type="text" class="form-control" id="masv" name="masv" required maxlength="10">
        </div>
        <div class="col-md-6 mb-3">
            <label for="hoten" class="form-label">Họ Tên (*)</label>
            <input type="text" class="form-control" id="hoten" name="hoten" required>
        </div>
    </div>
    <div class="row">
         <div class="col-md-6 mb-3">
            <label for="gioitinh" class="form-label">Giới Tính</label>
            <select class="form-select" id="gioitinh" name="gioitinh">
                <option value="Nam" selected>Nam</option>
                <option value="Nữ">Nữ</option>
                <option value="Khác">Khác</option>
            </select>
        </div>
        <div class="col-md-6 mb-3">
            <label for="ngaysinh" class="form-label">Ngày Sinh</label>
            <input type="date" class="form-control" id="ngaysinh" name="ngaysinh">
        </div>
    </div>
     <div class="row">
        <div class="col-md-6 mb-3">
            <label for="hinh" class="form-label">Hình ảnh</label>
            <input type="file" class="form-control" id="hinh" name="hinh" accept="image/*">
        </div>
         <div class="col-md-6 mb-3">
            <label for="manganh" class="form-label">Ngành Học (*)</label>
            <select class="form-select" id="manganh" name="manganh" required>
                <option value="" disabled selected>-- Chọn Ngành Học --</option>
                <?php foreach ($nganhHocList as $nganh): ?>
                    <option value="<?php echo htmlspecialchars($nganh['MaNganh']); ?>">
                        <?php echo htmlspecialchars($nganh['TenNganh']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="password" class="form-label">Mật khẩu (*)</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
         <div class="col-md-6 mb-3">
            <label for="password_confirm" class="form-label">Xác nhận Mật khẩu (*)</label>
            <input type="password" class="form-control" id="password_confirm" name="password_confirm" required>
        </div>
    </div>

    <div class="mt-3">
        <button type="submit" class="btn btn-success">Lưu Sinh Viên</button>
        <a href="students_index.php" class="btn btn-secondary">Hủy bỏ</a>
    </div>
</form>

<?php include 'templates/footer.php'; ?>