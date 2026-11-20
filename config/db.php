<?php
$host = 'localhost';
$dbname = 'quan_ly_tro'; // Theo đúng tên thư mục của bạn
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Thiết lập chính xác múi giờ Hà Nội
    date_default_timezone_set('Asia/Ho_Chi_Minh');
} catch (PDOException $e) {
    die("Lỗi kết nối cơ sở dữ liệu: " . $e->getMessage());
}
?>