<?php
require_once 'includes/auth_check.php';
require_once 'includes/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // --- Lấy dữ liệu từ form ---
    $maSV = trim($_POST['masv'] ?? '');
    $hoTen = trim($_POST['hoten'] ?? '');
    $gioiTinh = $_POST['gioitinh'] ?? 'Nam';
    $ngaySinh = $_POST['ngaysinh'] ?? null;
    $maNganh = $_POST['manganh'] ?? '';
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    // --- Validate dữ liệu cơ bản ---
    $errors = [];
    if (empty($maSV)) $errors[] = 'Mã sinh viên không được để trống.';
    if (strlen($maSV) > 10) $errors[] = 'Mã sinh viên không được quá 10 ký tự.';
    if (empty($hoTen)) $errors[] = 'Họ tên không được để trống.';
    if (empty($maNganh)) $errors[] = 'Vui lòng chọn ngành học.';
    if (empty($password)) $errors[] = 'Mật khẩu không được để trống.';
    if ($password !== $password_confirm) $errors[] = 'Mật khẩu xác nhận không khớp.';
    // Kiểm tra mã SV đã tồn tại chưa
    try {
        $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM SinhVien WHERE MaSV = :masv");
        $checkStmt->execute([':masv' => $maSV]);
        if ($checkStmt->fetchColumn() > 0) {
            $errors[] = 'Mã sinh viên đã tồn tại.';
        }
    } catch (PDOException $e) {
         error_log("Check MaSV Error: " . $e->getMessage());
         $errors[] = 'Lỗi kiểm tra mã sinh viên.';
    }


    // --- Xử lý Upload Hình ảnh ---
    $hinhPath = null; // Đường dẫn lưu vào DB
    $uploadDir = 'images/'; // Thư mục lưu ảnh (phải tồn tại và có quyền ghi)
    if (isset($_FILES['hinh']) && $_FILES['hinh']['error'] === UPLOAD_ERR_OK) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $fileType = $_FILES['hinh']['type'];

        if (in_array($fileType, $allowedTypes)) {
            $fileName = uniqid('sv_', true) . '_' . basename($_FILES['hinh']['name']);
            $uploadFilePath = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES['hinh']['tmp_name'], $uploadFilePath)) {
                $hinhPath = $uploadFilePath; // Lấy đường dẫn tương đối để lưu
            } else {
                $errors[] = 'Không thể tải lên hình ảnh.';
            }
        } else {
            $errors[] = 'Định dạng hình ảnh không hợp lệ (chỉ chấp nhận JPG, PNG, GIF).';
        }
    }

    // --- Nếu không có lỗi thì thực hiện INSERT ---
    if (empty($errors)) {
        try {
            // Hash mật khẩu
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $sql = "INSERT INTO SinhVien (MaSV, HoTen, GioiTinh, NgaySinh, Hinh, MaNganh, Password)
                    VALUES (:masv, :hoten, :gioitinh, :ngaysinh, :hinh, :manganh, :password)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':masv', $maSV);
            $stmt->bindParam(':hoten', $hoTen);
            $stmt->bindParam(':gioitinh', $gioiTinh);
            // Xử lý ngày sinh null
            $ngaySinhValue = !empty($ngaySinh) ? $ngaySinh : null;
            $stmt->bindParam(':ngaysinh', $ngaySinhValue);
            $stmt->bindParam(':hinh', $hinhPath); // Lưu đường dẫn ảnh
            $stmt->bindParam(':manganh', $maNganh);
            $stmt->bindParam(':password', $hashedPassword);

            if ($stmt->execute()) {
                // Thành công, chuyển hướng về trang danh sách
                header('Location: students_index.php?message=Thêm sinh viên thành công!&type=success');
                exit();
            } else {
                $errors[] = 'Thêm sinh viên thất bại.';
            }
        } catch (PDOException $e) {
            error_log("Student Store Error: " . $e->getMessage());
            // Xóa file ảnh đã upload nếu insert lỗi
            if ($hinhPath && file_exists($hinhPath)) {
                unlink($hinhPath);
            }
            $errors[] = 'Lỗi CSDL: Không thể thêm sinh viên. Mã lỗi: ' . $e->getCode();
             // Check for specific duplicate entry error (1062)
            if ($e->getCode() == '23000' && strpos($e->getMessage(), 'Duplicate entry') !== false) {
                 $errors[] = 'Mã sinh viên đã tồn tại trong hệ thống.';
             }
        }
    }

    // --- Nếu có lỗi, hiển thị lại form với thông báo ---
    if (!empty($errors)) {
        // Lưu lỗi vào session để hiển thị lại form
        $_SESSION['form_errors'] = $errors;
        $_SESSION['form_data'] = $_POST; // Lưu lại dữ liệu đã nhập
        header('Location: student_create.php'); // Quay lại form
        exit();
    }

} else {
    // Nếu không phải POST request, chuyển hướng
    header('Location: student_create.php');
    exit();
}
?>