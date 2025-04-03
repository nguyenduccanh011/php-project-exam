<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'includes/db_connection.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$masv = $_SESSION['user_id'];

// Lấy danh sách học phần từ session
$registered_courses = [];
$total_credits = 0;

if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    try {
        $placeholders = str_repeat('?,', count($_SESSION['cart']) - 1) . '?';
        $sql = "SELECT MaHP, TenHP, SoTinChi 
                FROM hocphan 
                WHERE MaHP IN ($placeholders)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($_SESSION['cart']);
        $registered_courses = $stmt->fetchAll();

        // Tính tổng số tín chỉ
        foreach ($registered_courses as $course) {
            $total_credits += $course['SoTinChi'];
        }
    } catch (PDOException $e) {
        die("Lỗi truy vấn: " . $e->getMessage());
    }
}

include 'templates/header.php';
?>

<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-shopping-cart me-2"></i>
                    Giỏ đăng ký học phần
                </h5>
                <?php if (count($registered_courses) > 0): ?>
                    <a href="clear_cart.php" 
                       class="btn btn-danger"
                       onclick="return confirm('Bạn có chắc muốn xóa hết học phần khỏi giỏ đăng ký?')">
                        <i class="fas fa-trash-alt me-2"></i>
                        Xóa hết
                    </a>
                <?php endif; ?>
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
                            <th class="text-end">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($registered_courses)): ?>
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">
                                    <i class="fas fa-shopping-cart me-2"></i>
                                    Chưa có học phần nào trong giỏ đăng ký
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($registered_courses as $course): ?>
                            <tr>
                                <td class="font-monospace"><?php echo htmlspecialchars($course['MaHP']); ?></td>
                                <td><?php echo htmlspecialchars($course['TenHP']); ?></td>
                                <td class="text-center"><?php echo htmlspecialchars($course['SoTinChi']); ?></td>
                                <td class="text-end">
                                    <a href="remove_course.php?mahp=<?php echo urlencode($course['MaHP']); ?>" 
                                       class="btn btn-sm btn-danger"
                                       onclick="return confirm('Bạn có chắc muốn xóa học phần này?')">
                                        <i class="fas fa-trash-alt me-1"></i>
                                        Xóa
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                    <?php if (!empty($registered_courses)): ?>
                    <tfoot>
                        <tr class="table-light">
                            <td colspan="2">
                                <strong>Tổng số học phần:</strong> 
                                <span class="badge bg-primary"><?php echo count($registered_courses); ?></span>
                            </td>
                            <td colspan="2" class="text-end">
                                <strong>Tổng số tín chỉ:</strong> 
                                <span class="badge bg-primary"><?php echo $total_credits; ?></span>
                            </td>
                        </tr>
                    </tfoot>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-4 d-flex gap-2">
        <a href="courses.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>
            Tiếp tục đăng ký
        </a>
        <?php if (count($registered_courses) > 0): ?>
            <a href="confirm_registration.php" class="btn btn-primary">
                <i class="fas fa-save me-2"></i>
                Lưu đăng ký
            </a>
        <?php endif; ?>
    </div>
</div>

<?php include 'templates/footer.php'; ?> 