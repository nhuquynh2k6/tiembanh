<?php
session_start();
// BẢO MẬT: Kiểm tra quyền Admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../pages/login.php");
    exit();
}

$conn = new mysqli("localhost", "bakery_user", "BakeryPassword123", "bakery_shop");
$conn->set_charset("utf8mb4");

$error = "";
$success = "";

// Lấy danh sách danh mục để hiển thị ra Select Box
$cat_result = $conn->query("SELECT * FROM categories");

// XỬ LÝ KHI NGƯỜI DÙNG BẤM NÚT "LƯU SẢN PHẨM"
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $category_id = $_POST['category_id'];
    $price = $_POST['price'];
    $stock_quantity = $_POST['stock_quantity'];
    $status = $_POST['status'];
    $description = $_POST['description'];
    $image_link = $_POST['image_link'];
    
    $image_name = NULL;

    // Xử lý Upload file ảnh (nếu có chọn file)
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $filename = $_FILES['image']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            // Đổi tên file để không bị trùng (Thêm mốc thời gian vào trước tên file)
            $image_name = time() . '_' . basename($filename);
			$target_dir = "../images/";            
			$target_file = $target_dir . $image_name;
            
            if (!move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                $error = "Lỗi: Không thể tải ảnh lên thư mục uploads. Hãy kiểm tra lại thư mục!";
            }
        } else {
            $error = "Chỉ chấp nhận file ảnh định dạng JPG, JPEG, PNG, GIF, WEBP.";
        }
    }

    // Nếu không có lỗi thì tiến hành lưu vào DB
    if (empty($error)) {
        $stmt = $conn->prepare("INSERT INTO products (category_id, name, price, stock_quantity, status, description, image, image_link) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isdiisss", $category_id, $name, $price, $stock_quantity, $status, $description, $image_name, $image_link);
        
        if ($stmt->execute()) {
            $success = "Thêm bánh mới thành công! Hệ thống sẽ chuyển về danh sách sau 2 giây...";
            header("refresh:2;url=products.php"); // Tự động về trang danh sách
        } else {
            $error = "Lỗi Database: " . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm Sản Phẩm | Sweet Bakery</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background-color: #f4f6f9; }
        .sidebar { min-height: 100vh; background: #2c3e50; color: white; width: 260px; position: fixed; top:0; left:0;}
        .sidebar a { color: #bdc3c7; padding: 15px 20px; display: block; text-decoration: none; transition: 0.3s; }
        .sidebar a:hover, .sidebar a.active { background: #D4A373; color: white; border-left: 4px solid #fff;}
        .main-content { margin-left: 260px; padding: 25px; }
        .form-label { font-weight: 600; color: #495057; font-size: 0.9rem; }
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
            <div>
                <a href="products.php" class="text-decoration-none text-muted small"><i class="bi bi-arrow-left"></i> Quay lại danh sách</a>
                <h4 class="fw-bold text-dark mt-1">Thêm Sản Phẩm Mới</h4>
            </div>
        </div>

        <?php if($error): ?><div class="alert alert-danger p-2 small"><i class="bi bi-exclamation-triangle"></i> <?= $error ?></div><?php endif; ?>
        <?php if($success): ?><div class="alert alert-success p-2 small"><i class="bi bi-check-circle"></i> <?= $success ?></div><?php endif; ?>

        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-4">
                <form action="add_product.php" method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-7 border-end pe-4">
                            <h6 class="fw-bold text-secondary mb-3">THÔNG TIN CƠ BẢN</h6>
                            
                            <div class="mb-3">
                                <label class="form-label">Tên sản phẩm (Bánh) <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" required placeholder="Nhập tên bánh...">
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Danh mục <span class="text-danger">*</span></label>
                                    <select name="category_id" class="form-select" required>
                                        <option value="">-- Chọn danh mục --</option>
                                        <?php 
                                        if($cat_result->num_rows > 0) {
                                            while($cat = $cat_result->fetch_assoc()) {
                                                echo "<option value='{$cat['id']}'>{$cat['name']}</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Giá bán (VNĐ) <span class="text-danger">*</span></label>
                                    <input type="number" name="price" class="form-control" required placeholder="Ví dụ: 50000">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Số lượng tồn kho</label>
                                    <input type="number" name="stock_quantity" class="form-control" value="10">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Trạng thái</label>
                                    <select name="status" class="form-select">
                                        <option value="1" selected>Đang bán (Còn hàng)</option>
                                        <option value="0">Ngừng bán (Hết hàng)</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Mô tả sản phẩm</label>
                                <textarea name="description" class="form-control" rows="4" placeholder="Nhập mô tả chi tiết về bánh (thành phần, hương vị...)"></textarea>
                            </div>
                        </div>

                        <div class="col-md-5 ps-4">
                            <h6 class="fw-bold text-secondary mb-3">HÌNH ẢNH SẢN PHẨM</h6>
                            
                            <div class="mb-4">
                                <label class="form-label">Tải ảnh lên từ máy tính</label>
                                <input type="file" name="image" class="form-control" accept="image/*">
                                <small class="text-muted fst-italic mt-1 d-block">Định dạng hỗ trợ: JPG, PNG, WEBP.</small>
                            </div>

                            <div class="position-relative text-center my-3">
                                <hr class="text-muted m-0">
                                <span class="position-absolute top-50 start-50 translate-middle px-2 bg-white small text-muted text-uppercase" style="font-size: 0.7rem;">Hoặc</span>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Dùng Link ảnh trực tuyến (URL)</label>
                                <input type="url" name="image_link" class="form-control" placeholder="https://...">
                                <small class="text-muted fst-italic">Nếu điền link mạng, hệ thống sẽ ưu tiên hiển thị link này.</small>
                            </div>
                        </div>
                    </div>

                    <hr class="mt-4 mb-4">
                    
                    <div class="text-end">
                        <a href="products.php" class="btn btn-light border px-4 me-2">Hủy bỏ</a>
                        <button type="submit" class="btn text-white fw-bold px-5" style="background-color: #14452F;">LƯU SẢN PHẨM</button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>