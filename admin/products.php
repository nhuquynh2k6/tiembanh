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

// TRUY VẤN: Nối bảng products và categories để lấy tên danh mục
// Thay đổi ORDER BY ở cuối dòng
$sql = "SELECT p.*, c.name as category_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        ORDER BY c.name ASC, p.name ASC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Sản phẩm | Sweet Bakery</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background-color: #f4f6f9; }
        .sidebar { min-height: 100vh; background: #2c3e50; color: white; width: 260px; position: fixed; top: 0; left: 0; }
        .sidebar a { color: #bdc3c7; padding: 15px 20px; display: block; text-decoration: none; transition: 0.3s; }
        .sidebar a:hover, .sidebar a.active { background: #D4A373; color: white; border-left: 4px solid #fff; }
        .main-content { margin-left: 260px; padding: 25px; }
        .table-image { width: 55px; height: 55px; object-fit: cover; border-radius: 8px; border: 1px solid #ddd; }
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
        <a href="products.php" class="active"><i class="bi bi-cake2 me-2"></i> Sản phẩm</a>
        <a href="orders.php"><i class="bi bi-cart-check me-2"></i> Đơn hàng</a>
        <a href="customers.php"><i class="bi bi-people me-2"></i> Khách hàng</a>
        <div class="mt-5 pt-5">
            <a href="../pages/login.php" class="text-danger"><i class="bi bi-box-arrow-left me-2"></i> Đăng xuất</a>
        </div>
    </div>

    <main class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold text-dark">Quản lý Sản phẩm</h4>
            <a href="add_product.php" class="btn text-white fw-semibold" style="background-color: #D4A373;">
                <i class="bi bi-plus-circle me-1"></i> Thêm bánh mới
            </a>
        </div>

        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom-0">
                <h6 class="mb-0 fw-bold text-secondary">DANH SÁCH BÁNH</h6>
                <div class="input-group" style="width: 300px;">
                    <input type="text" class="form-control form-control-sm" placeholder="Tìm tên bánh, ID...">
                    <button class="btn btn-outline-secondary btn-sm" type="button"><i class="bi bi-search"></i></button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-muted small text-uppercase">
                            <tr>
                                <th class="ps-4">Mã SP</th>
                                <th>Hình ảnh</th>
                                <th>Tên sản phẩm</th>
                                <th>Danh mục</th>
                                <th>Giá bán</th>
                                <th>Tồn kho</th>
                                <th>Trạng thái</th>
                                <th class="text-end pe-4">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($result && $result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    // 1. Tạo mã SP đẹp
                                    $display_id = "#SP" . str_pad($row['id'], 3, "0", STR_PAD_LEFT);
                                    
                                    // 2. Format Giá tiền
                                    $price = number_format($row['price'], 0, ',', '.') . 'đ';
                                    
                                    // 3. Xử lý Trạng thái (1: Đang bán, 0: Ngừng bán)
                                    $status_text = ($row['status'] == 1) ? 'Đang bán' : 'Ngừng bán';
                                    $status_badge = ($row['status'] == 1) ? 'bg-success' : 'bg-secondary';
                                    
                                    // 4. Xử lý logic Hình ảnh
                                    $img_src = 'https://via.placeholder.com/55?text=No+Image'; // Mặc định
                                    if (!empty($row['image_link'])) {
                                        $img_src = $row['image_link']; // Ưu tiên link online
                                    } elseif (!empty($row['image'])) {
                                        $img_src = '../images/' . $row['image']; // Lấy ảnh tải lên
                                    }
                                    // 5. Tên danh mục (Nếu bị null thì báo 'Chưa phân loại')
                                    $cat_name = $row['category_name'] ? htmlspecialchars($row['category_name']) : '<span class="text-muted fst-italic">Chưa phân loại</span>';

                                    echo "<tr>
                                        <td class='ps-4 fw-bold text-secondary'>{$display_id}</td>
                                        <td><img src='{$img_src}' class='table-image shadow-sm' alt='img'></td>
                                        <td class='fw-bold text-dark'>" . htmlspecialchars($row['name']) . "</td>
                                        <td class='small'>{$cat_name}</td>
                                        <td class='text-danger fw-bold'>{$price}</td>
                                        <td>" . $row['stock_quantity'] . " cái</td>
                                        <td><span class='badge {$status_badge}'>{$status_text}</span></td>
                                        <td class='text-end pe-4'>
                                            <a href='edit_product.php?id={$row['id']}' class='btn btn-sm btn-light border text-primary me-1'><i class='bi bi-pencil-square'></i></a>
                                            <a href='delete_product.php?id={$row['id']}' class='btn btn-sm btn-light border text-danger' onclick='return confirm(\"Bạn có chắc chắn muốn xóa bánh này khỏi hệ thống?\")'><i class='bi bi-trash'></i></a>
                                        </td>
                                    </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='8' class='text-center py-5 text-muted'>Chưa có dữ liệu bánh nào trong hệ thống.</td></tr>";
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