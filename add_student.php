<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'includes/db_connection.php';

// Xử lý form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $masv = $_POST['masv'] ?? '';
    $hoten = $_POST['hoten'] ?? '';
    $gioitinh = $_POST['gioitinh'] ?? '';
    $ngaysinh = $_POST['ngaysinh'] ?? '';
    $manganh = $_POST['manganh'] ?? '';
    
    // Xử lý upload hình
    $hinh = '';
    if (isset($_FILES['hinh']) && $_FILES['hinh']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'images/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $fileName = 'sv_' . time() . '_' . $_FILES['hinh']['name'];
        $uploadFile = $uploadDir . $fileName;
        
        if (move_uploaded_file($_FILES['hinh']['tmp_name'], $uploadFile)) {
            $hinh = $uploadFile;
        }
    }
    
    try {
        $sql = "INSERT INTO sinhvien (MaSV, HoTen, GioiTinh, NgaySinh, Hinh, MaNganh) 
                VALUES (:masv, :hoten, :gioitinh, :ngaysinh, :hinh, :manganh)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':masv' => $masv,
            ':hoten' => $hoten,
            ':gioitinh' => $gioitinh,
            ':ngaysinh' => $ngaysinh,
            ':hinh' => $hinh,
            ':manganh' => $manganh
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
    <h2>THÊM SINH VIÊN</h2>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-danger">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>
    
    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="masv" class="form-label">MaSV</label>
            <input type="text" class="form-control" id="masv" name="masv" required>
        </div>
        
        <div class="mb-3">
            <label for="hoten" class="form-label">Họ Tên</label>
            <input type="text" class="form-control" id="hoten" name="hoten" required>
        </div>
        
        <div class="mb-3">
            <label for="gioitinh" class="form-label">Giới Tính</label>
            <select class="form-control" id="gioitinh" name="gioitinh" required>
                <option value="Nam">Nam</option>
                <option value="Nữ">Nữ</option>
            </select>
        </div>
        
        <div class="mb-3">
            <label for="ngaysinh" class="form-label">Ngày Sinh</label>
            <input type="date" class="form-control" id="ngaysinh" name="ngaysinh" required>
        </div>
        
        <div class="mb-3">
            <label for="hinh" class="form-label">Hình</label>
            <input type="file" class="form-control" id="hinh" name="hinh" accept="image/*">
        </div>
        
        <div class="mb-3">
            <label for="manganh" class="form-label">Mã Ngành</label>
            <select class="form-control" id="manganh" name="manganh" required>
                <?php foreach ($nganhs as $nganh): ?>
                    <option value="<?php echo htmlspecialchars($nganh['MaNganh']); ?>">
                        <?php echo htmlspecialchars($nganh['TenNganh']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <button type="submit" class="btn btn-primary">Create</button>
        <a href="students.php" class="btn btn-secondary">Back to List</a>
    </form>
</div>

<?php include 'templates/footer.php'; ?> 