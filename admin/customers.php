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

// TRUY VẤN: Lấy danh sách tất cả tài khoản, sắp xếp người mới đăng ký lên đầu
$sql = "SELECT id, fullname, email, phone, address, role, created_at 
        FROM users 
        ORDER BY created_at DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Khách hàng | Sweet Bakery</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background-color: #f4f6f9; }
        .sidebar { min-height: 100vh; background: #2c3e50; color: white; width: 260px; position: fixed; top: 0; left: 0; }
        .sidebar a { color: #bdc3c7; padding: 15px 20px; display: block; text-decoration: none; transition: 0.3s; }
        .sidebar a:hover, .sidebar a.active { background: #D4A373; color: white; border-left: 4px solid #fff; }
        .main-content { margin-left: 260px; padding: 25px; }
        .avatar-circle { width: 40px; height: 40px; background-color: #e9ecef; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; color: #6c757d; }
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
        <a href="orders.php"><i class="bi bi-cart-check me-2"></i> Đơn hàng</a>
        <a href="customers.php" class="active"><i class="bi bi-people me-2"></i> Khách hàng</a>
        <div class="mt-5 pt-5">
            <a href="../pages/login.php" class="text-danger"><i class="bi bi-box-arrow-left me-2"></i> Đăng xuất</a>
        </div>
    </div>

    <main class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold text-dark">Quản lý Khách hàng</h4>
        </div>

        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom-0">
                <h6 class="mb-0 fw-bold text-secondary">DANH SÁCH TÀI KHOẢN</h6>
                <div class="input-group" style="width: 300px;">
                    <input type="text" class="form-control form-control-sm" placeholder="Tìm tên, email, sđt...">
                    <button class="btn btn-outline-secondary btn-sm" type="button"><i class="bi bi-search"></i></button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-muted small text-uppercase">
                            <tr>
                                <th class="ps-4">Khách hàng</th>
                                <th>Liên hệ</th>
                                <th>Địa chỉ</th>
                                <th>Ngày đăng ký</th>
                                <th>Vai trò</th>
                                <th class="text-end pe-4">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($result && $result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    // Xử lý Tên và Avatar mặc định (lấy chữ cái đầu)
                                    $fullname = $row['fullname'] ? htmlspecialchars($row['fullname']) : 'Khách vãng lai';
                                    $first_letter = mb_strtoupper(mb_substr($fullname, 0, 1, 'UTF-8'));
                                    
                                    // Xử lý Liên hệ (Email và SĐT)
                                    $email = htmlspecialchars($row['email']);
                                    $phone = $row['phone'] ? htmlspecialchars($row['phone']) : '<span class="text-muted fst-italic small">Chưa cập nhật</span>';
                                    
                                    // Xử lý Địa chỉ
                                    $address = $row['address'] ? htmlspecialchars($row['address']) : '<span class="text-muted fst-italic small">Chưa cập nhật</span>';

                                    // Format Ngày đăng ký
                                    $created_at = date("d/m/Y", strtotime($row['created_at']));
                                    
                                    // Xử lý Phân quyền (Role)
                                    $is_admin = (strtolower($row['role']) === 'admin');
                                    $role_badge = $is_admin ? '<span class="badge bg-danger">Quản trị viên</span>' : '<span class="badge bg-secondary">Khách hàng</span>';

                                    echo "<tr>
                                        <td class='ps-4'>
                                            <div class='d-flex align-items-center'>
                                                <div class='avatar-circle me-3'>{$first_letter}</div>
                                                <div>
                                                    <div class='fw-bold text-dark'>{$fullname}</div>
                                                    <div class='text-muted small'>ID: #USR" . str_pad($row['id'], 3, "0", STR_PAD_LEFT) . "</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class='small'><i class='bi bi-envelope text-muted me-1'></i> {$email}</div>
                                            <div class='small mt-1'><i class='bi bi-telephone text-muted me-1'></i> {$phone}</div>
                                        </td>
                                        <td class='small'>{$address}</td>
                                        <td class='small text-muted'>{$created_at}</td>
                                        <td>{$role_badge}</td>
                                        <td class='text-end pe-4'>
                                            <button class='btn btn-sm btn-light border text-primary' title='Chỉnh sửa'><i class='bi bi-pencil'></i></button>
                                            " . (!$is_admin ? "<button class='btn btn-sm btn-light border text-danger ms-1' title='Khóa tài khoản'><i class='bi bi-lock'></i></button>" : "") . "
                                        </td>
                                    </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='6' class='text-center py-5 text-muted'>Chưa có tài khoản nào trong hệ thống.</td></tr>";
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