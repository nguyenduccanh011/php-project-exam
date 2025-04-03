<?php
require_once 'includes/db_connection.php';

try {
    // Tắt kiểm tra khóa ngoại tạm thời
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");

    // Xóa các ràng buộc khóa ngoại cũ
    $sql = "ALTER TABLE dangky 
            DROP FOREIGN KEY IF EXISTS dangky_ibfk_1";
    $pdo->exec($sql);
    echo "Đã xóa ràng buộc khóa ngoại cũ của bảng dangky<br>";

    $sql = "ALTER TABLE chitietdangky 
            DROP FOREIGN KEY IF EXISTS chitietdangky_ibfk_1,
            DROP FOREIGN KEY IF EXISTS chitietdangky_ibfk_2";
    $pdo->exec($sql);
    echo "Đã xóa ràng buộc khóa ngoại cũ của bảng chitietdangky<br>";

    // Sửa cấu trúc bảng dangky
    $sql = "ALTER TABLE dangky 
            MODIFY MaSV char(10) NOT NULL,
            MODIFY NgayDK DATETIME NOT NULL,
            MODIFY TrangThai tinyint(4) NOT NULL DEFAULT 1";
    $pdo->exec($sql);
    echo "Đã sửa cấu trúc bảng dangky thành công!<br>";

    // Thêm lại các ràng buộc khóa ngoại
    $sql = "ALTER TABLE dangky
            ADD CONSTRAINT fk_dangky_sinhvien
            FOREIGN KEY (MaSV) REFERENCES sinhvien(MaSV)";
    $pdo->exec($sql);
    echo "Đã thêm ràng buộc khóa ngoại cho bảng dangky thành công!<br>";

    $sql = "ALTER TABLE chitietdangky
            ADD CONSTRAINT fk_chitiet_dangky
            FOREIGN KEY (MaDK) REFERENCES dangky(MaDK)
            ON DELETE CASCADE,
            ADD CONSTRAINT fk_chitiet_hocphan
            FOREIGN KEY (MaHP) REFERENCES hocphan(MaHP)";
    $pdo->exec($sql);
    echo "Đã thêm ràng buộc khóa ngoại cho bảng chitietdangky thành công!<br>";

    // Bật lại kiểm tra khóa ngoại
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
    echo "Hoàn tất cập nhật cấu trúc database!";

} catch (PDOException $e) {
    // Đảm bảo bật lại kiểm tra khóa ngoại ngay cả khi có lỗi
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
    echo "Lỗi: " . $e->getMessage();
}
?> 