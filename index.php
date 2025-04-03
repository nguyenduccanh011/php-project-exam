<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['user_id'])) {
    // Nếu đã đăng nhập, chuyển đến trang courses
    header('Location: courses.php');
    exit();
} else {
    // Nếu chưa đăng nhập, chuyển đến trang login
    header('Location: login.php');
    exit();
}

// Hoặc bạn có thể hiển thị một trang chào mừng tại đây
// include 'templates/header.php';
// echo "<h1>Chào mừng đến với Website Đăng Ký Học Phần</h1>";
// echo '<p><a href="login.php">Đăng nhập</a> để bắt đầu.</p>';
// include 'templates/footer.php';
?>