<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'includes/db_connection.php';

// Bật hiển thị lỗi chi tiết
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$masv = $_SESSION['user_id'];

try {
    // Bắt đầu transaction
    $pdo->beginTransaction();

    // Debug: In ra thông tin session
    echo "<pre>Debug: Session Info\n";
    echo "User ID: " . $masv . "\n";
    echo "Cart: "; print_r($_SESSION['cart']);
    echo "</pre>";

    // Kiểm tra giỏ đăng ký
    if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
        throw new Exception("Không có học phần nào trong giỏ đăng ký!");
    }

    // Kiểm tra số lượng dự kiến còn đủ không
    $sql = "SELECT MaHP, SoLuongDuKien FROM hocphan WHERE MaHP IN (" . str_repeat('?,', count($_SESSION['cart']) - 1) . '?)';
    $stmt = $pdo->prepare($sql);
    $stmt->execute($_SESSION['cart']);
    $hocphan_info = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($hocphan_info as $hp) {
        if ($hp['SoLuongDuKien'] <= 0) {
            throw new Exception("Học phần " . $hp['MaHP'] . " đã hết slot đăng ký!");
        }
    }

    // Tạo bản ghi mới trong bảng dangky
    $sql = "INSERT INTO dangky (MaSV, NgayDK, TrangThai) VALUES (:masv, NOW(), 1)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':masv' => $masv]);
    $madk = $pdo->lastInsertId();
    
    echo "<pre>Debug: Đã tạo đăng ký mới với MaDK = " . $madk . "</pre>";

    // Thêm chi tiết đăng ký và cập nhật số lượng dự kiến
    $sql_insert = "INSERT INTO chitietdangky (MaDK, MaHP) VALUES (:madk, :mahp)";
    $stmt_insert = $pdo->prepare($sql_insert);
    
    $sql_update = "UPDATE hocphan SET SoLuongDuKien = SoLuongDuKien - 1 WHERE MaHP = :mahp AND SoLuongDuKien > 0";
    $stmt_update = $pdo->prepare($sql_update);
    
    foreach ($_SESSION['cart'] as $mahp) {
        try {
            // Thêm chi tiết đăng ký
            $stmt_insert->execute([
                ':madk' => $madk,
                ':mahp' => $mahp
            ]);
            
            // Cập nhật số lượng dự kiến
            $stmt_update->execute([':mahp' => $mahp]);
            
            if ($stmt_update->rowCount() == 0) {
                throw new Exception("Không thể cập nhật số lượng cho học phần " . $mahp);
            }
            
            echo "<pre>Debug: Đã thêm và cập nhật số lượng học phần " . $mahp . "</pre>";
        } catch (PDOException $e) {
            throw new Exception("Lỗi khi xử lý học phần " . $mahp . ": " . $e->getMessage());
        }
    }

    // Xóa giỏ đăng ký sau khi đã lưu thành công
    unset($_SESSION['cart']);
    
    // Commit transaction
    $pdo->commit();
    
    $_SESSION['message'] = 'Đăng ký học phần thành công!';
    $_SESSION['message_type'] = 'success';
    
    // Chuyển hướng về trang thông báo thành công
    header("Location: registration_success.php");
    exit();

} catch (Exception $e) {
    // Rollback nếu có lỗi
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    echo "<pre>Debug: Error Details\n";
    echo "Error Message: " . $e->getMessage() . "\n";
    echo "Stack Trace:\n" . $e->getTraceAsString() . "\n";
    echo "</pre>";
    
    $_SESSION['message'] = 'Có lỗi xảy ra: ' . $e->getMessage();
    $_SESSION['message_type'] = 'danger';
    
    // Chuyển hướng về trang thông báo lỗi
    header("Location: registration_error.php");
    exit();
}
?> 