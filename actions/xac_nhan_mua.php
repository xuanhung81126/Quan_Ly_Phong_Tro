<?php
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {$danh_muc_id = isset($_POST['danh_muc_id']) ? intval($_POST['danh_muc_id']) : 0;

    if ($danh_muc_id > 0) {
        try {
            // 1. Lấy thông tin món đồ và người đang đến lượt
            $stmt =$pdo->prepare("SELECT * FROM danh_muc_do WHERE id = ?");
            $stmt->execute([$danh_muc_id]);
            $item =$stmt->fetch(PDO::FETCH_ASSOC);

            if ($item) {
                $nguoi_thuc_hien_id =$item['nguoi_hien_tai_id'];
                
                // 2. Ghi lại lịch sử 
                $stmt_his =$pdo->prepare("INSERT INTO lich_su_mua (danh_muc_id, thanh_vien_id, so_tien, ghi_chu, thoi_gian) VALUES (?, ?, 0, '', NOW())");
                $stmt_his->execute([$danh_muc_id,$nguoi_thuc_hien_id]);

                // 3. Xử lý nhảy lượt tiếp theo
                $vong_str = $item['thu_tu_vong'];$danh_sach_vong = explode(',', $vong_str);$vi_tri_hien_tai = array_search($nguoi_thuc_hien_id,$danh_sach_vong);

                if ($vi_tri_hien_tai !== false) {
                    $vi_tri_tiep_theo = ($vi_tri_hien_tai + 1) % count($danh_sach_vong);$nguoi_tiep_theo_id = intval($danh_sach_vong[$vi_tri_tiep_theo]);

                    // 4. Cập nhật người tiếp theo vào bảng danh_muc_do
                    $stmt_update =$pdo->prepare("UPDATE danh_muc_do SET nguoi_hien_tai_id = ? WHERE id = ?");
                    $stmt_update->execute([$nguoi_tiep_theo_id,$danh_muc_id]);
                }
            }
        } catch (Exception $e) {
            // Lỗi nếu có
        }
    }
}

header('Location: ../index.php');
exit;
?>