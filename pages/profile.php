<?php
session_start();

// 1. Kiểm tra đăng nhập (Bắt buộc)
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Kết nối CSDL
$conn = new mysqli("localhost", "bakery_user", "BakeryPassword123", "bakery_shop");
$conn->set_charset("utf8mb4");

$user_id = $_SESSION['user_id'];
$msg = '';

// 2. Xử lý khi người dùng bấm "Cập nhật thông tin"
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $fullname = $_POST['fullname'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $address = $_POST['address'] ?? '';

    // Cập nhật vào bảng users (Em kiểm tra xem bảng users có đúng tên cột này không nhé)
    $stmt_update = $conn->prepare("UPDATE users SET fullname = ?, phone = ?, address = ? WHERE id = ?");
    $stmt_update->bind_param("sssi", $fullname, $phone, $address, $user_id);
    
    if ($stmt_update->execute()) {
        $msg = "<div class='alert alert-success'>Cập nhật thông tin thành công!</div>";
    } else {
        $msg = "<div class='alert alert-danger'>Lỗi cập nhật. Vui lòng thử lại.</div>";
    }
}

// 3. Lấy thông tin User hiện tại
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// 4. Lấy Lịch sử đơn hàng của User này
$stmt_orders = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY order_date DESC");
$stmt_orders->bind_param("i", $user_id);
$stmt_orders->execute();
$orders = $stmt_orders->get_result();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hồ Sơ Của Tôi | Sweet Bakery</title>
    <link href="../../bootstrap/bootstrap-5.3.8-dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #FAFAFA; }
        .text-brand { color: #D4A373; }
        .btn-brand { background-color: #D4A373; color: white; transition: 0.3s; }
        .btn-brand:hover { background-color: #bc8f63; color: white; }
    </style>
</head>
<body>
    <nav class="navbar navbar-light bg-white shadow-sm py-3 mb-5">
        <div class="container d-flex justify-content-between">
            <a class="navbar-brand fw-bold text-brand fs-4" href="../index.php">
                <i class="bi bi-arrow-left me-2 text-dark"></i> Về Trang Chủ
            </a>
            <div class="fw-medium">
                Xin chào, <span class="text-brand"><?= htmlspecialchars($user['username'] ?? 'Bạn') ?></span>!
            </div>
        </div>
    </nav>

    <div class="container pb-5">
        <?= $msg ?>
        <div class="row g-4">
            
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <div class="text-center mb-4">
                            <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 100px; height: 100px;">
                                <i class="bi bi-person text-secondary" style="font-size: 3rem;"></i>
                            </div>
                            <h4 class="fw-bold mb-0">Hồ sơ cá nhân</h4>
                        </div>

                        <form action="profile.php" method="POST">
                            <input type="hidden" name="update_profile" value="1">
                            
                            <div class="mb-3">
                                <label class="form-label text-muted small">Tên đăng nhập (Không thể đổi)</label>
                                <input type="text" class="form-control bg-light" value="<?= htmlspecialchars($user['username'] ?? '') ?>" disabled>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label text-muted small">Họ và tên</label>
                                <input type="text" name="fullname" class="form-control" value="<?= htmlspecialchars($user['fullname'] ?? '') ?>" placeholder="Nhập họ tên đầy đủ">
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-muted small">Số điện thoại</label>
                                <input type="tel" name="phone" class="form-control" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" placeholder="Nhập số điện thoại">
                            </div>

                            <div class="mb-4">
                                <label class="form-label text-muted small">Địa chỉ mặc định</label>
                                <textarea name="address" class="form-control" rows="3" placeholder="Nhập địa chỉ giao hàng mặc định"><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
                            </div>

                            <button type="submit" class="btn btn-brand w-100 rounded-pill py-2 fw-bold">Cập nhật thông tin</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4">
                        <h4 class="fw-bold mb-4"><i class="bi bi-receipt text-brand me-2"></i>Lịch sử mua hàng</h4>
                        
                        <?php if ($orders->num_rows > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Mã đơn</th>
                                            <th>Ngày đặt</th>
                                            <th>Tổng tiền</th>
                                            <th>Trạng thái</th>
                                            </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($row = $orders->fetch_assoc()): ?>
                                            <tr>
                                                <td class="fw-bold">#ORD<?= str_pad($row['id'], 4, "0", STR_PAD_LEFT) ?></td>
                                                <td><?= date('d/m/Y H:i', strtotime($row['order_date'])) ?></td>
                                                <td class="text-brand fw-bold"><?= number_format($row['total_amount'], 0, ',', '.') ?>đ</td>
                                                <td>
                                                    <?php 
                                                        // Hiển thị trạng thái màu sắc tương ứng
                                                        $stt = $row['status'];
                                                        if($stt == 'Pending') echo "<span class='badge bg-warning text-dark'>Chờ xác nhận</span>";
                                                        elseif($stt == 'Processing') echo "<span class='badge bg-info'>Đang làm bánh</span>";
                                                        elseif($stt == 'Delivering') echo "<span class='badge bg-primary'>Đang giao</span>";
                                                        elseif($stt == 'Completed') echo "<span class='badge bg-success'>Hoàn thành</span>";
                                                        else echo "<span class='badge bg-secondary'>$stt</span>";
                                                    ?>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <i class="bi bi-box-seam text-muted" style="font-size: 3rem;"></i>
                                <p class="mt-3 text-muted">Bạn chưa có đơn hàng nào.</p>
                                <a href="../index.php" class="btn btn-outline-dark rounded-pill mt-2">Bắt đầu mua sắm</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        </div>
    </div>
    
    <script src="../../bootstrap/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>