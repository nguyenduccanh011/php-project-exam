<?php
require_once 'includes/auth_check.php';
require_once 'includes/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // --- Lấy dữ liệu từ form ---
    $maSV = trim($_POST['masv'] ?? ''); // Mã SV không đổi, dùng để WHERE
    $hoTen = trim($_POST['hoten'] ?? '');
    $gioiTinh = $_POST['gioitinh'] ?? 'Nam';
    $ngaySinh = $_POST['ngaysinh'] ?? null;
    $maNganh = $_POST['manganh'] ?? '';
    $password = $_POST['password'] ?? ''; // Mật khẩu mới (nếu có)
    $password_confirm = $_POST['password_confirm'] ?? '';
    $currentHinh = $_POST['current_hinh'] ?? null; // Đường dẫn ảnh cũ

    // --- Validate dữ liệu cơ bản ---
    $errors = [];
    if (empty($hoTen)) $errors[] = 'Họ tên không được để trống.';
    if (empty($maNganh)) $errors[] = 'Vui lòng chọn ngành học.';
    // Validate password nếu được nhập
    if (!empty($password) && $password !== $password_confirm) {
        $errors[] = 'Mật khẩu xác nhận không khớp.';
    }

    // --- Xử lý Upload Hình ảnh Mới (nếu có) ---
    $hinhPath = $currentHinh; // Mặc định giữ ảnh cũ
    $uploadDir = 'images/';
    $oldImageToDelete = null; // Lưu ảnh cũ cần xóa nếu upload thành công

    if (isset($_FILES['hinh']) && $_FILES['hinh']['error'] === UPLOAD_ERR_OK) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $fileType = $_FILES['hinh']['type'];

        if (in_array($fileType, $allowedTypes)) {
            $fileName = uniqid('sv_', true) . '_' . basename($_FILES['hinh']['name']);
            $uploadFilePath = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES['hinh']['tmp_name'], $uploadFilePath)) {
                $hinhPath = $uploadFilePath; // Đường dẫn ảnh mới
                $oldImageToDelete = $currentHinh; // Đánh dấu ảnh cũ để xóa sau khi update DB thành công
            } else {
                $errors[] = 'Không thể tải lên hình ảnh mới.';
            }
        } else {
            $errors[] = 'Định dạng hình ảnh mới không hợp lệ (chỉ chấp nhận JPG, PNG, GIF).';
        }
    }

    // --- Nếu không có lỗi thì thực hiện UPDATE ---
    if (empty($errors)) {
        try {
            // Xây dựng câu lệnh SQL UPDATE động
            $sql = "UPDATE SinhVien SET HoTen = :hoten, GioiTinh = :gioitinh, NgaySinh = :ngaysinh, Hinh = :hinh, MaNganh = :manganh";
            $params = [
                ':hoten' => $hoTen,
                ':gioitinh' => $gioiTinh,
                ':ngaysinh' => !empty($ngaySinh) ? $ngaySinh : null,
                ':hinh' => $hinhPath,
                ':manganh' => $maNganh,
                ':masv' => $maSV
            ];

            // Nếu có mật khẩu mới thì thêm vào câu lệnh và hash nó
            if (!empty($password)) {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $sql .= ", Password = :password";
                $params[':password'] = $hashedPassword;
            }

            $sql .= " WHERE MaSV = :masv";

            $stmt = $pdo->prepare($sql);

            if ($stmt->execute($params)) {
                 // Xóa ảnh cũ nếu đã upload ảnh mới thành công
                 if ($oldImageToDelete && file_exists($oldImageToDelete) && $oldImageToDelete !== $hinhPath) {
                     @unlink($oldImageToDelete); // Dùng @ để tránh lỗi nếu không xóa được
                 }
                 // Thành công, chuyển hướng
                 header('Location: students_index.php?message=Cập nhật thông tin sinh viên thành công!&type=success');
                 exit();
            } else {
                // Nếu update thất bại, xóa ảnh mới đã upload (nếu có)
                if ($hinhPath !== $currentHinh && file_exists($hinhPath)) {
                    unlink($hinhPath);
                }
                $errors[] = 'Cập nhật thông tin thất bại.';
            }
        } catch (PDOException $e) {
            error_log("Student Update Error: " . $e->getMessage());
            // Xóa ảnh mới đã upload nếu có lỗi DB
             if ($hinhPath !== $currentHinh && file_exists($hinhPath)) {
                 unlink($hinhPath);
             }
            $errors[] = 'Lỗi CSDL: Không thể cập nhật sinh viên.';
        }
    }

     // --- Nếu có lỗi, hiển thị lại form edit với thông báo ---
    if (!empty($errors)) {
        $_SESSION['form_errors'] = $errors;
        $_SESSION['form_data'] = $_POST; // Lưu lại dữ liệu đã nhập (cần xử lý cẩn thận hơn)
        header('Location: student_edit.php?id=' . urlencode($maSV)); // Quay lại form edit
        exit();
    }

} else {
    // Chuyển hướng nếu không phải POST
    header('Location: students_index.php');
    exit();
}
?>