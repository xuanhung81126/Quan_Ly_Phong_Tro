<?php
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $so_no_id = isset($_POST['so_no_id']) ? intval($_POST['so_no_id']) : 0;
    $so_tien_tra_them = isset($_POST['so_tien_tra']) ? floatval($_POST['so_tien_tra']) : 0;

    if ($so_no_id > 0 && $so_tien_tra_them > 0) {
        try {
            // Lấy thông tin khoản nợ hiện tại
            $stmt = $pdo->prepare("SELECT * FROM so_no WHERE id = ?");
            $stmt->execute([$so_no_id]);
            $no = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($no) {
                $da_tra_moi = $no['da_tra'] + $so_tien_tra_them;
                // Không cho phép trả quá số tiền gốc
                if ($da_tra_moi > $no['so_tien_goc']) {
                    $da_tra_moi = $no['so_tien_goc'];
                }

                // Cập nhật số tiền đã trả vào bảng so_no
                $stmt_update = $pdo->prepare("UPDATE so_no SET da_tra = ?, ngay_cap_nhat = NOW() WHERE id = ?");
                $stmt_update->execute([$da_tra_moi, $so_no_id]);

                // Lưu lịch sử trả nợ chi tiết
                $stmt_his = $pdo->prepare("INSERT INTO lich_su_tra_no (so_no_id, so_tien_tra, thoi_gian) VALUES (?, ?, NOW())");
                $stmt_his->execute([$so_no_id, $so_tien_tra_them]);
            }
        } catch (Exception $e) {
            // Lỗi nếu có
        }
    }
}

header('Location: ../index.php');
exit;
?>