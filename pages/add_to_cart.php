<?php
// Tắt toàn bộ thông báo lỗi PHP để tránh làm hỏng định dạng JSON
error_reporting(0);

// Khai báo cho trình duyệt biết đây là file JSON
header('Content-Type: application/json');

session_start();

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

if ($id > 0) {
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    
    if (isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id] += $quantity;
    } else {
        $_SESSION['cart'][$id] = $quantity;
    }
    
    // Xuất kết quả JSON chuẩn
    echo json_encode([
        'status' => 'success', 
        'count' => array_sum($_SESSION['cart'])
    ]);
    exit();
}

// Nếu lỗi, trả về JSON lỗi
echo json_encode(['status' => 'error', 'message' => 'Invalid ID']);
?>