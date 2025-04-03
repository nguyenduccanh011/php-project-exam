<?php
require_once 'includes/auth_check.php';
require_once 'includes/db_connection.php';

$maSV = $_GET['id'] ?? null;

if (!$maSV) {
    header('Location: students_index.php?message=Mã sinh viên không hợp lệ.&type=danger');
    exit();
}

try {
    // Lấy thông tin chi tiết sinh viên và ngành học
    $sql = "SELECT sv.*, nh.TenNganh
            FROM SinhVien sv
            LEFT JOIN NganhHoc nh ON sv.MaNganh = nh.MaNganh
            WHERE sv.MaSV = :masv";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':masv' => $maSV]);
    $student = $stmt->fetch();

    if (!$student) {
        header('Location: students_index.php?message=Không tìm thấy sinh viên.&type=warning');
        exit();
    }

    // Lấy lịch sử đăng ký học phần của sinh viên này (Ví dụ)
    $sqlDangKy = "SELECT dk.NgayDK, hp.MaHP, hp.TenHP, hp.SoTinChi
                  FROM DangKy dk
                  JOIN ChiTietDangKy ctdk ON dk.MaDK = ctdk.MaDK
                  JOIN HocPhan hp ON ctdk.MaHP = hp.MaHP
                  WHERE dk.MaSV = :masv
                  ORDER BY dk.NgayDK DESC, hp.TenHP ASC";
    $stmtDangKy = $pdo->prepare($sqlDangKy);
    $stmtDangKy->execute([':masv' => $maSV]);
    $registrations = $stmtDangKy->fetchAll();


} catch (PDOException $e) {
    error_log("Student Details Error: " . $e->getMessage());
    die("Lỗi: Không thể tải thông tin chi tiết sinh viên.");
}

include 'templates/header.php';
?>

<h2 class="mb-4">Thông tin chi tiết Sinh viên</h2>

<div class="card mb-4">
    <div class="card-header">
        Thông tin cá nhân
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4 text-center">
                 <?php if (!empty($student['Hinh']) && file_exists($student['Hinh'])): ?>
                    <img src="<?php echo htmlspecialchars($student['Hinh']); ?>" alt="Hình <?php echo htmlspecialchars($student['HoTen']); ?>" class="img-fluid img-thumbnail mb-3" style="max-height: 250px;">
                <?php else: ?>
                     <p class="text-muted">Không có hình ảnh</p>
                <?php endif; ?>
            </div>
            <div class="col-md-8">
                <dl class="row">
                    <dt class="col-sm-4">Mã Sinh Viên:</dt>
                    <dd class="col-sm-8"><?php echo htmlspecialchars($student['MaSV']); ?></dd>

                    <dt class="col-sm-4">Họ Tên:</dt>
                    <dd class="col-sm-8"><?php echo htmlspecialchars($student['HoTen']); ?></dd>

                    <dt class="col-sm-4">Giới Tính:</dt>
                    <dd class="col-sm-8"><?php echo htmlspecialchars($student['GioiTinh']); ?></dd>

                    <dt class="col-sm-4">Ngày Sinh:</dt>
                    <dd class="col-sm-8"><?php echo $student['NgaySinh'] ? htmlspecialchars(date('d/m/Y', strtotime($student['NgaySinh']))) : 'Chưa cập nhật'; ?></dd>

                     <dt class="col-sm-4">Ngành Học:</dt>
                    <dd class="col-sm-8"><?php echo htmlspecialchars($student['TenNganh'] ?? 'Chưa có'); ?></dd>
                </dl>
                 <a href="student_edit.php?id=<?php echo htmlspecialchars($student['MaSV']); ?>" class="btn btn-warning">Sửa thông tin</a>
                 <a href="students_index.php" class="btn btn-secondary">Quay lại Danh sách</a>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        Lịch sử Đăng ký Học phần
    </div>
    <div class="card-body">
        <?php if (count($registrations) > 0): ?>
            <div class="table-responsive">
                <table class="table table-sm table-striped">
                    <thead>
                        <tr>
                            <th>Ngày ĐK</th>
                            <th>Mã HP</th>
                            <th>Tên Học Phần</th>
                            <th>Số Tín Chỉ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($registrations as $reg): ?>
                        <tr>
                            <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($reg['NgayDK']))); ?></td>
                            <td><?php echo htmlspecialchars($reg['MaHP']); ?></td>
                            <td><?php echo htmlspecialchars($reg['TenHP']); ?></td>
                            <td><?php echo htmlspecialchars($reg['SoTinChi']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p>Sinh viên này chưa đăng ký học phần nào.</p>
        <?php endif; ?>
    </div>
</div>


<?php include 'templates/footer.php'; ?>