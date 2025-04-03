<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Xóa giỏ đăng ký
unset($_SESSION['cart']);

// Thông báo thành công
$_SESSION['message'] = 'Đã xóa hết học phần khỏi giỏ đăng ký!';
$_SESSION['message_type'] = 'success';

// Quay lại trang giỏ đăng ký
header('Location: cart.php');
exit(); 