<?php
require_once 'includes/auth_check.php';
require_once 'includes/db_connection.php';

$cart = $_SESSION['cart'] ?? [];
$maSV = $_SESSION['user_id']; // Lấy MaSV từ session

if (empty($cart)) {
    header('Location: cart_view.php?message=Giỏ hàng trống, không thể lưu đăng ký.&type=warning');
    exit();
}

$ngayDK = date('Y-m-d');

// --- Bắt đầu Transaction ---
try {
    $pdo->beginTransaction();

    // --- Bước 1: Kiểm tra lại số lượng trước khi đăng ký ---
    $placeholders = implode(',', array_fill(0, count($cart), '?'));
    $sqlCheck = "SELECT MaHP, SoLuongDuKien FROM HocPhan WHERE MaHP IN ($placeholders) FOR UPDATE"; // FOR UPDATE để khóa dòng
    $stmtCheck = $pdo->prepare($sqlCheck);
    $stmtCheck->execute($cart);
    $availableCourses = $stmtCheck->fetchAll(PDO::FETCH_KEY_PAIR); // Lấy dạng MaHP => SoLuongDuKien

    foreach ($cart as $maHP) {
        if (!isset($availableCourses[$maHP]) || $availableCourses[$maHP] <= 0) {
            // Nếu có học phần hết chỗ, hủy transaction và báo lỗi
            throw new Exception("Học phần " . htmlspecialchars($maHP) . " đã hết chỗ hoặc không tồn tại.");
        }
    }

    // --- Bước 2: Thêm vào bảng DangKy ---
    $sqlDangKy = "INSERT INTO DangKy (NgayDK, MaSV) VALUES (?, ?)";
    $stmtDangKy = $pdo->prepare($sqlDangKy);
    $stmtDangKy->execute([$ngayDK, $maSV]);
    $maDK = $pdo->lastInsertId(); // Lấy MaDK vừa tạo

    // --- Bước 3: Thêm vào ChiTietDangKy và Cập nhật HocPhan ---
    $sqlChiTiet = "INSERT INTO ChiTietDangKy (MaDK, MaHP) VALUES (?, ?)";
    $stmtChiTiet = $pdo->prepare($sqlChiTiet);

    $sqlCapNhatHP = "UPDATE HocPhan SET SoLuongDuKien = SoLuongDuKien - 1 WHERE MaHP = ?"; // Không cần AND SoLuong > 0 vì đã check ở trên
    $stmtCapNhatHP = $pdo->prepare($sqlCapNhatHP);

    foreach ($cart as $maHP) {
        // Thêm chi tiết
        $stmtChiTiet->execute([$maDK, $maHP]);
        // Cập nhật số lượng
        $stmtCapNhatHP->execute([$maHP]);
        // Kiểm tra kỹ hơn nếu cần (ví dụ: $stmtCapNhatHP->rowCount() == 0 thì có vấn đề)
    }

    // --- Bước 4: Commit transaction nếu mọi thứ thành công ---
    $pdo->commit();

    // --- Bước 5: Xóa giỏ hàng khỏi session ---
    unset($_SESSION['cart']);

    // --- Bước 6: Chuyển hướng đến trang thành công ---
    header('Location: registration_success.php?maDK=' . $maDK);
    exit();

} catch (Exception $e) {
    // Rollback transaction nếu có lỗi
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log("Registration Save Error: " . $e->getMessage());
    // Chuyển hướng về giỏ hàng với thông báo lỗi
    header('Location: cart_view.php?message=' . urlencode("Đăng ký thất bại: " . $e->getMessage()) . '&type=danger');
    exit();
}
?>