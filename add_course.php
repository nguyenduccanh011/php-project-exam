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
    header('Location: courses.php');
    exit();
}

try {
    // Kiểm tra học phần có tồn tại không
    $sql = "SELECT MaHP FROM hocphan WHERE MaHP = :mahp";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':mahp' => $mahp]);
    
    if (!$stmt->fetch()) {
        throw new Exception('Học phần không tồn tại!');
    }

    // Khởi tạo giỏ đăng ký nếu chưa có
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Kiểm tra học phần đã có trong giỏ chưa
    if (in_array($mahp, $_SESSION['cart'])) {
        throw new Exception('Học phần đã có trong giỏ đăng ký!');
    }

    // Thêm học phần vào giỏ
    $_SESSION['cart'][] = $mahp;
    
    $_SESSION['message'] = 'Đã thêm học phần vào giỏ đăng ký.';
    $_SESSION['message_type'] = 'success';
} catch (Exception $e) {
    $_SESSION['message'] = 'Lỗi: ' . $e->getMessage();
    $_SESSION['message_type'] = 'danger';
}

header('Location: courses.php');
exit(); 