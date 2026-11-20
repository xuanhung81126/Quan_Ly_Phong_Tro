<?php
require_once '../config/db.php';

if (isset($_GET['id']) && intval($_GET['id']) > 0) {
    $danh_muc_id = intval($_GET['id']);

    try {
        // 1. Tìm lịch sử mua gần nhất của món đồ này
        $stmt = $pdo->prepare("SELECT * FROM lich_su_mua WHERE danh_muc_id = ? ORDER BY thoi_gian DESC, id DESC LIMIT 1");
        $stmt->execute([$danh_muc_id]);
        $last_history = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($last_history) {
            $nguoi_phai_tra_lai_luot = $last_history['thanh_vien_id'];
            $history_id = $last_history['id'];

            // 2. Cập nhật lại lượt hiện tại của món đồ về lại người vừa thực hiện trước đó
            $stmt_update = $pdo->prepare("UPDATE danh_muc_do SET nguoi_hien_tai_id = ? WHERE id = ?");
            $stmt_update->execute([$nguoi_phai_tra_lai_luot, $danh_muc_id]);

            // 3. Xóa dòng lịch sử bấm nhầm đó đi
            $stmt_del = $pdo->prepare("DELETE FROM lich_su_mua WHERE id = ?");
            $stmt_del->execute([$history_id]);
        }
    } catch (Exception $e) {
        // Lỗi nếu có
    }
}

header('Location: ../index.php');
exit;
?>