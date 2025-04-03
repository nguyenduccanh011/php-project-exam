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

// Lấy thông tin sinh viên cần sửa
try {
    $sql = "SELECT * FROM sinhvien WHERE MaSV = :id";
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

// Xử lý form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $hoten = $_POST['hoten'] ?? '';
    $gioitinh = $_POST['gioitinh'] ?? '';
    $ngaysinh = $_POST['ngaysinh'] ?? '';
    $manganh = $_POST['manganh'] ?? '';
    
    // Xử lý upload hình mới (nếu có)
    $hinh = $student['Hinh']; // Giữ nguyên hình cũ nếu không upload hình mới
    if (isset($_FILES['hinh']) && $_FILES['hinh']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'images/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $fileName = 'sv_' . time() . '_' . $_FILES['hinh']['name'];
        $uploadFile = $uploadDir . $fileName;
        
        if (move_uploaded_file($_FILES['hinh']['tmp_name'], $uploadFile)) {
            // Xóa hình cũ nếu có
            if (!empty($student['Hinh']) && file_exists($student['Hinh'])) {
                unlink($student['Hinh']);
            }
            $hinh = $uploadFile;
        }
    }
    
    try {
        $sql = "UPDATE sinhvien 
                SET HoTen = :hoten, 
                    GioiTinh = :gioitinh, 
                    NgaySinh = :ngaysinh, 
                    Hinh = :hinh, 
                    MaNganh = :manganh 
                WHERE MaSV = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':hoten' => $hoten,
            ':gioitinh' => $gioitinh,
            ':ngaysinh' => $ngaysinh,
            ':hinh' => $hinh,
            ':manganh' => $manganh,
            ':id' => $id
        ]);
        
        header('Location: students.php');
        exit();
    } catch (PDOException $e) {
        $error = "Lỗi: " . $e->getMessage();
    }
}

// Lấy danh sách ngành học cho dropdown
try {
    $sql = "SELECT MaNganh, TenNganh FROM nganhhoc ORDER BY TenNganh";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $nganhs = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = "Lỗi: " . $e->getMessage();
}

include 'templates/header.php';
?>

<div class="container mt-4">
    <h2>CHỈNH SỬA SINH VIÊN</h2>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-danger">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>
    
    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="masv" class="form-label">MaSV</label>
            <input type="text" class="form-control" id="masv" value="<?php echo htmlspecialchars($student['MaSV']); ?>" readonly>
        </div>
        
        <div class="mb-3">
            <label for="hoten" class="form-label">Họ Tên</label>
            <input type="text" class="form-control" id="hoten" name="hoten" value="<?php echo htmlspecialchars($student['HoTen']); ?>" required>
        </div>
        
        <div class="mb-3">
            <label for="gioitinh" class="form-label">Giới Tính</label>
            <select class="form-control" id="gioitinh" name="gioitinh" required>
                <option value="Nam" <?php echo $student['GioiTinh'] === 'Nam' ? 'selected' : ''; ?>>Nam</option>
                <option value="Nữ" <?php echo $student['GioiTinh'] === 'Nữ' ? 'selected' : ''; ?>>Nữ</option>
            </select>
        </div>
        
        <div class="mb-3">
            <label for="ngaysinh" class="form-label">Ngày Sinh</label>
            <input type="date" class="form-control" id="ngaysinh" name="ngaysinh" value="<?php echo htmlspecialchars($student['NgaySinh']); ?>" required>
        </div>
        
        <div class="mb-3">
            <label for="hinh" class="form-label">Hình</label>
            <?php if (!empty($student['Hinh'])): ?>
                <div class="mb-2">
                    <img src="<?php echo htmlspecialchars($student['Hinh']); ?>" alt="Current photo" style="max-width: 200px;">
                </div>
            <?php endif; ?>
            <input type="file" class="form-control" id="hinh" name="hinh" accept="image/*">
        </div>
        
        <div class="mb-3">
            <label for="manganh" class="form-label">Mã Ngành</label>
            <select class="form-control" id="manganh" name="manganh" required>
                <?php foreach ($nganhs as $nganh): ?>
                    <option value="<?php echo htmlspecialchars($nganh['MaNganh']); ?>" 
                            <?php echo $student['MaNganh'] === $nganh['MaNganh'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($nganh['TenNganh']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <button type="submit" class="btn btn-primary">Save</button>
        <a href="students.php" class="btn btn-secondary">Back to List</a>
    </form>
</div>

<?php include 'templates/footer.php'; ?> 