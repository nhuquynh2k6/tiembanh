<?php
session_start();
$conn = new mysqli("localhost", "bakery_user", "BakeryPassword123", "bakery_shop");
$conn->set_charset("utf8mb4");

$cat_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$cat_name = "Danh mục";

// Lấy tên danh mục
$cat_result = $conn->query("SELECT name FROM categories WHERE id=$cat_id");
if ($cat_result && $cat_result->num_rows > 0) {
    $cat_name = $cat_result->fetch_assoc()['name'];
}

// Lấy sản phẩm thuộc danh mục
$products = $conn->query("SELECT * FROM products WHERE category_id=$cat_id AND status=1");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($cat_name) ?> | Sweet Bakery</title>
    
    <!-- Nạp Bootstrap & Icons & Fonts giống trang chủ -->
    <link href="../../bootstrap/bootstrap-5.3.8-dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #FAFAFA; color: #333; }
        .text-brand { color: #D4A373; }
        .bg-brand { background-color: #D4A373; color: white; }
        .btn-brand { background-color: #D4A373; color: white; border-radius: 50px; font-weight: 500; padding: 8px 24px; transition: all 0.3s ease; border: none; }
        .btn-brand:hover { background-color: #bc8f63; color: white; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(212, 163, 115, 0.3); }
        .navbar-custom { background-color: #FFFFFF; box-shadow: 0 2px 15px rgba(0,0,0,0.04); }
        .product-card { transition: all 0.3s ease; border: 1px solid rgba(0,0,0,0.05) !important; }
        .product-card:hover { transform: translateY(-8px); box-shadow: 0 15px 30px rgba(0,0,0,0.08) !important; }
    </style>
</head>
<body>

    <!-- THANH MENU GỌN GÀNG -->
    <nav class="navbar navbar-expand-lg navbar-custom sticky-top py-3 mb-4">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center fw-bold fs-4 text-brand" href="../index.php">
                <img src="../images/logo.jpg" alt="Logo" style="height: 45px; width: auto; object-fit: contain;" class="me-2">
                Sweet Bakery
            </a>
            
            <div class="d-flex align-items-center gap-3 ms-auto">
                <!-- Nút Giỏ hàng -->
                <a href="cart.php" class="btn btn-light position-relative rounded-circle p-2 shadow-sm border-0">
                    <i class="bi bi-bag fs-5 text-dark"></i>
                    <span id="cart-count" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-white">
                        <?= isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : '0' ?>
                    </span>					
                </a>
                
                <!-- Nút Tài khoản -->
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="profile.php" class="btn btn-outline-dark btn-sm rounded-pill px-3">Hồ sơ</a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-outline-dark btn-sm rounded-pill px-3">Đăng nhập</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- NỘI DUNG CHÍNH -->
    <div class="container py-4 mb-5">
        <div class="d-flex align-items-center mb-4">
            <a href="../index.php" class="text-dark text-decoration-none me-3"><i class="bi bi-arrow-left fs-4"></i></a>
            <h2 class="fw-bold mb-0"><?= htmlspecialchars($cat_name) ?></h2>
        </div>

        <div class="row row-cols-1 row-cols-md-3 row-cols-lg-4 g-4">
            <?php if ($products && $products->num_rows > 0): ?>
                <?php while($row = $products->fetch_assoc()): ?>
                <div class="col">
                    <div class="card h-100 product-card rounded-4 bg-white border-0 shadow-sm">
                        <!-- Hiển thị ảnh -->
                        <img src="<?= !empty($row['image_link']) ? htmlspecialchars($row['image_link']) : '../images/'.htmlspecialchars($row['image']) ?>" class="card-img-top rounded-top-4" style="height: 220px; object-fit:cover" alt="<?= htmlspecialchars($row['name']) ?>">
                        
                        <div class="card-body p-4 d-flex flex-column">
                            <h5 class="card-title fw-bold mb-3 fs-6"><?= htmlspecialchars($row['name']) ?></h5>
                            
                            <div class="mt-auto d-flex justify-content-between align-items-center pt-3 border-top">
                                <span class="fw-bold text-brand mb-0"><?= number_format($row['price'], 0, ',', '.') ?>đ</span>
                                
                                <!-- NÚT THÊM GIỎ HÀNG -->
                                <button onclick="addToCart(<?= $row['id'] ?>)" class="btn btn-brand btn-sm px-3 fw-medium shadow-sm">
                                    Thêm vào giỏ
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <i class="bi bi-box-seam text-muted" style="font-size: 3rem;"></i>
                    <p class="mt-3 text-muted">Chưa có sản phẩm nào trong danh mục này.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="../../bootstrap/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- SCRIPT CHUẨN XỬ LÝ NÚT GIỎ HÀNG -->
    <script>
    function addToCart(productId) {
        let formData = new FormData();
        formData.append('id', productId);
        formData.append('quantity', 1);

        // Gọi trực tiếp file add_to_cart.php vì nó nằm cùng thư mục pages/
        fetch('add_to_cart.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if(data.status === 'success') {
                // Cập nhật số lượng trên icon giỏ hàng
                document.getElementById('cart-count').innerText = data.count;
                alert("Đã thêm thành công vào giỏ hàng!");
            } else {
                alert("Lỗi: " + data.message);
            }
        })
        .catch(error => console.error('Lỗi:', error));
    }
    </script>
</body>
</html>