<?php
require_once 'includes/auth_check.php';

$maHPToRemove = $_GET['id'] ?? null;
$return_to = $_GET['return'] ?? 'cart'; // Lấy trang để quay lại (courses hoặc cart)

if ($maHPToRemove && isset($_SESSION['cart'])) {
    // Tìm key của học phần trong mảng session
    $key = array_search($maHPToRemove, $_SESSION['cart']);
    if ($key !== false) {
        // Xóa học phần khỏi mảng
        unset($_SESSION['cart'][$key]);
        // Re-index mảng (optional)
        $_SESSION['cart'] = array_values($_SESSION['cart']);
        $message = "Đã xóa học phần khỏi danh sách.";
        $type = "success";
    } else {
         $message = "Không tìm thấy học phần trong danh sách.";
         $type = "warning";
    }
} else {
     $message = "Yêu cầu không hợp lệ.";
     $type = "danger";
}

// Chuyển hướng về trang giỏ hàng hoặc trang courses
$redirect_url = ($return_to == 'courses') ? 'courses.php' : 'cart_view.php';
header('Location: ' . $redirect_url . '?message=' . urlencode($message) . '&type=' . $type);
exit();
?>