<?php
session_start();
if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$action = isset($_POST['action']) ? $_POST['action'] : ''; // 'increase', 'decrease', 'remove'

if ($id > 0) {
    if ($action == 'remove') {
        unset($_SESSION['cart'][$id]);
    } elseif ($action == 'increase') {
        $_SESSION['cart'][$id]++;
    } elseif ($action == 'decrease') {
        $_SESSION['cart'][$id]--;
        if ($_SESSION['cart'][$id] <= 0) unset($_SESSION['cart'][$id]);
    }
}
// Trả về số lượng mới để cập nhật giao diện
echo json_encode(['count' => array_sum($_SESSION['cart'])]);
?>