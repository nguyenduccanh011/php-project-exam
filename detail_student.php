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

// Lấy thông tin chi tiết sinh viên
try {
    $sql = "SELECT s.*, n.TenNganh 
            FROM sinhvien s 
            LEFT JOIN nganhhoc n ON s.MaNganh = n.MaNganh 
            WHERE s.MaSV = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);
    $student = $stmt->fetch();
    
    if (!$student) {
        header('Location: students.php');
        exit();
    }
} catch (PDOException $e) {
    die("Lỗi truy vấn: " . $e->getMessage());
}

include 'templates/header.php';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <h2>CHI TIẾT SINH VIÊN</h2>
            
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <!-- Cột bên trái cho thông tin -->
                        <div class="col-md-8">
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 200px;">Mã sinh viên:</th>
                                    <td><?php echo htmlspecialchars($student['MaSV']); ?></td>
                                </tr>
                                <tr>
                                    <th>Họ tên:</th>
                                    <td><?php echo htmlspecialchars($student['HoTen']); ?></td>
                                </tr>
                                <tr>
                                    <th>Giới tính:</th>
                                    <td><?php echo htmlspecialchars($student['GioiTinh']); ?></td>
                                </tr>
                                <tr>
                                    <th>Ngày sinh:</th>
                                    <td><?php echo htmlspecialchars($student['NgaySinh']); ?></td>
                                </tr>
                                <tr>
                                    <th>Ngành học:</th>
                                    <td>
                                        <?php echo htmlspecialchars($student['MaNganh']); ?> - 
                                        <?php echo htmlspecialchars($student['TenNganh'] ?? ''); ?>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        
                        <!-- Cột bên phải cho hình ảnh -->
                        <div class="col-md-4 text-center">
                            <?php if (!empty($student['Hinh']) && file_exists($student['Hinh'])): ?>
                                <img src="<?php echo htmlspecialchars($student['Hinh']); ?>" 
                                     alt="Hình sinh viên" 
                                     class="img-fluid rounded" 
                                     style="max-width: 300px;">
                            <?php else: ?>
                                <div class="alert alert-info">
                                    Không có hình ảnh
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-3">
                <a href="edit_student.php?id=<?php echo urlencode($student['MaSV']); ?>" class="btn btn-primary">Sửa</a>
                <a href="students.php" class="btn btn-secondary">Quay lại danh sách</a>
                <a href="delete_student.php?id=<?php echo urlencode($student['MaSV']); ?>" 
                   class="btn btn-danger"
                   onclick="return confirm('Bạn có chắc muốn xóa sinh viên này?')">Xóa</a>
            </div>
        </div>
    </div>
</div>

<?php include 'templates/footer.php'; ?> 