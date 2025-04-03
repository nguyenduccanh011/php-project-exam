<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'includes/db_connection.php';

// Thiết lập phân trang
$items_per_page = 4; // Số sinh viên mỗi trang
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $items_per_page;

// Lấy tổng số sinh viên
try {
    $sql = "SELECT COUNT(*) as total FROM sinhvien";
    $stmt = $pdo->query($sql);
    $total_items = $stmt->fetch()['total'];
    $total_pages = ceil($total_items / $items_per_page);
} catch (PDOException $e) {
    die("Lỗi truy vấn: " . $e->getMessage());
}

// Lấy danh sách sinh viên có phân trang
try {
    $sql = "SELECT s.*, n.TenNganh 
            FROM sinhvien s 
            LEFT JOIN nganhhoc n ON s.MaNganh = n.MaNganh 
            ORDER BY s.MaSV 
            LIMIT :offset, :items_per_page";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->bindValue(':items_per_page', $items_per_page, PDO::PARAM_INT);
    $stmt->execute();
    $students = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Lỗi truy vấn: " . $e->getMessage());
}

include 'templates/header.php';
?>

<div class="container mt-4">
    <div class="row mb-3">
        <div class="col">
            <h2>TRANG SINH VIÊN</h2>
        </div>
        <div class="col text-end">
            <a href="add_student.php" class="btn btn-primary">Add Student</a>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>MaSV</th>
                    <th>Họ Tên</th>
                    <th>Giới Tính</th>
                    <th>Ngày Sinh</th>
                    <th>Hình</th>
                    <th>Mã Ngành</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($students as $student): ?>
                <tr>
                    <td><?php echo htmlspecialchars($student['MaSV']); ?></td>
                    <td><?php echo htmlspecialchars($student['HoTen']); ?></td>
                    <td><?php echo htmlspecialchars($student['GioiTinh']); ?></td>
                    <td><?php echo htmlspecialchars($student['NgaySinh']); ?></td>
                    <td>
                        <?php if (!empty($student['Hinh'])): ?>
                            <img src="<?php echo htmlspecialchars($student['Hinh']); ?>" alt="Student photo" style="max-width: 100px;">
                        <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars($student['MaNganh']); ?></td>
                    <td>
                        <a href="edit_student.php?id=<?php echo urlencode($student['MaSV']); ?>" class="btn btn-sm btn-primary">Edit</a>
                        <a href="detail_student.php?id=<?php echo urlencode($student['MaSV']); ?>" class="btn btn-sm btn-info">Details</a>
                        <a href="delete_student.php?id=<?php echo urlencode($student['MaSV']); ?>" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc muốn xóa sinh viên này?')">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Phân trang -->
    <?php if ($total_pages > 1): ?>
    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center">
            <?php if ($page > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?php echo $page-1; ?>">Previous</a>
                </li>
            <?php endif; ?>
            
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>
            
            <?php if ($page < $total_pages): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?php echo $page+1; ?>">Next</a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
    <?php endif; ?>
</div>

<?php include 'templates/footer.php'; ?> 