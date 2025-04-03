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
$mahp = $_GET['mahp'] ?? '';

if (empty($mahp)) {
    header('Location: cart.php');
    exit();
}

try {
    // Kiểm tra xem học phần có trong giỏ không
    if (!isset($_SESSION['cart']) || !in_array($mahp, $_SESSION['cart'])) {
        throw new Exception('Học phần không có trong giỏ đăng ký!');
    }

    // Xóa học phần khỏi giỏ
    $_SESSION['cart'] = array_diff($_SESSION['cart'], [$mahp]);
    
    $_SESSION['message'] = 'Đã xóa học phần khỏi giỏ đăng ký.';
    $_SESSION['message_type'] = 'success';
} catch (Exception $e) {
    $_SESSION['message'] = 'Lỗi: ' . $e->getMessage();
    $_SESSION['message_type'] = 'danger';
}

header('Location: cart.php');
exit(); 