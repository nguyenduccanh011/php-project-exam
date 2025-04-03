<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include 'templates/header.php';
?>

<div class="container mt-4">
    <div class="card">
        <div class="card-body text-center">
            <h3 class="text-success mb-4">THÔNG TIN HỌC PHẦN ĐÃ LƯU</h3>
            <p>Bạn đã đăng ký học phần thành công!</p>
            <div class="mt-4">
                <a href="courses.php" class="btn btn-primary">Về trang chủ</a>
            </div>
        </div>
    </div>
</div>

<?php 
// Xóa message sau khi hiển thị
unset($_SESSION['message']);
unset($_SESSION['message_type']);

include 'templates/footer.php'; 
?>
