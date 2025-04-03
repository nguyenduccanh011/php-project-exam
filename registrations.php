<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

require_once 'includes/db_connection.php';

$masv = $_SESSION['user_id'];

try {
    // Lấy thông tin sinh viên
    $sql = "SELECT sv.*, n.TenNganh 
            FROM sinhvien sv 
            LEFT JOIN nganhhoc n ON sv.MaNganh = n.MaNganh 
            WHERE sv.MaSV = :masv";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':masv' => $masv]);
    $student = $stmt->fetch();

    // Lấy danh sách học phần đã đăng ký
    $sql = "SELECT hp.*, dk.NgayDK, dk.TrangThai
            FROM dangky dk 
            JOIN chitietdangky ct ON dk.MaDK = ct.MaDK
            JOIN hocphan hp ON ct.MaHP = hp.MaHP
            WHERE dk.MaSV = :masv
            ORDER BY dk.NgayDK DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':masv' => $masv]);
    $registrations = $stmt->fetchAll();

    // Tính tổng số tín chỉ
    $total_credits = 0;
    foreach ($registrations as $reg) {
        $total_credits += $reg['SoTinChi'];
    }

} catch (PDOException $e) {
    die("Lỗi truy vấn: " . $e->getMessage());
}

include 'templates/header.php';
?>

<div class="container py-4">
    <div class="row">
        <!-- Thông tin sinh viên -->
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user-graduate me-2"></i>
                        Thông tin sinh viên
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <td><strong>MSSV:</strong></td>
                            <td><?php echo htmlspecialchars($student['MaSV']); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Họ tên:</strong></td>
                            <td><?php echo htmlspecialchars($student['HoTen']); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Ngành:</strong></td>
                            <td><?php echo htmlspecialchars($student['TenNganh']); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Tổng TC:</strong></td>
                            <td><span class="badge bg-primary"><?php echo $total_credits; ?> tín chỉ</span></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Danh sách học phần đã đăng ký -->
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-clipboard-list me-2"></i>
                        Danh sách học phần đã đăng ký
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Mã HP</th>
                                    <th>Tên học phần</th>
                                    <th class="text-center">Số TC</th>
                                    <th class="text-center">Ngày ĐK</th>
                                    <th class="text-center">Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($registrations)): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        <i class="fas fa-info-circle me-2"></i>
                                        Chưa có học phần nào được đăng ký
                                    </td>
                                </tr>
                                <?php else: ?>
                                    <?php foreach ($registrations as $reg): ?>
                                    <tr>
                                        <td class="font-monospace"><?php echo htmlspecialchars($reg['MaHP']); ?></td>
                                        <td><?php echo htmlspecialchars($reg['TenHP']); ?></td>
                                        <td class="text-center"><?php echo htmlspecialchars($reg['SoTinChi']); ?></td>
                                        <td class="text-center">
                                            <?php echo date('d/m/Y', strtotime($reg['NgayDK'])); ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($reg['TrangThai'] == 1): ?>
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check me-1"></i> Đã xác nhận
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-warning">
                                                    <i class="fas fa-clock me-1"></i> Chờ xác nhận
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'templates/footer.php'; ?> 