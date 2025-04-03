<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include 'templates/header.php';
?>

<div class="container mt-4">
    <div class="card">
        <div class="card-body text-center">
            <h3 class="text-danger mb-4">LỖI ĐĂNG KÝ</h3>
            <p><?php echo isset($_SESSION['message']) ? $_SESSION['message'] : 'Có lỗi xảy ra trong quá trình đăng ký!'; ?></p>
            <div class="mt-4">
                <a href="cart.php" class="btn btn-primary">Quay lại giỏ đăng ký</a>
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