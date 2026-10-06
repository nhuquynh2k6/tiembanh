<?php
session_start();
// Nếu đã đăng nhập thì đẩy ra trang chủ
if (isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = new mysqli("localhost", "bakery_user", "BakeryPassword123", "bakery_shop");
    $conn->set_charset("utf8mb4");

    // Lấy dữ liệu từ form (dựa vào thuộc tính name)
    $fullname = trim($_POST['regName']);
    $phone = trim($_POST['regPhone']);
    $email = trim($_POST['regEmail']);
    $password = $_POST['regPassword'];
    $confirm_password = $_POST['regConfirmPassword'];

    // 1. Kiểm tra mật khẩu xác nhận
    if ($password !== $confirm_password) {
        $error = "Mật khẩu xác nhận không khớp!";
    } else {
        // 2. Kiểm tra xem email đã tồn tại trong Database chưa
        $stmt_check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt_check->bind_param("s", $email);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();

        if ($result_check->num_rows > 0) {
            $error = "Email này đã được đăng ký. Vui lòng đăng nhập!";
        } else {
            // 3. Nếu hợp lệ, mã hóa mật khẩu và lưu vào DB
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $role = 'user'; // Mặc định khách hàng mới có quyền 'user'
            
            $stmt_insert = $conn->prepare("INSERT INTO users (fullname, email, password, phone, role) VALUES (?, ?, ?, ?, ?)");
            $stmt_insert->bind_param("sssss", $fullname, $email, $hashed_password, $phone, $role);
            
            if ($stmt_insert->execute()) {
                $success = "Đăng ký thành công! Hệ thống sẽ chuyển tới trang Đăng nhập sau 2 giây...";
                header("refresh:2;url=login.php"); // Tự động chuyển hướng sau 2 giây
            } else {
                $error = "Có lỗi hệ thống: " . $conn->error;
            }
        }
    }
}
?>
<!doctype html>
<html lang="vi">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Đăng ký - Sweet Bakery</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
      body {
        background-color: #f9f8f4;
        font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
      }
      .auth-card {
        max-width: 520px;
        border: none;
        border-radius: 20px;
      }
      .form-control:focus {
        border-color: #14452F;
        box-shadow: 0 0 0 0.25rem rgba(20, 69, 47, 0.25);
      }
      .btn-brand {
        background-color: #14452F;
        color: white;
        border-radius: 10px;
      }
      .btn-brand:hover {
        background-color: #0b291c;
        color: white;
      }
    </style>
  </head>
  <body class="d-flex flex-column min-vh-100 justify-content-center align-items-center py-5">

    <div class="position-absolute top-0 start-0 m-4">
      <a href="../index.php" class="text-decoration-none text-secondary fw-semibold">
        <i class="bi bi-arrow-left"></i> Quay lại Trang chủ
      </a>
    </div>

    <div class="card auth-card shadow-lg w-100 m-3">
      <div class="card-header text-center text-white border-0 py-4" style="background-color: #14452F; border-top-left-radius: 20px; border-top-right-radius: 20px;">
        <h4 class="fw-bold text-uppercase mb-1" style="letter-spacing: 1px;">TẠO TÀI KHOẢN MỚI</h4>
        <p class="card-text small text-white-50 m-0">Sweet Bakery</p>
      </div>

      <div class="card-body p-4">
        <?php if($error): ?><div class="alert alert-danger p-2 small text-center"><?= $error ?></div><?php endif; ?>
        <?php if($success): ?><div class="alert alert-success p-2 small text-center"><?= $success ?></div><?php endif; ?>

        <form action="register.php" method="POST">
          <div class="mb-3">
            <label for="regName" class="form-label small fw-semibold text-secondary">Họ và tên</label>
            <div class="input-group">
              <span class="input-group-text bg-white border-end-0 text-muted" style="border-radius: 10px 0 0 10px;"><i class="bi bi-person-vcard"></i></span>
              <input type="text" name="regName" class="form-control border-start-0 py-2" id="regName" placeholder="Họ và tên của bạn..." style="border-radius: 0 10px 10px 0; font-size: 0.9rem;" required>
            </div>
          </div>

          <div class="mb-3">
            <label for="regPhone" class="form-label small fw-semibold text-secondary">Số điện thoại</label>
            <div class="input-group">
              <span class="input-group-text bg-white border-end-0 text-muted" style="border-radius: 10px 0 0 10px;"><i class="bi bi-telephone"></i></span>
              <input type="tel" name="regPhone" class="form-control border-start-0 py-2" id="regPhone" placeholder="Số điện thoại..." style="border-radius: 0 10px 10px 0; font-size: 0.9rem;" required>
            </div>
          </div>

          <div class="mb-3">
            <label for="regEmail" class="form-label small fw-semibold text-secondary">Địa chỉ Email</label>
            <div class="input-group">
              <span class="input-group-text bg-white border-end-0 text-muted" style="border-radius: 10px 0 0 10px;"><i class="bi bi-envelope"></i></span>
              <input type="email" name="regEmail" class="form-control border-start-0 py-2" id="regEmail" placeholder="name@example.com" style="border-radius: 0 10px 10px 0; font-size: 0.9rem;" required>
            </div>
          </div>

          <div class="mb-3">
            <label for="regPassword" class="form-label small fw-semibold text-secondary">Mật khẩu</label>
            <div class="input-group">
              <span class="input-group-text bg-white border-end-0 text-muted" style="border-radius: 10px 0 0 10px;"><i class="bi bi-lock"></i></span>
              <input type="password" name="regPassword" class="form-control border-start-0 py-2" id="regPassword" placeholder="Tạo mật khẩu..." style="border-radius: 0 10px 10px 0; font-size: 0.9rem;" required>
            </div>
          </div>

          <div class="mb-3">
            <label for="regConfirmPassword" class="form-label small fw-semibold text-secondary">Xác nhận mật khẩu</label>
            <div class="input-group">
              <span class="input-group-text bg-white border-end-0 text-muted" style="border-radius: 10px 0 0 10px;"><i class="bi bi-lock-check"></i></span>
              <input type="password" name="regConfirmPassword" class="form-control border-start-0 py-2" id="regConfirmPassword" placeholder="Nhập lại mật khẩu..." style="border-radius: 0 10px 10px 0; font-size: 0.9rem;" required>
            </div>
          </div>

          <div class="form-check mb-4">
            <input class="form-check-input" type="checkbox" id="agreeTerms" required>
            <label class="form-check-label text-muted small" for="agreeTerms">
              Tôi đồng ý với <a href="#" style="color: #14452F; font-weight: 500;">Điều khoản dịch vụ</a> & <a href="#" style="color: #14452F; font-weight: 500;">Chính sách bảo mật</a>.
            </label>
          </div>

          <button type="submit" class="btn btn-brand w-100 py-2.5 fw-bold">ĐĂNG KÝ NGAY</button>

          <div class="text-center mt-4">
            <p class="text-muted small mb-0">Đã có tài khoản? <a href="login.php" class="fw-bold text-decoration-none" style="color: #14452F;">Đăng nhập</a></p>
          </div>
        </form>
      </div>
    </div>

  </body>
</html>