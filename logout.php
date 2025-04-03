<?php
// Bắt đầu session nếu chưa bắt đầu
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Xóa tất cả các biến session
$_SESSION = array();

// Hủy session cookie nếu tồn tại
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Hủy session
session_destroy();

// Chuyển hướng về trang đăng nhập
header('Location: login.php');
exit();
?>
