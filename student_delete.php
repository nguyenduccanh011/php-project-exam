<?php
require_once 'includes/auth_check.php';
require_once 'includes/db_connection.php';

$maSV = $_GET['id'] ?? null;

if (!$maSV) {
    header('Location: students_index.php?message=Mã sinh viên không hợp lệ.&type=danger');
    exit();
}

// --- Lấy thông tin sinh viên để hiển thị xác nhận (optional but good UX) ---
try {
    $stmt = $pdo->prepare("SELECT MaSV, HoTen, Hinh FROM SinhVien WHERE MaSV = :masv");
    $stmt->execute([':masv' => $maSV]);
    $student = $stmt->fetch();
    if (!$student) {
         header('Location: students_index.php?message=Không tìm thấy sinh viên.&type=warning');
         exit();
     }
} catch (PDOException $e) {
     error_log("Fetch student for delete error: " . $e->getMessage());
     // Có thể bỏ qua lỗi này và chỉ hiển thị nút xóa
     $student = ['MaSV' => $maSV, 'HoTen' => 'Không rõ']; // Dữ liệu tạm
}


// --- Xử lý xóa nếu người dùng xác nhận (ví dụ: thông qua POST hoặc param 'confirm=yes') ---
$confirm = $_GET['confirm'] ?? 'no';

if ($confirm === 'yes') {
    try {
        // *** Quan trọng: Kiểm tra xem sinh viên có dữ liệu liên quan không (ví dụ: Đăng ký)
        // Nếu có ràng buộc khóa ngoại, việc xóa có thể bị chặn bởi CSDL
        // Hoặc bạn cần xử lý logic phức tạp hơn (xóa các bản ghi liên quan, hoặc không cho xóa)
        // Ví dụ đơn giản: kiểm tra bảng DangKy
        $checkDangKy = $pdo->prepare("SELECT COUNT(*) FROM DangKy WHERE MaSV = :masv");
        $checkDangKy->execute([':masv' => $maSV]);
        if ($checkDangKy->fetchColumn() > 0) {
             header('Location: students_index.php?message=Không thể xóa sinh viên này vì đã có dữ liệu đăng ký học phần liên quan.&type=danger');
             exit();
         }

        // Bắt đầu transaction (nếu cần xóa ảnh)
        $pdo->beginTransaction();

        // Lấy đường dẫn ảnh để xóa file
        $imagePath = $student['Hinh'] ?? null;

        // Thực hiện xóa sinh viên
        $deleteStmt = $pdo->prepare("DELETE FROM SinhVien WHERE MaSV = :masv");
        $deleteStmt->execute([':masv' => $maSV]);

        // Nếu xóa thành công trong DB, xóa file ảnh (nếu có)
        if ($deleteStmt->rowCount() > 0) {
            if ($imagePath && file_exists($imagePath)) {
                @unlink($imagePath);
            }
            $pdo->commit(); // Hoàn tất transaction
            header('Location: students_index.php?message=Xóa sinh viên thành công!&type=success');
            exit();
        } else {
             $pdo->rollBack(); // Hủy transaction nếu không xóa được dòng nào
             header('Location: students_index.php?message=Xóa sinh viên thất bại hoặc sinh viên không tồn tại.&type=warning');
             exit();
        }

    } catch (PDOException $e) {
         $pdo->rollBack(); // Hủy transaction nếu có lỗi CSDL
         error_log("Student Delete Error: " . $e->getMessage());
         // Kiểm tra lỗi khóa ngoại
         if ($e->getCode() == '23000') {
              header('Location: students_index.php?message=Không thể xóa sinh viên do có ràng buộc dữ liệu (ví dụ: đăng ký học phần).&type=danger');
         } else {
             header('Location: students_index.php?message=Lỗi CSDL: Không thể xóa sinh viên.&type=danger');
         }
         exit();
    }
}

// --- Hiển thị trang xác nhận xóa ---
include 'templates/header.php';
?>
<h2 class="mb-4">Xác nhận Xóa Sinh Viên</h2>

<div class="card">
    <div class="card-body">
        <h5 class="card-title">Bạn có chắc chắn muốn xóa sinh viên này?</h5>
        <p class="card-text">
            <strong>Mã SV:</strong> <?php echo htmlspecialchars($student['MaSV']); ?><br>
            <strong>Họ Tên:</strong> <?php echo htmlspecialchars($student['HoTen']); ?>
        </p>
        <?php if (!empty($student['Hinh']) && file_exists($student['Hinh'])): ?>
            <img src="<?php echo htmlspecialchars($student['Hinh']); ?>" alt="Hình <?php echo htmlspecialchars($student['HoTen']); ?>" class="img-thumbnail mb-3" style="max-width: 150px;">
        <?php endif; ?>

        <p class="text-danger"><strong>Cảnh báo:</strong> Hành động này không thể hoàn tác!</p>

        <a href="student_delete.php?id=<?php echo htmlspecialchars($student['MaSV']); ?>&confirm=yes" class="btn btn-danger">Xác nhận Xóa</a>
        <a href="students_index.php" class="btn btn-secondary">Hủy bỏ</a>
    </div>
</div>

<?php include 'templates/footer.php'; ?>