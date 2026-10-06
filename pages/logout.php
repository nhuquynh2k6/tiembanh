<?php
session_start();
session_destroy();
header("Location: ../index.php"); // Quay về trang chủ
exit();
?>