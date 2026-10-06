<?php session_start(); ?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đặt hàng thành công | Sweet Bakery</title>
    <link href="../../bootstrap/bootstrap-5.3.8-dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body style="font-family: 'Poppins', sans-serif; background-color: #FAFAFA;">
    <div class="container py-5 text-center" style="max-width: 600px; margin-top: 50px;">
        <i class="bi bi-check-circle-fill text-success" style="font-size: 5rem;"></i>
        <h2 class="fw-bold mt-4 mb-3">Đặt Hàng Thành Công!</h2>
        <p class="text-muted fs-5 mb-4">
            Cảm ơn bạn đã mua sắm tại Sweet Bakery.<br>
            Mã đơn hàng của bạn là: <strong>#ORD<?= str_pad($_GET['id'] ?? '0', 4, "0", STR_PAD_LEFT) ?></strong>
        </p>
        <div class="card border-0 shadow-sm rounded-4 mb-4 text-start">
            <div class="card-body p-4 bg-white rounded-4">
                <p class="mb-1"><i class="bi bi-info-circle me-2 text-warning"></i> Chúng tôi sẽ sớm liên hệ với bạn để xác nhận đơn hàng.</p>
                <p class="mb-0"><i class="bi bi-truck me-2 text-primary"></i> Đơn hàng sẽ được giao đến địa chỉ của bạn trong thời gian sớm nhất.</p>
            </div>
        </div>
        <a href="../index.php" class="btn text-white px-5 py-3 rounded-pill fw-bold" style="background-color: #D4A373;">
            Quay Về Trang Chủ
        </a>
    </div>
</body>
</html>