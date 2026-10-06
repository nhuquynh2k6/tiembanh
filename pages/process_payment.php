<?php
session_start();

// 1. Kiểm tra an toàn: Phải là POST và giỏ hàng không được trống
if ($_SERVER['REQUEST_METHOD'] != 'POST' || empty($_SESSION['cart'])) {
    header("Location: ../index.php");
    exit();
}

// 2. Kết nối CSDL
$conn = new mysqli("localhost", "bakery_user", "BakeryPassword123", "bakery_shop");
$conn->set_charset("utf8mb4");

// 3. Nhận dữ liệu từ form thanh toán
$fullname = $_POST['fullname'] ?? '';
$phone = $_POST['phone'] ?? '';
$address = $_POST['address'] ?? '';
$note = $_POST['note'] ?? '';
$payment_method = $_POST['payment_method'] ?? 'COD';

// ID người dùng (nếu có đăng nhập)
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : NULL; 

// --- BƯỚC ĐỘT PHÁ: Gộp thông tin để nhét vừa vào cột shipping_address của em ---
$full_shipping_info = "Người nhận: " . $fullname . "\n";
$full_shipping_info .= "SĐT: " . $phone . "\n";
$full_shipping_info .= "Địa chỉ: " . $address;
if (!empty($note)) {
    $full_shipping_info .= "\nGhi chú: " . $note;
}

// 4. Tính toán lại Tổng tiền từ CSDL (Chống hack giá)
$total_amount = 0;
$order_items = [];

foreach ($_SESSION['cart'] as $id => $qty) {
    $stmt = $conn->prepare("SELECT id, price FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    
    if ($res) {
        $total_amount += ($res['price'] * $qty);
        $order_items[] = [
            'product_id' => $res['id'],
            'price' => $res['price'],
            'quantity' => $qty
        ];
    }
}

// 5. Bắt đầu Transaction (Đảm bảo an toàn dữ liệu)
$conn->begin_transaction();

try {
    // 5.1 Lưu thông tin vào bảng `orders` khớp với cấu trúc ảnh em gửi
    $status = 'Pending'; // Trạng thái chữ mặc định của em
    
    $stmt_order = $conn->prepare("INSERT INTO orders (user_id, total_amount, payment_method, status, shipping_address) VALUES (?, ?, ?, ?, ?)");
    
    // Bind param: user_id (int), total_amount (double), payment_method (string), status (string), shipping_address (string)
    $stmt_order->bind_param("idsss", $user_id, $total_amount, $payment_method, $status, $full_shipping_info);
    $stmt_order->execute();
    
    // Lấy mã đơn hàng (id) vừa tạo ra
    $order_id = $conn->insert_id; 

    // 5.2 Lưu chi tiết từng loại bánh vào bảng `order_details`
    $stmt_detail = $conn->prepare("INSERT INTO order_details (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
    
    // (Tùy chọn) Trừ số lượng tồn kho (nếu em có cột stock_quantity trong bảng products)
    // $stmt_update_stock = $conn->prepare("UPDATE products SET stock_quantity = stock_quantity - ? WHERE id = ?");

    foreach ($order_items as $item) {
        // Thêm chi tiết đơn
        // order_id (int), product_id (int), quantity (int), price (double)
        $stmt_detail->bind_param("iiid", $order_id, $item['product_id'], $item['quantity'], $item['price']);
        $stmt_detail->execute();
    }

    // 5.3 Thành công -> Xác nhận lưu (Commit)
    $conn->commit();

    // 6. Xóa giỏ hàng
    unset($_SESSION['cart']);

    // 7. Chuyển sang trang báo thành công
    header("Location: order_success.php?id=" . $order_id);
    exit();

} catch (Exception $e) {
    // Nếu lỗi, hủy bỏ mọi thao tác (Rollback)
    $conn->rollback();
    die("Có lỗi xảy ra trong quá trình đặt hàng: " . $e->getMessage());
}
?>