<?php
require_once 'includes/auth_check.php';
require_once 'includes/db_connection.php';

$cartItems = [];
$totalCredits = 0;
$cart = $_SESSION['cart'] ?? [];

if (!empty($cart)) {
    try {
        // Tạo chuỗi placeholder (?,?,?) cho câu lệnh IN
        $placeholders = implode(',', array_fill(0, count($cart), '?'));
        $sql = "SELECT MaHP, TenHP, SoTinChi FROM HocPhan WHERE MaHP IN ($placeholders)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($cart);
        $cartItems = $stmt->fetchAll();

        // Tính tổng số tín chỉ
        foreach ($cartItems as $item) {
            $totalCredits += $item['SoTinChi'];
        }
    } catch (PDOException $e) {
        error_log("Cart View Error: " . $e->getMessage());
        die("Lỗi: Không thể tải thông tin giỏ hàng.");
    }
}

include 'templates/header.php';
?>

<h2 class="mb-4">Giỏ hàng Đăng ký Học phần</h2>

<?php if (isset($_GET['message'])): ?>
    <div class="alert alert-<?php echo htmlspecialchars($_GET['type'] ?? 'info'); ?>" role="alert">
        <?php echo htmlspecialchars($_GET['message']); ?>
    </div>
<?php endif; ?>

<?php if (!empty($cartItems)): ?>
    <div class="table-responsive mb-4">
        <table class="table table-striped align-middle">
            <thead>
                <tr>
                    <th>Mã HP</th>
                    <th>Tên Học Phần</th>
                    <th>Số Tín Chỉ</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cartItems as $item): ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['MaHP']); ?></td>
                    <td><?php echo htmlspecialchars($item['TenHP']); ?></td>
                    <td><?php echo htmlspecialchars($item['SoTinChi']); ?></td>
                    <td>
                        <a href="cart_remove.php?id=<?php echo htmlspecialchars($item['MaHP']); ?>" class="btn btn-danger btn-sm">Xóa</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                 <tr>
                    <td colspan="2" class="text-end"><strong>Tổng số học phần:</strong></td>
                    <td><strong><?php echo count($cartItems); ?></strong></td>
                    <td></td>
                </tr>
                <tr>
                    <td colspan="2" class="text-end"><strong>Tổng số tín chỉ:</strong></td>
                    <td><strong><?php echo $totalCredits; ?></strong></td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="d-flex justify-content-between">
         <a href="cart_clear.php" class="btn btn-warning" onclick="return confirm('Bạn có chắc muốn xóa tất cả học phần đã chọn?');">Xóa hết Đăng ký</a>
         <a href="registration_save.php" class="btn btn-success">Lưu Đăng ký</a>
    </div>

<?php else: ?>
    <p>Giỏ hàng đăng ký của bạn đang trống.</p>
    <a href="courses.php" class="btn btn-primary">Xem Danh sách Học phần</a>
<?php endif; ?>


<?php include 'templates/footer.php'; ?>