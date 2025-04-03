<?php
require_once 'includes/db_connection.php';

try {
    // Kiểm tra bảng dangky
    $sql = "DESCRIBE dangky";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    echo "<h3>Cấu trúc bảng dangky:</h3>";
    echo "<pre>";
    print_r($stmt->fetchAll());
    echo "</pre>";

    // Kiểm tra bảng chitietdangky
    $sql = "DESCRIBE chitietdangky";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    echo "<h3>Cấu trúc bảng chitietdangky:</h3>";
    echo "<pre>";
    print_r($stmt->fetchAll());
    echo "</pre>";

    // Kiểm tra session và cart
    session_start();
    echo "<h3>Thông tin Session:</h3>";
    echo "<pre>";
    echo "User ID: " . ($_SESSION['user_id'] ?? 'Không có');
    echo "\nCart: ";
    print_r($_SESSION['cart'] ?? 'Không có giỏ hàng');
    echo "</pre>";

} catch (PDOException $e) {
    echo "Lỗi: " . $e->getMessage();
}
?> 