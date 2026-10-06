<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') { exit(); }

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $conn = new mysqli("localhost", "bakery_user", "BakeryPassword123", "bakery_shop");
    
    // Xóa ảnh cũ trước (để đỡ tốn dung lượng host)
    $stmt_img = $conn->prepare("SELECT image FROM products WHERE id = ?");
    $stmt_img->bind_param("i", $id);
    $stmt_img->execute();
    $row = $stmt_img->get_result()->fetch_assoc();
    if($row && !empty($row['image'])) {
        unlink("../images/" . $row['image']); // Xóa file ảnh trong thư mục images
    }

    // Xóa trong Database
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        header("Location: products.php?status=deleted");
    }
}
?>