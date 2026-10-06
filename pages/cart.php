<?php
session_start();

// Kết nối CSDL
$conn = new mysqli("localhost", "bakery_user", "BakeryPassword123", "bakery_shop");
$conn->set_charset("utf8mb4");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giỏ Hàng | Sweet Bakery</title>
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
    <div class="container py-5">
        <div class="d-flex align-items-center mb-4">
            <a href="../index.php" class="text-dark text-decoration-none me-3"><i class="bi bi-arrow-left fs-4"></i></a>
            <h2 class="fw-bold mb-0">Giỏ hàng của bạn</h2>
        </div>

        <div class="row">
            <div class="col-lg-8 mb-4">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <?php
                        $total_price = 0;
                        if (!empty($_SESSION['cart'])) {
                            echo '<div class="table-responsive"><table class="table align-middle border-bottom-0">';
                            echo '<thead><tr class="text-muted"><th colspan="2">Sản phẩm</th><th>Đơn giá</th><th>Số lượng</th><th class="text-end">Thành tiền</th><th></th></tr></thead>';
                            echo '<tbody>';
                            
                            foreach ($_SESSION['cart'] as $id => $qty) {
                                $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
                                $stmt->bind_param("i", $id);
                                $stmt->execute();
                                $item = $stmt->get_result()->fetch_assoc();
                                
                                if ($item) {
                                    $subtotal = $item['price'] * $qty;
                                    $total_price += $subtotal;
                                    $img = !empty($item['image_link']) ? $item['image_link'] : '../images/'.$item['image'];
                                    ?>
                                    <tr>
                                        <td style="width: 80px;">
                                            <img src="<?= htmlspecialchars($img) ?>" class="rounded-3 object-fit-cover" width="70" height="70">
                                        </td>
                                        <td class="fw-bold"><?= htmlspecialchars($item['name']) ?></td>
                                        <td class="text-muted"><?= number_format($item['price'], 0, ',', '.') ?>đ</td>
                                        <td>
                                            <div class="input-group input-group-sm" style="width: 100px;">
                                                <button class="btn btn-outline-secondary" onclick="updateCart(<?= $id ?>, 'decrease')">-</button>
                                                <input type="text" class="form-control text-center fw-bold px-0" value="<?= $qty ?>" readonly>
                                                <button class="btn btn-outline-secondary" onclick="updateCart(<?= $id ?>, 'increase')">+</button>
                                            </div>
                                        </td>
                                        <td class="text-end fw-bold text-brand"><?= number_format($subtotal, 0, ',', '.') ?>đ</td>
                                        <td class="text-end">
                                            <button onclick="updateCart(<?= $id ?>, 'remove')" class="btn btn-sm text-danger"><i class="bi bi-trash fs-5"></i></button>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            }
                            echo '</tbody></table></div>';
                        } else {
                            echo "<div class='text-center py-5'>
                                    <i class='bi bi-cart-x text-muted' style='font-size: 4rem;'></i>
                                    <h5 class='mt-3'>Giỏ hàng trống</h5>
                                    <p class='text-muted'>Chưa có sản phẩm nào trong giỏ hàng của bạn.</p>
                                    <a href='../index.php' class='btn btn-brand rounded-pill px-4'>Tiếp tục mua sắm</a>
                                  </div>";
                        }
                        ?>
                    </div>
                </div>
            </div>

            <?php if (!empty($_SESSION['cart'])): ?>
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 sticky-top" style="top: 20px;">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4">Tóm tắt đơn hàng</h5>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Tạm tính</span>
                            <span class="fw-bold"><?= number_format($total_price, 0, ',', '.') ?>đ</span>
                        </div>
                        <hr class="border-secondary opacity-25">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <span class="fw-bold fs-5">Tổng cộng</span>
                            <span class="fw-bold fs-4 text-brand"><?= number_format($total_price, 0, ',', '.') ?>đ</span>
                        </div>
                        <a href="payment.php" class="btn btn-brand w-100 py-3 rounded-pill fw-bold fs-6 shadow-sm">
                            Tiến hành thanh toán
                        </a>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
    function updateCart(productId, action) {
        let formData = new FormData();
        formData.append('id', productId);
        formData.append('action', action);

        // Gọi đến file cart_action.php (vì cart.php và cart_action.php cùng nằm trong thư mục pages)
        fetch('cart_action.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            // Load lại trang để cập nhật bảng giá
            location.reload(); 
        })
        .catch(error => console.error('Lỗi:', error));
    }
    </script>
</body>
</html>