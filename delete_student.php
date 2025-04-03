<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'includes/db_connection.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: students.php');
    exit();
}

try {
    // Lấy thông tin sinh viên để xóa hình (nếu có)
    $sql = "SELECT Hinh FROM sinhvien WHERE MaSV = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);
    $student = $stmt->fetch();
    
    if ($student) {
        // Xóa file hình (nếu có)
        if (!empty($student['Hinh']) && file_exists($student['Hinh'])) {
            unlink($student['Hinh']);
        }
        
        // Xóa sinh viên khỏi CSDL
        $sql = "DELETE FROM sinhvien WHERE MaSV = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
    }
    
    header('Location: students.php');
    exit();
} catch (PDOException $e) {
    die("Lỗi xóa sinh viên: " . $e->getMessage());
} 