<?php
require_once 'includes/auth_check.php'; // Đảm bảo đã đăng nhập
require_once 'includes/db_connection.php';

// --- Phân trang ---
$students_per_page = 4; // Số sinh viên mỗi trang theo yêu cầu
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $students_per_page;

try {
    // Đếm tổng số sinh viên
    $total_students_stmt = $pdo->query("SELECT COUNT(*) FROM SinhVien");
    $total_students = $total_students_stmt->fetchColumn();
    $total_pages = ceil($total_students / $students_per_page);

    // Lấy dữ liệu sinh viên cho trang hiện tại
    $sql = "SELECT sv.MaSV, sv.HoTen, sv.GioiTinh, sv.NgaySinh, sv.Hinh, nh.TenNganh
            FROM SinhVien sv
            LEFT JOIN NganhHoc nh ON sv.MaNganh = nh.MaNganh
            ORDER BY sv.MaSV
            LIMIT :limit OFFSET :offset";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':limit', $students_per_page, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $students = $stmt->fetchAll();

} catch (PDOException $e) {
    error_log("Student Index Error: " . $e->getMessage());
    die("Lỗi: Không thể tải danh sách sinh viên.");
}

include 'templates/header.php';
?>

<h2 class="mb-4">Danh sách Sinh viên</h2>

<a href="student_create.php" class="btn btn-primary mb-3">Thêm Sinh Viên</a>

<?php if (isset($_GET['message'])): ?>
    <div class="alert alert-<?php echo htmlspecialchars($_GET['type'] ?? 'success'); ?>" role="alert">
        <?php echo htmlspecialchars($_GET['message']); ?>
    </div>
<?php endif; ?>

<?php if (count($students) > 0): ?>
    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle">
            <thead>
                <tr>
                    <th>Mã SV</th>
                    <th>Họ Tên</th>
                    <th>Giới Tính</th>
                    <th>Ngày Sinh</th>
                    <th>Hình</th>
                    <th>Ngành Học</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($students as $student): ?>
                <tr>
                    <td><?php echo htmlspecialchars($student['MaSV']); ?></td>
                    <td><?php echo htmlspecialchars($student['HoTen']); ?></td>
                    <td><?php echo htmlspecialchars($student['GioiTinh']); ?></td>
                    <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($student['NgaySinh']))); ?></td>
                    <td>
                        <?php if (!empty($student['Hinh']) && file_exists($student['Hinh'])): ?>
                            <img src="<?php echo htmlspecialchars($student['Hinh']); ?>" alt="Hình <?php echo htmlspecialchars($student['HoTen']); ?>" class="img-thumbnail student-img">
                        <?php else: ?>
                            <small>Không có hình</small>
                        <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars($student['TenNganh'] ?? 'Chưa có'); ?></td>
                    <td>
                        <a href="student_details.php?id=<?php echo htmlspecialchars($student['MaSV']); ?>" class="btn btn-info btn-sm" title="Xem chi tiết">Xem</a>
                        <a href="student_edit.php?id=<?php echo htmlspecialchars($student['MaSV']); ?>" class="btn btn-warning btn-sm" title="Sửa">Sửa</a>
                        <a href="student_delete.php?id=<?php echo htmlspecialchars($student['MaSV']); ?>" class="btn btn-danger btn-sm" title="Xóa" onclick="return confirm('Bạn có chắc chắn muốn xóa sinh viên này?');">Xóa</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php if ($total_pages > 1): ?>
    <nav aria-label="Page navigation">
      <ul class="pagination justify-content-center">
        <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
          <a class="page-link" href="?page=<?php echo $page - 1; ?>">Trước</a>
        </li>
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
          <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
            <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
          </li>
        <?php endfor; ?>
        <li class="page-item <?php echo ($page >= $total_pages) ? 'disabled' : ''; ?>">
          <a class="page-link" href="?page=<?php echo $page + 1; ?>">Sau</a>
        </li>
      </ul>
    </nav>
    <?php endif; ?>

<?php else: ?>
    <p>Chưa có sinh viên nào.</p>
<?php endif; ?>

<?php include 'templates/footer.php'; ?>