<?php
require_once 'includes/auth_check.php';

// Xóa giỏ hàng
if (isset($_SESSION['cart'])) {
    unset($_SESSION['cart']);
}

// Chuyển hướng về trang giỏ hàng với thông báo
header('Location: cart_view.php?message=Đã xóa tất cả học phần khỏi danh sách đăng ký.&type=success');
exit();
?>