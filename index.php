<?php
session_start(); // Khởi tạo Session
// ==========================================
// 1. KẾT NỐI DATABASE
// ==========================================
$host = "localhost";
$user = "bakery_user";
$pass = "BakeryPassword123";
$db   = "bakery_shop";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Lỗi kết nối CSDL: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

// 1. Lấy Bánh mới (Trừ Combo 9, Phụ kiện 13)
$sql_new = "SELECT * FROM products WHERE status = 1 AND category_id NOT IN (9, 13) ORDER BY id DESC LIMIT 6";
$result_products = $conn->query($sql_new);

// 2. Lấy Sản phẩm nổi bật (Trừ Phụ kiện 13)
$sql_featured = "SELECT * FROM products WHERE status = 1 AND category_id NOT IN (13) ORDER BY RAND() LIMIT 9";
$result_featured = $conn->query($sql_featured);

// Đổ dữ liệu vào mảng để dùng cho Slider
$featured_items = [];
while($row = $result_featured->fetch_assoc()) {
    $featured_items[] = $row;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sweet Bakery | Tiệm Bánh Hiện Đại</title>
    
	<link href="../bootstrap/bootstrap-5.3.8-dist/css/bootstrap.min.css" rel="stylesheet">    
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
    .dropdown-menu { z-index: 9999 !important; }
</style>
    <style>
        /* ==========================================
           KHUNG 1: STYLE GỐC (Trang Chủ & Bánh mới ra lò)
        ========================================== */
        body { font-family: 'Poppins', sans-serif; background-color: #FAFAFA; color: #333; }
        .text-brand { color: #D4A373; }
        .bg-brand { background-color: #D4A373; color: white; }
        .btn-brand { background-color: #D4A373; color: white; border-radius: 50px; font-weight: 500; padding: 8px 24px; transition: all 0.3s ease; }
        .btn-brand:hover { background-color: #bc8f63; color: white; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(212, 163, 115, 0.3); }
        .navbar-custom { background-color: #FFFFFF; box-shadow: 0 2px 15px rgba(0,0,0,0.04); }
        .product-card { transition: all 0.3s ease; border: 1px solid rgba(0,0,0,0.05) !important; }
        .product-card:hover { transform: translateY(-8px); box-shadow: 0 15px 30px rgba(0,0,0,0.08) !important; }
        .product-card img { height: 280px; object-fit: cover; border-top-left-radius: var(--bs-border-radius-xl); border-top-right-radius: var(--bs-border-radius-xl); }
        #heroBanner .carousel-item { height: 60vh; min-height: 400px; background-color: #111; }
        #heroBanner .carousel-item img { height: 100%; object-fit: cover; opacity: 0.75; }
        #heroBanner .carousel-caption { bottom: 30%; text-shadow: 0 2px 4px rgba(0,0,0,0.5); }

        /* ==========================================
           KHUNG 2: STYLE SẢN PHẨM NỔI BẬT (Thẻ Đen)
        ========================================== */
        .aquatic-style-card {
            background: #000000;
            border: 1px solid #222222;
            border-radius: 0px;
            padding: 0px;
            overflow: hidden;
            transition: box-shadow 0.3s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        .aquatic-style-card:hover { box-shadow: 0 6px 16px rgba(0, 0, 0, 0.3); }
        .cake-img-wrapper { width: 100%; aspect-ratio: 1 / 1; overflow: hidden; background-color: #fcfcfc; }
        .cake-img-wrapper img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.4s ease; }
        .aquatic-style-card:hover .cake-img-wrapper img { transform: scale(1.03); }
        .cake-info-body { padding: 15px 20px 20px 20px; text-align: left; background-color: #000000; display: flex; flex-direction: column; flex-grow: 1; }
        .cake-name { font-size: 0.95rem; font-weight: 600; color: #f1b84f; line-height: 1.4; margin-bottom: 8px; height: 44px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .cake-price { font-size: 1.05rem; font-weight: 600; color: #ffffff; margin-bottom: 15px; }
        .cake-action-buttons { margin-top: auto; display: flex; gap: 10px; }
        .btn-add-cart-custom { background-color: transparent; color: #f1b84f; border: 1px solid #f1b84f; border-radius: 4px; padding: 6px 12px; font-size: 0.9rem; transition: all 0.2s ease; }
        .btn-add-cart-custom:hover { background-color: #f1b84f; color: #000000; }
        .btn-buy-now-custom { background-color: #14452F; color: #ffffff; border: 1px solid #14452F; border-radius: 4px; padding: 6px 16px; font-size: 0.9rem; font-weight: 500; flex-grow: 1; text-align: center; transition: background-color 0.2s ease; }
        .btn-buy-now-custom:hover { background-color: #1a583c; color: #ffffff; }
        
        .product-slider-wrapper { position: relative; }
        .btn-arrow-control { width: 40px; height: 40px; background-color: #ffffff; border: 1px solid #e0e0e0; border-radius: 50%; top: 50%; transform: translateY(-50%); opacity: 0.9; position: absolute; z-index: 10; }
        .btn-arrow-control:hover { background-color: #14452F; }
        .btn-arrow-control-icon { filter: invert(20%) sepia(20%) saturate(1000%) hue-rotate(110deg); }
        .btn-arrow-control:hover .btn-arrow-control-icon { filter: invert(100%); }
        .control-prev-pos { left: -15px; }
        .control-next-pos { right: -15px; }

        /* ==========================================
           KHUNG 3: STYLE TIN TỨC
        ========================================== */
        .news-title-wrapper { border-bottom: 2px solid #333; display: inline-block; padding-bottom: 5px; }
        .news-card { background: #ffffff; border: 1px solid #eaeaea; overflow: hidden; height: 100%; border-radius: 12px; }
        .news-img-wrapper { width: 100%; aspect-ratio: 1.1 / 1; overflow: hidden; }
        .news-img-wrapper img { width: 100%; height: 100%; object-fit: cover; }
        .news-dark-box { background-color: #000000; color: #ffffff; padding: 15px 20px; text-align: left; }
        .news-dark-box h5 { font-size: 0.95rem; font-weight: 600; margin-bottom: 15px; color: #f1b84f; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .news-dark-box .news-date-tag { font-size: 0.75rem; color: #bbbbbb; font-style: italic; display: flex; justify-content: space-between; }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-custom sticky-top py-3">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center fw-bold fs-4 text-brand" href="index.php">
                <img src="images/logo.jpg" alt="Logo" style="height: 45px; width: auto; object-fit: contain;" class="me-2">
                Sweet Bakery
            </a>
            
            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
                <i class="bi bi-list fs-1 text-dark"></i>
            </button>

            <div class="collapse navbar-collapse" id="navMenu">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0 fw-medium">
                    <li class="nav-item"><a class="nav-link active" href="index.php">Trang Chủ</a></li>
					<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Danh Mục</a>
    <ul class="dropdown-menu border-0 shadow-sm rounded-3">
        <?php
        // Truy vấn lại danh sách danh mục từ CSDL
        $cat_query = $conn->query("SELECT * FROM categories");
        while ($cat = $cat_query->fetch_assoc()):
        ?>
        <li>
            <a class="dropdown-item" href="pages/category.php?id=<?= $cat['id'] ?>">
                <?= htmlspecialchars($cat['name']) ?>
            </a>
        </li>
        <?php endwhile; ?>
    </ul>
</li>
<li class="nav-item"><a class="nav-link" href="#">Về Chúng Tôi</a></li>
                </ul>

                <form class="d-flex me-3 d-none d-lg-flex" action="#" method="GET">
                    <div class="input-group">
                        <input class="form-control border-end-0 rounded-start-pill bg-light shadow-none" type="search" name="search" placeholder="Tìm tên bánh...">
                        <button class="btn btn-light border border-start-0 rounded-end-pill bg-light" type="submit">
                            <i class="bi bi-search text-muted"></i>
                        </button>
                    </div>
                </form>

                <div class="d-flex align-items-center gap-3">
					<a href="pages/cart.php" class="btn btn-light position-relative rounded-circle p-2 shadow-sm border-0">
        <i class="bi bi-bag fs-5 text-dark"></i>
        <span id="cart-count" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-white">
            <?= isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : '0' ?>
        </span>
    </a>
					<?php if (isset($_SESSION['user_id'])): ?>
						<div class="dropdown">
							<a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Tài khoản</a>
							<ul class="dropdown-menu">
								<li><a class="dropdown-item" href="pages/profile.php">Hồ sơ</a></li>
								<li><a class="dropdown-item text-danger" href="pages/logout.php">Đăng xuất</a></li>
							</ul>
						</div>
					<?php else: ?>
						<a href="pages/login.php" class="btn btn-outline-dark btn-sm">Đăng nhập</a>
						<a href="pages/register.php" class="btn btn-brand btn-sm">Đăng ký</a>
					<?php endif; ?>
				</div>
			</div>
        </div>
    </nav>

    <header id="heroBanner" class="carousel slide carousel-fade" data-bs-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active" data-bs-interval="4000">
                <img src="https://images.unsplash.com/photo-1578985545062-69928b1d9587?q=80&w=1989&auto=format&fit=crop" class="d-block w-100" alt="Cake">
                <div class="carousel-caption d-none d-md-block">
                    <h1 class="display-3 fw-bold mb-3">Hương Vị Ngọt Ngào</h1>
                    <p class="fs-5 mb-4">Từng chiếc bánh được chế tác bằng sự tận tâm và nguyên liệu cao cấp nhất.</p>
                    <a href="#menu-section" class="btn btn-brand btn-lg">Khám Phá Ngay</a>
                </div>
            </div>
            <div class="carousel-item" data-bs-interval="4000">
                <img src="https://images.unsplash.com/photo-1464195244916-405fa0a82545?q=80&w=2070&auto=format&fit=crop" class="d-block w-100" alt="Croissant">
                <div class="carousel-caption d-none d-md-block">
                    <h1 class="display-3 fw-bold mb-3">Tươi Mới Mỗi Ngày</h1>
                    <p class="fs-5 mb-4">Thưởng thức bánh nướng nóng hổi ra lò vào mỗi buổi sáng.</p>
                    <a href="#menu-section" class="btn btn-brand btn-lg">Đặt Mua Ngay</a>
                </div>
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#heroBanner" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#heroBanner" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
        </button>
    </header>
<section class="container py-5">
        <h2 class="fw-bold mb-4 text-center">Khám Phá Menu</h2>
        <div class="row row-cols-2 row-cols-md-4 row-cols-lg-5 g-3">
            <?php
            // Lấy tất cả danh mục từ CSDL
            $cat_query = $conn->query("SELECT * FROM categories");
            while ($cat = $cat_query->fetch_assoc()):
            ?>
            <div class="col">
                <a href="pages/category.php?id=<?= $cat['id'] ?>" class="text-decoration-none">
                    <div class="card h-100 border-0 shadow-sm rounded-4 text-center p-3 hover-effect">
                        <div class="fs-2 mb-2"><i class="bi bi-cake2 text-brand"></i></div>
                        <h6 class="fw-bold text-dark m-0"><?= htmlspecialchars($cat['name']) ?></h6>
                    </div>
                </a>
            </div>
            <?php endwhile; ?>
        </div>
    </section>

    <style>
        .hover-effect { transition: 0.3s; }
        .hover-effect:hover { background-color: #f8f1e9; transform: translateY(-5px); }
    </style>
    <!-- ==================================================
         KHUNG 1: BÁNH MỚI RA LÒ (DATABASE)
    =================================================== -->
    <main id="menu-section" class="container py-5 mt-4">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <span class="text-brand fw-bold text-uppercase tracking-wider">Thực đơn nổi bật</span>
                <h2 class="display-6 fw-bold mt-2">Bánh Mới Ra Lò</h2>
            </div>
        </div>

        <div class="row g-4">
            <?php
            if ($result_products->num_rows > 0) {
                while ($row = $result_products->fetch_assoc()) {
                    $image_src = !empty($row['image_link']) ? $row['image_link'] : 'images/' . $row['image'];
                    ?>
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card h-100 product-card rounded-4 bg-white">
                            <a href="pages/product-detail.php?id=<?= $row['id'] ?>">
                                <img src="<?= htmlspecialchars($image_src) ?>" class="card-img-top" alt="<?= htmlspecialchars($row['name']) ?>">
                            </a>
                            
                            <div class="card-body p-4 flex-column d-flex">
                                <h5 class="card-title fw-bold mb-0">
                                    <a href="pages/product-detail.php?id=<?= $row['id'] ?>" class="text-dark text-decoration-none">
                                        <?= htmlspecialchars($row['name']) ?>
                                    </a>
                                </h5>
                                <p class="card-text text-muted small mb-4 flex-grow-1">
                                    <?= mb_strimwidth(htmlspecialchars($row['description']), 0, 80, "...") ?>
                                </p>
                                
                                <div class="d-flex justify-content-between align-items-center mt-auto pt-3 border-top">
                                    <span class="fs-5 fw-bold text-brand mb-0">
                                        <?= number_format($row['price'], 0, ',', '.') ?>đ
                                    </span>
                                    <div class="d-flex gap-2">
                                        <button onclick="addToCart(<?= $row['id'] ?>)" class="btn btn-outline-secondary rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;" title="Thêm vào giỏ">
                                            <i class="bi bi-cart-plus fs-5"></i>
                                        </button>
                                        <button onclick="buyNow(<?= $row['id'] ?>)" class="btn btn-brand btn-sm px-3 fw-bold d-flex align-items-center shadow-sm">
                                            Mua Ngay
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo "<div class='col-12 text-center text-muted'><p>Hiện tại cửa hàng đang cập nhật bánh mới, vui lòng quay lại sau!</p></div>";
            }
            ?>
        </div>
    </main>

    <!-- ==================================================
         KHUNG 2: SẢN PHẨM NỔI BẬT (HIỂN THỊ TỪ DATABASE)
    =================================================== -->
    <div class="container py-5 product-slider-wrapper">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <span class="text-brand fw-bold text-uppercase tracking-wider">Lựa chọn hàng đầu</span>
                <h2 class="display-6 fw-bold mt-2">Sản Phẩm Nổi Bật</h2>
            </div>
        </div>

        <div id="cakeProductSlider" class="carousel slide" data-bs-ride="carousel" data-bs-interval="4000">
            <!-- Đã thêm class pb-4 để các nút không bị cắt lẹm -->
            <div class="carousel-inner pb-4" id="dynamic-cake-container">
                <?php
                if (!empty($featured_items)) {
                    // Hàm array_chunk tự động cắt mảng 9 sản phẩm thành các nhóm nhỏ (mỗi nhóm 3 sản phẩm) để nhét vào 1 Slide
                    $chunks = array_chunk($featured_items, 3);
                    foreach ($chunks as $index => $chunk) {
                        $activeClass = ($index === 0) ? 'active' : '';
                        echo "<div class='carousel-item {$activeClass}'><div class='row g-4 px-1'>";
                        
                        foreach ($chunk as $cake) {
                            $img = !empty($cake['image_link']) ? $cake['image_link'] : 'images/' . $cake['image'];
                            $price = number_format($cake['price'], 0, ',', '.') . 'đ';
                            $id = $cake['id'];
                            ?>
                            <div class="col-12 col-md-4">
                                <div class="aquatic-style-card shadow-sm h-100">
                                    <a href="pages/product-detail.php?id=<?= $id ?>">
                                        <div class="cake-img-wrapper">
                                            <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($cake['name']) ?>">
                                        </div>
                                    </a>
                                    <div class="cake-info-body">
                                        <a href="pages/product-detail.php?id=<?= $id ?>" class="text-decoration-none">
                                            <div class="cake-name"><?= htmlspecialchars($cake['name']) ?></div>
                                        </a>
                                        <div class="cake-price"><?= $price ?></div>
                                        <div class="cake-action-buttons">
                                            <button class="btn btn-add-cart-custom" onclick="addToCart(<?= $id ?>)" title="Thêm vào giỏ hàng">
                                                <i class="bi bi-cart-plus"></i>
                                            </button>
                                            <button class="btn btn-buy-now-custom" onclick="buyNow(<?= $id ?>)">
                                                Mua ngay
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                        echo "</div></div>";
                    }
                } else {
                    echo "<div class='w-100 text-center text-muted'>Chưa có sản phẩm nổi bật</div>";
                }
                ?>
            </div>
            
            <button class="carousel-control-prev btn-arrow-control control-prev-pos shadow-sm" type="button" data-bs-target="#cakeProductSlider" data-bs-slide="prev">
                <span class="carousel-control-prev-icon btn-arrow-control-icon" aria-hidden="true"></span>
            </button>
            <button class="carousel-control-next btn-arrow-control control-next-pos shadow-sm" type="button" data-bs-target="#cakeProductSlider" data-bs-slide="next">
                <span class="carousel-control-next-icon btn-arrow-control-icon" aria-hidden="true"></span>
            </button>
        </div>
    </div>

    <!-- ==================================================
         KHUNG 3: TIN TỨC
    =================================================== -->
    <div class="container py-5 mb-5">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <span class="text-brand fw-bold text-uppercase tracking-wider">Góc chia sẻ</span>
                <h2 class="display-6 fw-bold mt-2">Tin Tức & Sự Kiện</h2>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-12 col-md-4">
                <div class="news-card shadow-sm">
                    <div class="news-img-wrapper"><img src="https://i.pinimg.com/736x/6f/46/13/6f4613f951243c2fe5af5f5a3fcd0d2b.jpg" alt="Tin tức 1"></div>
                    <div class="news-dark-box">
                        <h5>Hành trình tìm kiếm nguyên liệu làm bánh của tụi mình</h5>
                        <div class="news-date-tag"><span>Ngày 17/06/2026</span><span>Tin tức</span></div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="news-card shadow-sm">
                    <div class="news-img-wrapper"><img src="https://i.pinimg.com/736x/9c/34/3d/9c343da5782bc9849684e69f6ab129a3.jpg" alt="Tin tức 2"></div>
                    <div class="news-dark-box">
                        <h5>Bí quyết giữ độ mềm xốp của cốt bánh Red Velvet suốt cả ngày dài</h5>
                        <div class="news-date-tag"><span>Ngày 15/05/2026</span><span>Tin công ty</span></div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="news-card shadow-sm">
                    <div class="news-img-wrapper"><img src="https://i.pinimg.com/736x/3b/03/f6/3b03f60687b51343b23fa9384f14c11f.jpg" alt="Tin tức 3"></div>
                    <div class="news-dark-box">
                        <h5>Sweet Bakery khai trương chi nhánh mới tràn ngập ưu đãi hấp dẫn</h5>
                        <div class="news-date-tag"><span>Ngày 01/04/2026</span><span>Sự kiện</span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <?php 
    $is_root = true; 
    include 'pages/footer.php'; 
    ?>
    <script src="../bootstrap/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>

<script>
    // 1. Hàm thêm vào giỏ hàng
    function sendCartRequest(productId, isBuyNow) {
        let formData = new FormData();
        formData.append('id', productId);
        formData.append('quantity', 1);

        fetch('pages/add_to_cart.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if(data.status === 'success') {
                document.getElementById('cart-count').innerText = data.count;
                if(isBuyNow) {
                    window.location.href = 'pages/payment.php';
                }
            } else {
                alert("Lỗi: " + data.message);
            }
        });
    }

    function addToCart(productId) { sendCartRequest(productId, false); }
    function buyNow(productId) { sendCartRequest(productId, true); }

    // 2. Hàm cập nhật giỏ hàng (+/-/Xóa)
    function updateCart(productId, action) {
        let formData = new FormData();
        formData.append('id', productId);
        formData.append('action', action);

        fetch('pages/cart_action.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            location.reload(); // Tải lại để cập nhật danh sách và tổng tiền
        });
    }
</script>

<script src="../bootstrap/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>