<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Nếu chưa đăng nhập, chuyển hướng về trang login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?message=Vui lòng đăng nhập để truy cập trang này.&type=warning');
    exit();
}
?>