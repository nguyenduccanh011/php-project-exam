<?php
require_once 'includes/auth_check.php'; // Đảm bảo đã đăng nhập

$maHPToAdd = $_GET['id'] ?? null;

if (!$maHPToAdd) {
    header('Location: courses.php?message=Mã học phần không hợp lệ.&type=danger');
    exit();
}

// Khởi tạo giỏ hàng nếu chưa có
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Kiểm tra xem học phần đã tồn tại trong giỏ chưa
if (!in_array($maHPToAdd, $_SESSION['cart'])) {
    // Thêm học phần vào giỏ hàng
    $_SESSION['cart'][] = $maHPToAdd;
    $message = "Đã thêm học phần vào danh sách đăng ký.";
    $type = "success";
} else {
    $message = "Học phần này đã có trong danh sách đăng ký.";
    $type = "warning";
}

// Chuyển hướng về trang danh sách học phần hoặc giỏ hàng
header('Location: courses.php?message=' . urlencode($message) . '&type=' . $type);
// Hoặc chuyển về giỏ hàng: header('Location: cart_view.php');
exit();
?>