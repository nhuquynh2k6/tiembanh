<?php
session_start();
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = new mysqli("localhost", "bakery_user", "BakeryPassword123", "bakery_shop");
    $conn->set_charset("utf8mb4");

    $email = $_POST['loginEmail']; 
    $password = $_POST['loginPassword'];

    $stmt = $conn->prepare("SELECT id, password, role, fullname FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
	

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['admin_name'] = $user['fullname'];
        
        // Điều hướng chính xác dựa trên role
        if ($user['role'] === 'admin') {
            // Chuyển tới trang quản trị
            header("Location: ../admin/admin.php");
        } else {
            // Chuyển tới trang chủ cho khách hàng
            header("Location: ../index.php");
        }
        exit();
    } else {
        $error = "Email hoặc mật khẩu không đúng!";
    }
}
?>
<!doctype html>
<html lang="vi">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Đăng nhập - Sweet Bakery</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
      body {
        background-color: #f9f8f4;
        font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
      }
      .auth-card {
        max-width: 450px;
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
        <h4 class="fw-bold text-uppercase mb-1" style="letter-spacing: 1px;">ĐĂNG NHẬP</h4>
        <p class="card-text small text-white-50 m-0">Sweet Bakery</p>
      </div>

      <div class="card-body p-4">
        <?php if($error): ?><div class="alert alert-danger p-2 small text-center"><?= $error ?></div><?php endif; ?>

        <form action="login.php" method="POST">
          <div class="mb-3">
            <label for="loginEmail" class="form-label small fw-semibold text-secondary">Email hoặc Số điện thoại</label>
            <div class="input-group">
              <span class="input-group-text bg-white border-end-0 text-muted" style="border-radius: 10px 0 0 10px;"><i class="bi bi-person"></i></span>
              <input type="text" name="loginEmail" class="form-control border-start-0 py-2" id="loginEmail" placeholder="Nhập email hoặc SĐT..." style="border-radius: 0 10px 10px 0; font-size: 0.9rem;" required>
            </div>
          </div>

          <div class="mb-3">
            <div class="d-flex justify-content-between mb-1">
              <label for="loginPassword" class="form-label small fw-semibold text-secondary m-0">Mật khẩu</label>
              <a href="#" class="text-decoration-none small" style="color: #14452F; font-weight: 500;">Quên mật khẩu?</a>
            </div>
            <div class="input-group">
              <span class="input-group-text bg-white border-end-0 text-muted" style="border-radius: 10px 0 0 10px;"><i class="bi bi-lock"></i></span>
              <input type="password" name="loginPassword" class="form-control border-start-0 py-2" id="loginPassword" placeholder="Nhập mật khẩu..." style="border-radius: 0 10px 10px 0; font-size: 0.9rem;" required>
            </div>
          </div>

          <div class="form-check mb-4">
            <input class="form-check-input" type="checkbox" id="rememberMe">
            <label class="form-check-label text-muted small" for="rememberMe">Ghi nhớ đăng nhập</label>
          </div>

          <button type="submit" class="btn btn-brand w-100 py-2.5 fw-bold">ĐĂNG NHẬP</button>

          <div class="position-relative text-center my-4">
            <hr class="text-muted m-0">
            <span class="position-absolute top-50 start-50 translate-middle px-3 bg-white small text-muted text-uppercase" style="font-size: 0.7rem;">Hoặc đăng nhập bằng</span>
          </div>

          <div class="row g-2 mb-3">
            <div class="col-6">
              <button type="button" class="btn btn-outline-danger w-100 py-2 d-flex align-items-center justify-content-center gap-2 small" style="border-radius: 10px;">
                <i class="bi bi-google"></i> Google
              </button>
            </div>
            <div class="col-6">
              <button type="button" class="btn btn-outline-primary w-100 py-2 d-flex align-items-center justify-content-center gap-2 small" style="border-radius: 10px;">
                <i class="bi bi-facebook"></i> Facebook
              </button>
            </div>
          </div>

          <div class="text-center mt-4">
            <p class="text-muted small mb-0">Chưa có tài khoản? <a href="register.php" class="fw-bold text-decoration-none" style="color: #14452F;">Đăng ký ngay</a></p>
          </div>
        </form>
      </div>
    </div>
  </body>
</html>