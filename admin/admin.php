<?php
session_start();
// [BẢO MẬT] Kiểm tra nếu không phải Admin thì đẩy ra ngoài
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../pages/login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard | Sweet Bakery</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f4f6f9; }
        .sidebar { min-height: 100vh; background: #2c3e50; color: white; width: 260px; position: fixed; }
        .sidebar a { color: #bdc3c7; padding: 15px 20px; display: block; text-decoration: none; transition: 0.3s; }
        .sidebar a:hover, .sidebar a.active { background: #D4A373; color: white; }
        .main-content { margin-left: 260px; padding: 25px; }
        .stat-card { border-radius: 10px; border: none; padding: 20px; color: white; }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="p-4 text-center">
            <h5 class="fw-bold">SWEET BAKERY</h5>
            <small class="text-secondary">Trang quản trị</small>
        </div>
        <hr class="border-secondary">
        <a href="index.php" class="active"><i class="bi bi-speedometer2 me-2"></i> Tổng quan</a>
        <a href="products.php"><i class="bi bi-cake2 me-2"></i> Sản phẩm</a>
        <a href="orders.php"><i class="bi bi-cart-check me-2"></i> Đơn hàng</a>
        <a href="customers.php"><i class="bi bi-people me-2"></i> Khách hàng</a>
        <div class="mt-5 pt-5">
            <a href="../pages/logout.php" class="text-danger"><i class="bi bi-box-arrow-left me-2"></i> Đăng xuất</a>
        </div>
    </div>

    <main class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4>Bảng điều khiển</h4>
            <span class="text-muted">Chào quản trị viên, <?= htmlspecialchars($_SESSION['admin_name'] ?? 'Admin') ?></span>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="stat-card bg-primary">
                    <h6>Đơn hàng mới</h6>
                    <h3>12</h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card bg-success">
                    <h6>Doanh thu hôm nay</h6>
                    <h3>2.500.000đ</h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card bg-warning">
                    <h6>Khách hàng</h6>
                    <h3>150</h3>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold">Đơn hàng mới nhất</h6>
            </div>
            <div class="card-body">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Mã ĐH</th>
                            <th>Khách hàng</th>
                            <th>Ngày đặt</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>#BK001</td>
                            <td>Nguyễn Văn A</td>
                            <td>20/06/2026</td>
                            <td>150.000đ</td>
                            <td><span class="badge bg-info">Chờ xử lý</span></td>
                            <td><button class="btn btn-sm btn-outline-primary">Xem</button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>