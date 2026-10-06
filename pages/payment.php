<?php
session_start();

// Nếu giỏ hàng trống thì không cho vào trang thanh toán, đẩy về trang chủ
if (empty($_SESSION['cart'])) {
    header("Location: ../index.php");
    exit();
}

// Kết nối CSDL
$conn = new mysqli("localhost", "bakery_user", "BakeryPassword123", "bakery_shop");
$conn->set_charset("utf8mb4");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh Toán | Sweet Bakery</title>
    <link href="../../bootstrap/bootstrap-5.3.8-dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #FAFAFA; color: #333; }
        .text-brand { color: #D4A373; }
        .bg-brand { background-color: #D4A373; color: white; }
        .btn-brand { background-color: #D4A373; color: white; transition: 0.3s; border: none; }
        .btn-brand:hover { background-color: #bc8f63; color: white; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(212, 163, 115, 0.3); }
        .form-control:focus, .form-select:focus { border-color: #D4A373; box-shadow: 0 0 0 0.25rem rgba(212, 163, 115, 0.25); }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="mb-4">
            <a href="cart.php" class="text-dark text-decoration-none"><i class="bi bi-arrow-left me-2"></i>Quay lại giỏ hàng</a>
        </div>
        <h2 class="fw-bold mb-5">Tiến hành thanh toán</h2>

        <form action="process_payment.php" method="POST">
            <div class="row g-5">
                
                <div class="col-lg-7">
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-body p-4">
                            <h5 class="fw-bold mb-4"><i class="bi bi-person-lines-fill text-brand me-2"></i>Thông tin giao hàng</h5>
                            
                            <div class="mb-3">
                                <label class="form-label fw-medium">Họ và tên người nhận *</label>
                                <input type="text" name="fullname" class="form-control" required placeholder="Nhập họ tên đầy đủ">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label fw-medium">Số điện thoại *</label>
                                <input type="tel" name="phone" class="form-control" required placeholder="Nhập số điện thoại liên hệ">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label fw-medium">Địa chỉ giao hàng *</label>
                                <textarea name="address" class="form-control" rows="3" required placeholder="Nhập số nhà, tên đường, phường/xã, quận/huyện..."></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-medium">Ghi chú đơn hàng (Tùy chọn)</label>
                                <textarea name="note" class="form-control" rows="2" placeholder="Ví dụ: Giao giờ hành chính, viết thiệp chúc mừng..."></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-body p-4">
                            <h5 class="fw-bold mb-3"><i class="bi bi-credit-card text-brand me-2"></i>Phương thức thanh toán</h5>
                            
                            <div class="form-check mb-3 border p-3 rounded-3">
                                <input class="form-check-input mt-1" type="radio" name="payment_method" id="cod" value="COD" checked>
                                <label class="form-check-label ms-2 d-block" for="cod">
                                    <span class="fw-bold text-dark">Thanh toán khi nhận hàng (COD)</span><br>
                                    <small class="text-muted">Nhận bánh và thanh toán trực tiếp bằng tiền mặt.</small>
                                </label>
                            </div>
                            
                            <div class="form-check border p-3 rounded-3">
                                <input class="form-check-input mt-1" type="radio" name="payment_method" id="banking" value="Banking">
                                <label class="form-check-label ms-2 d-block" for="banking">
                                    <span class="fw-bold text-dark">Chuyển khoản ngân hàng</span><br>
                                    <small class="text-muted">Quét mã QR để thanh toán. Đơn hàng sẽ được xử lý sau khi nhận được tiền.</small>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="card border-0 shadow-sm rounded-4 sticky-top" style="top: 20px;">
                        <div class="card-body p-4">
                            <h5 class="fw-bold mb-4">Tóm tắt đơn hàng</h5>
                            
                            <div class="order-items mb-4">
                                <?php
                                $total_price = 0;
                                // Lấy dữ liệu từ giỏ hàng
                                foreach ($_SESSION['cart'] as $id => $qty) {
                                    $stmt = $conn->prepare("SELECT name, price, image, image_link FROM products WHERE id = ?");
                                    $stmt->bind_param("i", $id);
                                    $stmt->execute();
                                    $item = $stmt->get_result()->fetch_assoc();
                                    
                                    if ($item) {
                                        $subtotal = $item['price'] * $qty;
                                        $total_price += $subtotal;
                                        $img = !empty($item['image_link']) ? $item['image_link'] : '../images/'.$item['image'];
                                        ?>
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="position-relative">
                                                    <img src="<?= htmlspecialchars($img) ?>" class="rounded-3 object-fit-cover" width="60" height="60">
                                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-secondary border border-white"><?= $qty ?></span>
                                                </div>
                                                <span class="fw-medium text-dark text-truncate" style="max-width: 150px;"><?= htmlspecialchars($item['name']) ?></span>
                                            </div>
                                            <span class="fw-bold text-dark"><?= number_format($subtotal, 0, ',', '.') ?>đ</span>
                                        </div>
                                        <?php
                                    }
                                }
                                ?>
                            </div>

                            <hr class="border-secondary opacity-25 mb-4">
                            
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Tạm tính</span>
                                <span class="fw-medium"><?= number_format($total_price, 0, ',', '.') ?>đ</span>
                            </div>
                            <div class="d-flex justify-content-between mb-4">
                                <span class="text-muted">Phí vận chuyển</span>
                                <span class="fw-medium">Được tính lúc giao hàng</span>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-4 pt-3 border-top">
                                <span class="fw-bold fs-5">Tổng thanh toán</span>
                                <span class="fw-bold fs-4 text-brand"><?= number_format($total_price, 0, ',', '.') ?>đ</span>
                                <input type="hidden" name="total_amount" value="<?= $total_price ?>">
                            </div>
                            
                            <button type="submit" class="btn btn-brand w-100 py-3 rounded-pill fw-bold fs-6">
                                Xác Nhận Đặt Hàng
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </form>
    </div>

    <script src="../../bootstrap/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>