<?php
// Luôn bắt đầu session ở đầu các file cần dùng session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Kiểm tra nếu người dùng chưa đăng nhập, chuyển hướng về trang đăng nhập
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

require_once 'includes/db_connection.php';

// Lấy thông tin người dùng
$masv = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

// Lấy số học phần trong giỏ
$cart_count = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;

// Lấy danh sách học phần
try {
    $sql = "SELECT h.* FROM hocphan h ORDER BY h.MaHP";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $courses = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Lỗi truy vấn: " . $e->getMessage());
}

// Hiển thị thông báo nếu có
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    $message_type = $_SESSION['message_type'];
    unset($_SESSION['message']);
    unset($_SESSION['message_type']);
}

include 'templates/header.php';
?>

<div class="container py-4">
    <?php if (isset($message)): ?>
    <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
        <?php echo $message; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-book me-2"></i>
                    Danh sách học phần
                </h5>
                <a href="cart.php" class="btn btn-primary">
                    <i class="fas fa-shopping-cart me-1"></i>
                    Giỏ đăng ký
                    <?php if ($cart_count > 0): ?>
                        <span class="badge bg-light text-primary ms-1"><?php echo $cart_count; ?></span>
                    <?php endif; ?>
                </a>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Mã HP</th>
                            <th>Tên học phần</th>
                            <th class="text-center">Số tín chỉ</th>
                            <th class="text-center">Số lượng dự kiến</th>
                            <th class="text-end">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($courses as $course): ?>
                        <tr>
                            <td class="font-monospace"><?php echo htmlspecialchars($course['MaHP']); ?></td>
                            <td><?php echo htmlspecialchars($course['TenHP']); ?></td>
                            <td class="text-center"><?php echo htmlspecialchars($course['SoTinChi']); ?></td>
                            <td class="text-center">
                                <?php if ($course['SoLuongDuKien'] > 0): ?>
                                    <span class="badge bg-success">
                                        <?php echo htmlspecialchars($course['SoLuongDuKien']); ?> slot
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Hết slot</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <?php if ($course['SoLuongDuKien'] <= 0): ?>
                                    <button class="btn btn-sm btn-secondary" disabled>
                                        <i class="fas fa-ban me-1"></i>
                                        Hết slot
                                    </button>
                                <?php elseif (isset($_SESSION['cart']) && in_array($course['MaHP'], $_SESSION['cart'])): ?>
                                    <a href="remove_course.php?mahp=<?php echo urlencode($course['MaHP']); ?>" 
                                       class="btn btn-sm btn-danger"
                                       data-bs-toggle="tooltip"
                                       title="Xóa khỏi giỏ đăng ký">
                                        <i class="fas fa-trash-alt me-1"></i>
                                        Xóa
                                    </a>
                                <?php else: ?>
                                    <a href="add_course.php?mahp=<?php echo urlencode($course['MaHP']); ?>" 
                                       class="btn btn-sm btn-success"
                                       data-bs-toggle="tooltip"
                                       title="Thêm vào giỏ đăng ký">
                                        <i class="fas fa-plus me-1"></i>
                                        Đăng ký
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'templates/footer.php'; ?>
