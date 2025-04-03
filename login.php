<?php
// Luôn bắt đầu session ở đầu các file cần dùng session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Nếu người dùng đã đăng nhập (có user_id trong session), chuyển hướng họ
if (isset($_SESSION['user_id'])) {
    header('Location: courses.php'); // Chuyển đến trang danh sách học phần (hoặc trang chính khác)
    exit(); // Dừng thực thi script sau khi chuyển hướng
}

require_once 'includes/db_connection.php'; // Nhúng file kết nối CSDL

$error_message = ''; // Biến lưu lỗi đăng nhập sai
$message = $_GET['message'] ?? null; // Lấy thông báo từ URL (nếu có)
$message_type = $_GET['type'] ?? 'info'; // Lấy loại thông báo (ví dụ: success, warning, danger)

// Chỉ xử lý nếu form được gửi đi bằng phương thức POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy dữ liệu từ form, trim() để loại bỏ khoảng trắng thừa
    $maSV = trim($_POST['username'] ?? ''); // Lấy MaSV từ input có name="username"
    $password = trim($_POST['password'] ?? ''); // Lấy mật khẩu từ input có name="password"

    // --- Validate đầu vào cơ bản ---
    if (empty($maSV) || empty($password)) {
        $error_message = 'Vui lòng nhập Mã sinh viên và Mật khẩu.';
    } else {
        // --- Nếu đầu vào hợp lệ, kiểm tra với CSDL ---
        try {
            // Chuẩn bị câu lệnh SQL với placeholder để tránh SQL Injection
            $sql = "SELECT MaSV, HoTen, Password FROM SinhVien WHERE MaSV = :masv";
            $stmt = $pdo->prepare($sql);

            // Thực thi câu lệnh, truyền giá trị MaSV vào placeholder
            $stmt->execute([':masv' => $maSV]);

            // Lấy dữ liệu của người dùng (nếu tìm thấy)
            $user = $stmt->fetch();

            // Kiểm tra người dùng có tồn tại và mật khẩu có khớp không
            if ($user && $password === $user['Password']) {
                // --- Đăng nhập thành công ---
                $_SESSION['user_id'] = $user['MaSV'];
                $_SESSION['user_name'] = $user['HoTen'];

                // Chuyển hướng đến trang chính sau khi đăng nhập
                header('Location: courses.php');
                exit();
            } else {
                // --- Sai Mã SV hoặc Mật khẩu ---
                $error_message = 'Mã sinh viên hoặc Mật khẩu không đúng.';
            }
        } catch (PDOException $e) {
            // --- Lỗi trong quá trình truy vấn CSDL ---
            error_log("Login Error: " . $e->getMessage());
            $error_message = 'Có lỗi xảy ra trong quá trình đăng nhập. Vui lòng thử lại.';
        }
    }
}

include 'templates/header.php'; // Nhúng phần đầu trang HTML và Navbar
?>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-4">
        <h2 class="text-center mb-4">Đăng nhập</h2>

        <?php // Hiển thị thông báo từ GET (ví dụ: sau khi logout)
        if ($message): ?>
            <div class="alert alert-<?php echo htmlspecialchars($message_type); ?>" role="alert">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <?php // Hiển thị lỗi đăng nhập sai
        if ($error_message): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <form action="login.php" method="POST">
                    <div class="mb-3">
                        <label for="username" class="form-label">Mã Sinh Viên (Username)</label>
                        <input type="text" class="form-control" id="username" name="username" required value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Mật khẩu</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Đăng nhập</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
include 'templates/footer.php'; // Nhúng phần chân trang HTML
?>