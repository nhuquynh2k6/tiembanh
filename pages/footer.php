<?php
// Xử lý đường dẫn linh hoạt:
// Nếu biến $is_root được khai báo (ở index.php), đường dẫn sẽ không có '../'
// Nếu gọi từ các trang con trong thư mục pages, đường dẫn sẽ tự động lùi ra ngoài '../'
$prefix = isset($is_root) ? '' : '../';
?>

<footer class="bg-white border-top pt-5 pb-3 mt-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-12 col-md-5">
                <div class="d-flex align-items-center mb-3">
                    <img src="<?= $prefix ?>images/logo.jpg"" alt="Logo Nhóm" width="45" height="45" class="me-2 object-fit-cover rounded-circle border">
                    <h4 class="fw-bold text-brand mb-0">Sweet Bakery</h4>
                </div>
                <p class="text-muted small pe-lg-5">Mang đến những chiếc bánh ngọt ngào nhất từ nguyên liệu cao cấp. Hương vị được chế tác từ sự tận tâm của đội ngũ thợ làm bánh chuyên nghiệp.</p>
            </div>
            <div class="col-6 col-md-3">
                <h6 class="fw-bold mb-3">Khám Phá</h6>
                <ul class="list-unstyled text-muted small">
                    <li><a href="<?= $prefix ?>index.php" class="text-decoration-none text-muted mb-2 d-block">Trang Chủ</a></li>
                    <li><a href="#" class="text-decoration-none text-muted mb-2 d-block">Thực Đơn</a></li>
                    <li><a href="#" class="text-decoration-none text-muted mb-2 d-block">Về Chúng Tôi</a></li>
                </ul>
            </div>
            <div class="col-6 col-md-4">
                <h6 class="fw-bold mb-3">Liên Hệ</h6>
                <ul class="list-unstyled text-muted small">
                    <li class="mb-2"><i class="bi bi-geo-alt me-2 text-brand"></i>Đại học Công Nghệ - Khu CNC</li>
                    <li class="mb-2"><i class="bi bi-telephone me-2 text-brand"></i>090 123 4567</li>
                    <li class="mb-2"><i class="bi bi-envelope me-2 text-brand"></i>contact@sweetbakery.vn</li>
                </ul>
            </div>
        </div>
        <hr class="text-muted my-4">
        <div class="text-center text-muted small">
            &copy; 2026 Dự án Website Bánh Ngọt - Thiết kế bởi Nhóm Sinh viên.
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>