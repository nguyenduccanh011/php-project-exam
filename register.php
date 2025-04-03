<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Nếu đã đăng nhập thì chuyển về trang chủ
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

require_once 'includes/db_connection.php';

$error = '';
$success = false;

// Xử lý form đăng ký
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $masv = trim($_POST['masv'] ?? '');
    $hoten = trim($_POST['hoten'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');
    $ngaysinh = trim($_POST['ngaysinh'] ?? '');
    $manganh = trim($_POST['manganh'] ?? '');

    // Validate dữ liệu
    if (empty($masv) || empty($hoten) || empty($password) || empty($confirm_password) || empty($ngaysinh) || empty($manganh)) {
        $error = 'Vui lòng điền đầy đủ thông tin!';
    } elseif ($password !== $confirm_password) {
        $error = 'Mật khẩu xác nhận không khớp!';
    } else {
        try {
            // Kiểm tra MSSV đã tồn tại chưa
            $stmt = $pdo->prepare("SELECT MaSV FROM sinhvien WHERE MaSV = ?");
            $stmt->execute([$masv]);
            if ($stmt->rowCount() > 0) {
                $error = 'Mã số sinh viên đã tồn tại!';
            } else {
                // Kiểm tra mã ngành có tồn tại không
                $stmt = $pdo->prepare("SELECT MaNganh FROM nganhhoc WHERE MaNganh = ?");
                $stmt->execute([$manganh]);
                if ($stmt->rowCount() == 0) {
                    $error = 'Mã ngành không tồn tại!';
                } else {
                    // Thêm sinh viên mới - không mã hóa mật khẩu
                    $stmt = $pdo->prepare("INSERT INTO sinhvien (MaSV, HoTen, Password, NgaySinh, MaNganh) VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute([$masv, $hoten, $password, $ngaysinh, $manganh]);
                    
                    $success = true;
                    $_SESSION['message'] = 'Đăng ký tài khoản thành công! Vui lòng đăng nhập.';
                    $_SESSION['message_type'] = 'success';
                    header('Location: login.php');
                    exit();
                }
            }
        } catch (PDOException $e) {
            $error = 'Có lỗi xảy ra: ' . $e->getMessage();
        }
    }
}

// Lấy danh sách ngành học
try {
    $stmt = $pdo->query("SELECT * FROM nganhhoc ORDER BY TenNganh");
    $nganhhoc = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = 'Không thể lấy danh sách ngành học: ' . $e->getMessage();
}

include 'templates/header.php';
?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0 text-center">
                        <i class="fas fa-user-plus me-2"></i>
                        Đăng ký tài khoản
                    </h5>
                </div>
                <div class="card-body">
                    <?php if ($error): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <?php echo $error; ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="" class="needs-validation" novalidate>
                        <div class="mb-3">
                            <label for="masv" class="form-label">Mã số sinh viên</label>
                            <input type="text" class="form-control" id="masv" name="masv" 
                                   value="<?php echo htmlspecialchars($_POST['masv'] ?? ''); ?>" required>
                            <div class="invalid-feedback">Vui lòng nhập mã số sinh viên</div>
                        </div>

                        <div class="mb-3">
                            <label for="hoten" class="form-label">Họ và tên</label>
                            <input type="text" class="form-control" id="hoten" name="hoten" 
                                   value="<?php echo htmlspecialchars($_POST['hoten'] ?? ''); ?>" required>
                            <div class="invalid-feedback">Vui lòng nhập họ tên</div>
                        </div>

                        <div class="mb-3">
                            <label for="ngaysinh" class="form-label">Ngày sinh</label>
                            <input type="date" class="form-control" id="ngaysinh" name="ngaysinh" 
                                   value="<?php echo htmlspecialchars($_POST['ngaysinh'] ?? ''); ?>" required>
                            <div class="invalid-feedback">Vui lòng chọn ngày sinh</div>
                        </div>

                        <div class="mb-3">
                            <label for="manganh" class="form-label">Ngành học</label>
                            <select class="form-select" id="manganh" name="manganh" required>
                                <option value="">Chọn ngành học</option>
                                <?php foreach ($nganhhoc as $nganh): ?>
                                    <option value="<?php echo htmlspecialchars($nganh['MaNganh']); ?>"
                                            <?php echo (isset($_POST['manganh']) && $_POST['manganh'] == $nganh['MaNganh']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($nganh['TenNganh']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">Vui lòng chọn ngành học</div>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Mật khẩu</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                            <div class="invalid-feedback">Vui lòng nhập mật khẩu</div>
                        </div>

                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Xác nhận mật khẩu</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                            <div class="invalid-feedback">Vui lòng xác nhận mật khẩu</div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-user-plus me-2"></i>
                                Đăng ký
                            </button>
                            <a href="login.php" class="btn btn-light">
                                <i class="fas fa-sign-in-alt me-2"></i>
                                Đã có tài khoản? Đăng nhập
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Form validation script -->
<script>
(function () {
    'use strict'
    var forms = document.querySelectorAll('.needs-validation')
    Array.prototype.slice.call(forms).forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault()
                event.stopPropagation()
            }
            form.classList.add('was-validated')
        }, false)
    })
})()
</script>

<?php include 'templates/footer.php'; ?> 