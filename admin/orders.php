<?php
session_start();
// BẢO MẬT: Kiểm tra quyền Admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../pages/login.php");
    exit();
}

// Kết nối CSDL
$conn = new mysqli("localhost", "bakery_user", "BakeryPassword123", "bakery_shop");
$conn->set_charset("utf8mb4");

// TRUY VẤN: Lấy danh sách đơn hàng mới nhất kèm tên khách hàng
$sql = "SELECT o.id, u.fullname, o.order_date, o.total_amount, o.payment_method, o.status, o.shipping_address 
        FROM orders o 
        LEFT JOIN users u ON o.user_id = u.id 
        ORDER BY o.order_date DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Đơn hàng | Sweet Bakery</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background-color: #f4f6f9; }
        .sidebar { min-height: 100vh; background: #2c3e50; color: white; width: 260px; position: fixed; top: 0; left: 0; }
        .sidebar a { color: #bdc3c7; padding: 15px 20px; display: block; text-decoration: none; transition: 0.3s; }
        .sidebar a:hover, .sidebar a.active { background: #D4A373; color: white; border-left: 4px solid #fff; }
        .main-content { margin-left: 260px; padding: 25px; }
        .status-badge { font-weight: 500; padding: 6px 12px; border-radius: 6px; font-size: 0.8rem; }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="p-4 text-center">
            <h5 class="fw-bold mb-0">SWEET BAKERY</h5>
            <small class="text-secondary">Administrator</small>
        </div>
        <hr class="border-secondary mt-0">
        <a href="admin.php"><i class="bi bi-speedometer2 me-2"></i> Tổng quan</a>
        <a href="products.php"><i class="bi bi-cake2 me-2"></i> Sản phẩm</a>
        <a href="orders.php" class="active"><i class="bi bi-cart-check me-2"></i> Đơn hàng</a>
        <a href="customers.php"><i class="bi bi-people me-2"></i> Khách hàng</a>
        <div class="mt-5 pt-5">
            <a href="../pages/login.php" class="text-danger"><i class="bi bi-box-arrow-left me-2"></i> Đăng xuất</a>
        </div>
    </div>

    <main class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold text-dark">Quản lý Đơn hàng</h4>
        </div>

        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom-0">
                <h6 class="mb-0 fw-bold text-secondary">DANH SÁCH ĐƠN HÀNG GẦN ĐÂY</h6>
                <div class="input-group" style="width: 300px;">
                    <input type="text" class="form-control form-control-sm" placeholder="Tìm mã đơn, tên khách...">
                    <button class="btn btn-outline-secondary btn-sm" type="button"><i class="bi bi-search"></i></button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-muted small text-uppercase">
                            <tr>
                                <th class="ps-4">Mã ĐH</th>
                                <th>Khách hàng</th>
                                <th>Ngày đặt</th>
                                <th>Tổng tiền</th>
                                <th>Thanh toán</th>
                                <th>Trạng thái</th>
                                <th class="text-end pe-4">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($result && $result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    // 1. Format Mã Đơn Hàng (VD: #DH001)
                                    $order_id = "#DH" . str_pad($row['id'], 3, "0", STR_PAD_LEFT);
                                    
                                    // 2. Format Ngày tháng
                                    $order_date = date("d/m/Y H:i", strtotime($row['order_date']));
                                    
                                    // 3. Format Tổng tiền
                                    $total_amount = number_format($row['total_amount'], 0, ',', '.') . 'đ';
                                    
                                    // 4. Xử lý logic Trạng thái đơn hàng
                                    $status = $row['status'];
                                    $status_class = '';
                                    $status_text = '';
                                    
                                    switch(strtolower($status)) {
                                        case 'pending':
                                            $status_class = 'bg-warning text-dark';
                                            $status_text = 'Chờ xử lý';
                                            break;
                                        case 'processing':
                                            $status_class = 'bg-info text-dark';
                                            $status_text = 'Đang giao';
                                            break;
                                        case 'completed':
                                            $status_class = 'bg-success text-white';
                                            $status_text = 'Đã giao';
                                            break;
                                        case 'cancelled':
                                            $status_class = 'bg-danger text-white';
                                            $status_text = 'Đã hủy';
                                            break;
                                        default:
                                            $status_class = 'bg-secondary text-white';
                                            $status_text = $status;
                                    }

                                    // Xử lý tên khách hàng (nếu tài khoản bị xóa)
                                    $customer_name = $row['fullname'] ? htmlspecialchars($row['fullname']) : '<span class="text-muted fst-italic">Khách vãng lai</span>';

                                    echo "<tr>
                                        <td class='ps-4 fw-bold text-secondary'>{$order_id}</td>
                                        <td class='fw-semibold text-dark'>{$customer_name}</td>
                                        <td class='small text-muted'>{$order_date}</td>
                                        <td class='text-danger fw-bold'>{$total_amount}</td>
                                        <td class='small fw-medium'>{$row['payment_method']}</td>
                                        <td><span class='badge status-badge {$status_class}'>{$status_text}</span></td>
                                        <td class='text-end pe-4'>
                                            <a href='order_detail.php?id={$row['id']}' class='btn btn-sm btn-light border text-primary' title='Xem chi tiết'>
                                                <i class='bi bi-eye'></i> Xem
                                            </a>
                                        </td>
                                    </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='7' class='text-center py-5 text-muted'>Chưa có đơn hàng nào trong hệ thống.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>