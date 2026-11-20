<?php
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nguoi_no_id = isset($_POST['nguoi_no_id']) ? intval($_POST['nguoi_no_id']) : 0;
    $nguoi_cho_vay_id = isset($_POST['nguoi_cho_vay_id']) ? intval($_POST['nguoi_cho_vay_id']) : 0;
    $so_tien_goc = isset($_POST['so_tien_goc']) ? floatval($_POST['so_tien_goc']) : 0;
    $ly_do = isset($_POST['ly_do']) ? trim($_POST['ly_do']) : '';

    if ($nguoi_no_id > 0 && $nguoi_cho_vay_id > 0 && $so_tien_goc > 0) {
        try {
            $stmt = $pdo->prepare("INSERT INTO so_no (nguoi_no_id, nguoi_cho_vay_id, so_tien_goc, da_tra, ly_do, ngay_tao, ngay_cap_nhat) VALUES (?, ?, ?, 0, ?, NOW(), NOW())");
            $stmt->execute([$nguoi_no_id, $nguoi_cho_vay_id, $so_tien_goc, $ly_do]);
        } catch (PDOException $e) {
            // Hiển thị lỗi ra màn hình nếu câu lệnh SQL gặp vấn đề cấu trúc bảng
            die("Lỗi SQL: " . $e->getMessage());
        }
    }
}

header('Location: ../index.php');
exit;
?>