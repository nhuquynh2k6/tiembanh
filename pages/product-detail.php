<?php
session_start(); // BẮT BUỘC PHẢI CÓ ĐỂ LƯU GIỎ HÀNG
// ==========================================
// 1. KẾT NỐI DATABASE VÀ XỬ LÝ DỮ LIỆU
// ==========================================
$host = "localhost";
$user = "bakery_user";
$pass = "BakeryPassword123";
$db   = "bakery_shop";

// Tạo kết nối
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Lỗi kết nối CSDL: " . $conn->connect_error);
}
// Set charset tiếng Việt
$conn->set_charset("utf8mb4");

// Lấy ID sản phẩm từ URL (vd: product-detail.php?id=1)
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Truy vấn lấy dữ liệu sản phẩm
$sql = "SELECT * FROM products WHERE id = $id AND status = 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Đổ dữ liệu vào mảng $product
    $product = $result->fetch_assoc();
} else {
    // Nếu sản phẩm không tồn tại
    die("<div style='font-family: sans-serif; text-align:center; margin-top:100px;'>
            <h2 style='color:#D4A373;'>Sản phẩm không tồn tại hoặc đã ngừng kinh doanh.</h2>
            <a href='../index.php' style='color:#333; text-decoration:none; font-weight:bold;'>← Quay lại Trang chủ</a>
         </div>");
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($product['name']) ?> | Sweet Bakery</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #FAFAFA; color: #333; }
        .text-brand { color: #D4A373; }
        .bg-brand { background-color: #D4A373; color: white; }
        .btn-brand { background-color: #D4A373; color: white; border-radius: 50px; font-weight: 500; padding: 10px 24px; transition: all 0.3s ease; }
        .btn-brand:hover { background-color: #bc8f63; color: white; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(212, 163, 115, 0.3); }
        .navbar-custom { background-color: #FFFFFF; box-shadow: 0 2px 15px rgba(0,0,0,0.04); }
        
        .product-image-main { border-radius: 1rem; object-fit: cover; width: 100%; height: 500px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        .qty-input { width: 70px; text-align: center; font-weight: 600; }
        .nav-tabs .nav-link { color: #6c757d; font-weight: 500; border: none; border-bottom: 2px solid transparent; padding: 12px 20px; }
        .nav-tabs .nav-link.active { color: #D4A373; border-bottom: 2px solid #D4A373; background: transparent; }
        .nav-tabs .nav-link:hover { border-color: transparent; color: #D4A373; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-custom sticky-top py-3">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center fw-bold fs-4 text-brand" href="../index.php">
                <i class="bi bi-shop me-2"></i> Sweet Bakery
            </a>
            <div class="ms-auto d-flex align-items-center gap-3">
                <a class="text-dark text-decoration-none fw-medium d-flex align-items-center" href="#">
                    <img src="https://ui-avatars.com/api/?name=K+H&background=D4A373&color=fff&rounded=true" alt="Avatar" width="35" class="me-2">
                    khachhang01
                </a>
                <a href="payment.php" class="btn btn-light position-relative rounded-circle p-2 shadow-sm border-0">
                    <i class="bi bi-bag fs-5 text-dark"></i>
                    <span id="cart-count" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-white">
                        <?= isset($_SESSION['cart']) ? count($_SESSION['cart']) : '0' ?>
                    </span>
                </a>
            </div>
        </div>
    </nav>

    <main class="container py-5 mt-2">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="../index.php" class="text-decoration-none text-muted">Trang Chủ</a></li>
                <li class="breadcrumb-item"><a href="#" class="text-decoration-none text-muted">Sản phẩm</a></li>
                <li class="breadcrumb-item active fw-bold text-dark" aria-current="page"><?= htmlspecialchars($product['name']) ?></li>
            </ol>
        </nav>

        <div class="row g-5 mb-5">
            <div class="col-lg-6">
                <?php 
                    // Kiểm tra dùng link ảnh trực tuyến hay file ảnh cục bộ
                    $image_src = !empty($product['image_link']) ? $product['image_link'] : '../images/' . $product['image']; 
                ?>
                <img src="<?= htmlspecialchars($image_src) ?>" class="product-image-main img-fluid" alt="<?= htmlspecialchars($product['name']) ?>">
            </div>

            <div class="col-lg-6">
                <span class="badge bg-danger rounded-pill fw-medium mb-2 px-3 py-2">Hot</span>
                
                <h1 class="display-6 fw-bold mb-3"><?= htmlspecialchars($product['name']) ?></h1>
                
                <div class="d-flex align-items-center mb-4">
                    <h2 class="fw-bold text-brand mb-0 me-3">
                        <?= number_format($product['price'], 0, ',', '.') ?>đ
                    </h2>
                    <span class="badge bg-light text-success border border-success rounded-pill px-3 py-1">
                        <i class="bi bi-check-circle-fill me-1"></i> Còn <?= $product['stock_quantity'] ?> sản phẩm
                    </span>
                </div>

                <p class="text-muted mb-4 lh-lg">
                    <?= nl2br(htmlspecialchars($product['description'])) ?>
                </p>

                <hr class="text-muted opacity-25 mb-4">

                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="input-group" style="width: 140px;">
                        <button class="btn btn-outline-secondary px-3" type="button" id="btn-minus"><i class="bi bi-dash"></i></button>
                        <input type="text" class="form-control qty-input shadow-none border-secondary" value="1" id="qty-input" readonly>
                        <button class="btn btn-outline-secondary px-3" type="button" id="btn-plus"><i class="bi bi-plus"></i></button>
                    </div>
                    
                    <button type="button" onclick="addToCartDetail(<?= $product['id'] ?>)" class="btn btn-brand flex-grow-1 fs-5 shadow-sm d-flex justify-content-center align-items-center py-2 border-0">
                        <i class="bi bi-cart-plus me-2"></i> Thêm Vào Giỏ
                    </button>
                </div>

                <ul class="list-unstyled text-muted small mt-4 bg-light p-4 rounded-4">
                    <li class="mb-2"><i class="bi bi-shield-check text-brand fs-5 me-2 align-middle"></i> 100% Nguyên liệu tự nhiên cao cấp.</li>
                    <li class="mb-2"><i class="bi bi-box-seam text-brand fs-5 me-2 align-middle"></i> Đóng gói sang trọng, phù hợp làm quà tặng.</li>
                    <li><i class="bi bi-truck text-brand fs-5 me-2 align-middle"></i> Giao hàng tận nơi trong vòng 2 giờ.</li>
                </ul>
            </div>
        </div>

        <div class="row mt-5">
            <div class="col-12">
                <ul class="nav nav-tabs border-bottom" id="productTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active fs-5" id="desc-tab" data-bs-toggle="tab" data-bs-target="#desc" type="button" role="tab">Chính sách bảo quản</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fs-5" id="ingredients-tab" data-bs-toggle="tab" data-bs-target="#ingredients" type="button" role="tab">Thành phần nguyên liệu</button>
                    </li>
                </ul>
                
                <div class="tab-content py-4" id="productTabsContent">
                    <div class="tab-pane fade show active text-muted lh-lg" id="desc" role="tabpanel" tabindex="0">
                        <p>Bánh của Sweet Bakery được nướng mới mỗi ngày bằng kỹ thuật thủ công. Chúng tôi không sử dụng chất bảo quản hay hương liệu nhân tạo.</p>
                        <p><strong>Bảo quản:</strong> Ngon nhất khi dùng trong ngày. Nếu cần bảo quản, vui lòng giữ trong ngăn mát tủ lạnh (2-4°C) và dùng trong vòng 3 ngày.</p>
                    </div>
                    
                    <div class="tab-pane fade" id="ingredients" role="tabpanel" tabindex="0">
                        <div id="xml-ingredients-data" class="table-responsive">
                            <table class="table table-bordered mt-3 text-muted">
                                <thead class="table-light">
                                    <tr>
                                        <th>Nguyên liệu</th>
                                        <th>Nguồn gốc</th>
                                        <th>Lưu ý dị ứng</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr><td>Đang chờ dữ liệu XML...</td><td>...</td><td>...</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="bg-white border-top pt-5 pb-3 mt-5">
        <div class="container">
            <div class="text-center text-muted small">
                &copy; 2026 Dự án Website Bánh Ngọt - Thiết kế bởi Nhóm Sinh viên.
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Tăng giảm số lượng
        document.getElementById('btn-plus').addEventListener('click', function() {
            let input = document.getElementById('qty-input');
            input.value = parseInt(input.value) + 1;
        });
        document.getElementById('btn-minus').addEventListener('click', function() {
            let input = document.getElementById('qty-input');
            if (parseInt(input.value) > 1) {
                input.value = parseInt(input.value) - 1;
            }
        });

        // HÀM XỬ LÝ THÊM VÀO GIỎ HÀNG BẰNG AJAX
        function addToCartDetail(productId) {
            let quantity = document.getElementById('qty-input').value;
            let formData = new FormData();
            formData.append('id', productId);
            formData.append('quantity', quantity);

            // Gửi dữ liệu về file add_to_cart.php nằm ở thư mục gốc
            fetch('add_to_cart.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if(data.status === 'success') {
                    // Cập nhật số màu đỏ trên icon giỏ hàng
                    document.getElementById('cart-count').innerText = data.count;
                } else {
                    alert('Có lỗi xảy ra, vui lòng thử lại.');
                }
            })
            .catch(error => console.error('Error:', error));
        }

        // Đọc dữ liệu thành phần từ XML
        document.getElementById('ingredients-tab').addEventListener('click', function() {
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    var xmlDoc = this.responseXML;
                    var products = xmlDoc.getElementsByTagName("product");
                    var currentName = "<?= htmlspecialchars($product['name']) ?>";
                    
                    let html = '<table class="table table-bordered mt-3 text-muted"><thead class="table-light"><tr><th>Nguyên liệu</th><th>Lưu ý dị ứng</th></tr></thead><tbody>';
                    
                    for (let i = 0; i < products.length; i++) {
                        let xmlName = products[i].getElementsByTagName("name")[0].firstChild.nodeValue;
                        if (xmlName === currentName) {
                            let warning = products[i].getElementsByTagName("warning")[0].firstChild.nodeValue;
                            let items = products[i].getElementsByTagName("item");
                            let list = "";
                            for(let j=0; j<items.length; j++) {
                                list += items[j].firstChild.nodeValue + (j < items.length - 1 ? ", " : "");
                            }
                            html += `<tr><td>${list}</td><td>${warning}</td></tr>`;
                        }
                    }
                    html += '</tbody></table>';
                    document.getElementById("xml-ingredients-data").innerHTML = html;
                }
            };
            xhttp.open("GET", "ingredients.xml", true);
            xhttp.send();
        });
    </script>
</body>
</html>