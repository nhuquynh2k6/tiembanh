<?php
// Thông tin kết nối mới mà em đã tạo ở bước trước
$servername = "localhost";
$username = "xpsjojzi_admin"; // Thay bằng User của em
$password = "Mật_khẩu_của_em"; // Thay bằng Mật khẩu của em
$dbname = "xpsjojzi_bakery";  // Thay bằng Tên Database của em

// Tạo kết nối
$conn = new mysqli($servername, $username, $password, $dbname);
$conn->set_charset("utf8mb4");

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}
echo "Kết nối CSDL thành công! <br>";

// Truy vấn thử nghiệm (giả sử bảng của em tên là products)
$sql = "SELECT id, name, price FROM products LIMIT 5";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "Dữ liệu truy vấn từ bảng products:<br>";
    while($row = $result->fetch_assoc()) {
        echo "- ID: " . $row["id"]. " | Tên: " . $row["name"]. " | Giá: " . $row["price"]. "<br>";
    }
} else {
    echo "Bảng chưa có dữ liệu nào.";
}
$conn->close();
?>