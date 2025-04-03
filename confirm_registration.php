<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'includes/db_connection.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Kiểm tra giỏ đăng ký
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    $_SESSION['message'] = 'Không có học phần nào trong giỏ đăng ký!';
    $_SESSION['message_type'] = 'warning';
    header('Location: courses.php');
    exit();
}

$masv = $_SESSION['user_id'];

// Lấy thông tin sinh viên
try {
    $sql = "SELECT s.*, n.TenNganh 
            FROM sinhvien s 
            LEFT JOIN nganhhoc n ON s.MaNganh = n.MaNganh 
            WHERE s.MaSV = :masv";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':masv' => $masv]);
    $student = $stmt->fetch();
} catch (PDOException $e) {
    die("Lỗi truy vấn: " . $e->getMessage());
}

// Lấy danh sách học phần từ session
try {
    $placeholders = str_repeat('?,', count($_SESSION['cart']) - 1) . '?';
    $sql = "SELECT MaHP, TenHP, SoTinChi 
            FROM hocphan 
            WHERE MaHP IN ($placeholders)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($_SESSION['cart']);
    $registered_courses = $stmt->fetchAll();

    // Tính tổng số tín chỉ
    $total_credits = 0;
    foreach ($registered_courses as $course) {
        $total_credits += $course['SoTinChi'];
    }
} catch (PDOException $e) {
    die("Lỗi truy vấn: " . $e->getMessage());
}

include 'templates/header.php';
?>

<div class="container mt-4">
    <h2>THÔNG TIN ĐĂNG KÍ</h2>

    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Thông tin sinh viên</h5>
                    <table class="table table-borderless">
                        <tr>
                            <td>Mã số sinh viên:</td>
                            <td><?php echo htmlspecialchars($student['MaSV']); ?></td>
                        </tr>
                        <tr>
                            <td>Họ tên sinh viên:</td>
                            <td><?php echo htmlspecialchars($student['HoTen']); ?></td>
                        </tr>
                        <tr>
                            <td>Ngày sinh:</td>
                            <td><?php echo htmlspecialchars($student['NgaySinh']); ?></td>
                        </tr>
                        <tr>
                            <td>Ngành học:</td>
                            <td><?php echo htmlspecialchars($student['TenNganh']); ?></td>
                        </tr>
                        <tr>
                            <td>Ngày đăng ký:</td>
                            <td><?php echo date('d/m/Y'); ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Danh sách học phần đăng ký</h5>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>MaHP</th>
                                    <th>Tên Học Phần</th>
                                    <th>Số Tín Chỉ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($registered_courses as $course): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($course['MaHP']); ?></td>
                                    <td><?php echo htmlspecialchars($course['TenHP']); ?></td>
                                    <td><?php echo htmlspecialchars($course['SoTinChi']); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="2" class="text-danger">Số học phần: <?php echo count($registered_courses); ?></td>
                                    <td class="text-danger">Tổng số tín chỉ: <?php echo $total_credits; ?></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4 text-center">
        <form action="save_registration.php" method="POST">
            <button type="submit" class="btn btn-success">Xác nhận</button>
            <a href="cart.php" class="btn btn-secondary">Trở về giỏ hàng</a>
        </form>
    </div>
</div>

<?php include 'templates/footer.php'; ?> 