<?php
// ---- Nhập mật khẩu bạn muốn hash vào đây ----
$myPassword = '123456';
// ------------------------------------------

$hashedPassword = password_hash($myPassword, PASSWORD_DEFAULT);

if ($hashedPassword === false) {
    echo "Có lỗi xảy ra khi hash mật khẩu!";
} else {
    echo "<h1>Tạo Hash Mật Khẩu</h1>";
    echo "<p>Mật khẩu gốc bạn chọn: <strong>" . htmlspecialchars($myPassword) . "</strong></p>";
    echo "<p>Chuỗi Hash tương ứng:</p>";
    echo "<textarea rows='3' cols='70' readonly onclick='this.select();' style='font-family: monospace;'>" . htmlspecialchars($hashedPassword) . "</textarea>";
    echo "<p><small>(Click vào ô chứa hash để chọn và copy)</small></p>";
    echo "<hr>";
    echo "<p><strong>Bước tiếp theo:</strong> Copy chuỗi hash ở trên và dùng phpMyAdmin để cập nhật cột 'Password' cho sinh viên bạn muốn trong bảng 'SinhVien'.</p>";
}

$sql = "SELECT MaHP, SoLuongDuKien FROM hocphan WHERE MaHP IN (...)";

$sql_update = "UPDATE hocphan SET SoLuongDuKien = SoLuongDuKien - 1 
               WHERE MaHP = :mahp AND SoLuongDuKien > 0";
?>