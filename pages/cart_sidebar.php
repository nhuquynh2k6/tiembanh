<?php
// File này sẽ được include vào index.php
// Đảm bảo $conn đã được khởi tạo ở index.php
?>
<div class="offcanvas offcanvas-end rounded-start-4 border-0 shadow" tabindex="-1" id="cartOffcanvas">
    <div class="offcanvas-header border-bottom py-4 px-4">
        <h5 class="offcanvas-title fw-bold d-flex align-items-center">
            <i class="bi bi-bag-check me-2 text-brand"></i> Giỏ Hàng
        </h5>
        <button type="button" class="btn-close shadow-none" data-bs-dismiss="offcanvas"></button>
    </div>
    
    <div class="offcanvas-body p-4 d-flex flex-column">
        <div class="flex-grow-1">
            <?php
            $total_price = 0;
            if (!empty($_SESSION['cart'])) {
                foreach ($_SESSION['cart'] as $id => $qty) {
                    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
                    $stmt->bind_param("i", $id);
                    $stmt->execute();
                    $item = $stmt->get_result()->fetch_assoc();
                    if ($item) {
                        $total_price += ($item['price'] * $qty);
                        $img = !empty($item['image_link']) ? $item['image_link'] : '../images/'.$item['image'];
            ?>
                <div class="d-flex gap-3 mb-4 align-items-center">
                    <img src="<?= htmlspecialchars($img) ?>" class="rounded-3 object-fit-cover" width="70" height="70" alt="cake">
                    <div class="flex-grow-1">
                        <h6 class="fw-bold mb-1"><?= htmlspecialchars($item['name']) ?></h6>
                        <div class="d-flex align-items-center gap-2">
                            <button onclick="updateCart(<?= $id ?>, 'decrease')" class="btn btn-sm btn-outline-secondary py-0">-</button>
                            <span class="fw-bold"><?= $qty ?></span>
                            <button onclick="updateCart(<?= $id ?>, 'increase')" class="btn btn-sm btn-outline-secondary py-0">+</button>
                        </div>
                    </div>
                    <div class="text-end">
                        <div class="fw-bold text-brand"><?= number_format($item['price'] * $qty, 0, ',', '.') ?>đ</div>
                        <button onclick="updateCart(<?= $id ?>, 'remove')" class="btn btn-link text-danger p-0 small">Xóa</button>
                    </div>
                </div>
            <?php 
                    }
                }
            } else {
                echo "<p class='text-muted text-center'>Giỏ hàng của bạn đang trống.</p>";
            }
            ?>
        </div>
        
        <div class="border-top pt-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <span class="text-muted fw-medium">Tổng thanh toán</span>
                <span class="fs-4 fw-bold text-dark"><?= number_format($total_price, 0, ',', '.') ?>đ</span>
            </div>
            <a href="pages/payment.php" class="btn btn-dark w-100 py-3 rounded-pill fw-bold fs-6 shadow-sm">
                Thanh toán ngay
            </a>
        </div>
    </div>
</div>