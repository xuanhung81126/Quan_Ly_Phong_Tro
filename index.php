<?php
require_once 'config/db.php';

$thu_trong_tuan = [
    'Mon' => 'Thứ Hai',
    'Tue' => 'Thứ Ba',
    'Wed' => 'Thứ Tư',
    'Thu' => 'Thứ Năm',
    'Fri' => 'Thứ Sáu',
    'Sat' => 'Thứ Bảy',
    'Sun' => 'Chủ Nhật'
];

$stmt = $pdo->query("SELECT * FROM thanh_vien");
$thanh_vien = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $thanh_vien[$row['id']] = $row;
}

$sql_do = "SELECT d.*, t.ten AS ten_nguoi_hien_tai, t.avatar_color 
           FROM danh_muc_do d 
           JOIN thanh_vien t ON d.nguoi_hien_tai_id = t.id
           ORDER BY d.id ASC";
$danh_sach_do = $pdo->query($sql_do)->fetchAll(PDO::FETCH_ASSOC);

$sql_no = "SELECT n.*, 
                  n1.ten AS ten_nguoi_no, 
                  n2.ten AS ten_nguoi_cho_vay 
           FROM so_no n
           JOIN thanh_vien n1 ON n.nguoi_no_id = n1.id
           JOIN thanh_vien n2 ON n.nguoi_cho_vay_id = n2.id
           ORDER BY n.ngay_cap_nhat DESC";
$danh_sach_no = $pdo->query($sql_no)->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Phòng Trọ - Trường, Hiệp, Hùng</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-50 text-slate-800 font-sans pb-12">

    <header class="bg-indigo-600 text-white shadow-md py-6 mb-8">
        <div class="max-w-6xl mx-auto px-4 flex flex-col md:flex-row justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold tracking-wide"><i class="fa-solid fa-house-chimney mr-2"></i>Bố Và 2 Thằng Chó</h1>
            </div>
            <div class="mt-4 md:mt-0 flex gap-2">
                <?php 
                    $eng_thu = date('D');
                    $thu_vn = isset($thu_trong_tuan[$eng_thu]) ? $thu_trong_tuan[$eng_thu] : '';
                ?>
                <span class="bg-indigo-700 px-3 py-1.5 rounded-lg text-sm font-medium">
                    <i class="fa-regular fa-clock mr-1"></i> <?= $thu_vn ?>, <?= date('d/m/Y H:i') ?> (Hà Nội)
                </span>
            </div>
        </div>
    </header>

    <main class="max-w-6xl mx-auto px-4 space-y-10">

        <!-- PHẦN 1: ĐỒ DÙNG & VIỆC VẶT -->
        <section>
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                <h2 class="text-xl font-bold text-slate-700 flex items-center gap-2">
                    <i class=""></i> Đến Thằng Chó Nào !
                </h2>
                <button onclick="document.getElementById('modalThemMon').classList.remove('hidden')" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition shadow-sm flex items-center gap-2">
                    <i class="fa-solid fa-plus"></i> Thêm 
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                <?php foreach ($danh_sach_do as $item): 
                    $stmt_h = $pdo->prepare("SELECT h.*, t.ten AS ten_nguoi_mua FROM lich_su_mua h JOIN thanh_vien t ON h.thanh_vien_id = t.id WHERE h.danh_muc_id = ? ORDER BY h.thoi_gian DESC, h.id DESC LIMIT 1");
                    $stmt_h->execute([$item['id']]);
                    $last_act = $stmt_h->fetch(PDO::FETCH_ASSOC);
                ?>
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 flex flex-col justify-between hover:shadow-md transition">
                        <div>
                            <div class="flex justify-between items-start mb-3">
                                <h3 class="font-bold text-lg text-slate-800"><?= htmlspecialchars($item['ten_mon']) ?></h3>
                                <?php if ($last_act): ?>
                                    <a href="actions/hoan_tac.php?id=<?= $item['id'] ?>" onclick="return confirm('Bạn có chắc muốn hoàn tác lượt của <?= htmlspecialchars($last_act['ten_nguoi_mua']) ?> không?');" class="text-xs bg-rose-50 hover:bg-rose-100 text-rose-600 px-2 py-1 rounded font-medium transition flex items-center gap-1" title="Hoàn tác nếu bấm nhầm">
                                        <i class="fa-solid fa-rotate-left"></i> Hoàn tác
                                    </a>
                                <?php endif; ?>
                            </div>
                            
                            <div class="my-3">
                                <p class="text-xs text-slate-400 uppercase tracking-wider mb-1 font-semibold">Đang đến lượt:</p>
                                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-white font-medium text-sm shadow-sm" style="background-color: <?= $item['avatar_color'] ?>">
                                    <i class="fa-solid fa-user-tag"></i>
                                    <span><?= htmlspecialchars($item['ten_nguoi_hien_tai']) ?></span>
                                </div>
                            </div>

                            <div class="text-xs text-slate-500 bg-slate-50 p-2.5 rounded-lg border border-slate-100 mb-4">
                                <?php if ($last_act): 
                                    $time_update = strtotime($last_act['thoi_gian']);
                                    $eng_day = date('D', $time_update);
                                    $viet_thu = isset($thu_trong_tuan[$eng_day]) ? $thu_trong_tuan[$eng_day] : '';
                                ?>
                                    <span class="font-semibold text-slate-600"><i class="fa-solid fa-circle-check text-emerald-500 mr-1"></i> Gần nhất: <strong class="text-slate-700"><?= htmlspecialchars($last_act['ten_nguoi_mua']) ?></strong> đã mua</span><br>
                                    <span class="text-indigo-600 font-medium"><?= $viet_thu ?>, <?= date('d/m/Y H:i', $time_update) ?></span>
                                <?php else: ?>
                                    <span class="text-slate-400">Chưa có lịch sử mua</span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="pt-2 border-t border-slate-100">
                            <form action="actions/xac_nhan_mua.php" method="POST">
                                <input type="hidden" name="danh_muc_id" value="<?= $item['id'] ?>">
                                <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-medium text-sm py-2.5 rounded-lg transition shadow-sm flex items-center justify-center gap-1.5">
                                    <i class="fa-solid fa-check-to-slot"></i> Bố Mua rồi
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- PHẦN 2: QUẢN LÝ SỔ NỢ -->
        <section class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                <h2 class="text-xl font-bold text-slate-700 flex items-center gap-2">
                    <i class="fa-solid fa-wallet text-indigo-600"></i> Sổ Ghi Nợ
                </h2>
                <!-- Nút mở form thêm khoản nợ -->
                <button onclick="document.getElementById('modalThemNo').classList.remove('hidden')" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition shadow-sm flex items-center gap-2">
                    <i class="fa-solid fa-file-invoice-dollar"></i> Thêm khoản nợ mới
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200 text-slate-400 text-xs uppercase tracking-wider">
                            <th class="py-3 px-4">Người nợ</th>
                            <th class="py-3 px-4">Người cho vay</th>
                            <th class="py-3 px-4">Nợ tiền gì</th>
                            <th class="py-3 px-4">Số tiền gốc</th>
                            <th class="py-3 px-4">Đã trả</th>
                            <th class="py-3 px-4">Còn lại</th>
                            <th class="py-3 px-4">Trạng thái</th>
                            <th class="py-3 px-4 text-right">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        <?php if (empty($danh_sach_no)): ?>
                            <tr>
                                <td colspan="8" class="text-center py-6 text-slate-400">Chưa có khoản nợ nào được ghi nhận.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($danh_sach_no as $no): 
                                $con_lai = $no['so_tien_goc'] - $no['da_tra'];
                            ?>
                                <tr class="hover:bg-slate-50">
                                    <td class="py-3 px-4 font-semibold text-rose-600"><?= htmlspecialchars($no['ten_nguoi_no']) ?></td>
                                    <td class="py-3 px-4 font-semibold text-emerald-600"><?= htmlspecialchars($no['ten_nguoi_cho_vay']) ?></td>
                                    <td class="py-3 px-4"><?= htmlspecialchars($no['ly_do']) ?></td>
                                    <td class="py-3 px-4 font-medium"><?= number_format($no['so_tien_goc']) ?> đ</td>
                                    <td class="py-3 px-4 text-emerald-600 font-medium"><?= number_format($no['da_tra']) ?> đ</td>
                                    <td class="py-3 px-4 font-bold text-indigo-600"><?= number_format($con_lai) ?> đ</td>
                                    <td class="py-3 px-4">
                                        <?php if ($con_lai <= 0): ?>
                                            <span class="bg-emerald-100 text-emerald-700 text-xs px-2.5 py-1 rounded-full font-medium">Đã trả hết</span>
                                        <?php else: ?>
                                            <span class="bg-amber-100 text-amber-700 text-xs px-2.5 py-1 rounded-full font-medium">Chưa trả hết</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-3 px-4 text-right">
                                        <?php if ($con_lai > 0): ?>
                                            <button onclick="moModalTraNo(<?= $no['id'] ?>, '<?= htmlspecialchars($no['ly_do']) ?>', <?= $con_lai ?>)" class="bg-indigo-50 hover:bg-indigo-100 text-indigo-600 text-xs font-medium px-3 py-1.5 rounded-lg transition">
                                                Trả tiền
                                            </button>
                                        <?php else: ?>
                                            <span class="text-xs text-slate-400">Hoàn tất</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

    </main>

    <!-- MODAL THÊM MÓN ĐỒ MỚI -->
    <div id="modalThemMon" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm hidden flex items-center justify-center p-4 z-50">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-slate-800"><i class="fa-solid fa-circle-plus text-indigo-600"></i> Thêm món đồ / việc vặt mới</h3>
                <button onclick="document.getElementById('modalThemMon').classList.add('hidden')" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>
            <form action="actions/them_mon.php" method="POST" class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-500 mb-1">Tên món đồ / công việc</label>
                    <input type="text" name="ten_mon" required placeholder="Ví dụ: Giấy vệ sinh, Bất kỳ thứ gì..." class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-500 mb-1">Thứ tự xoay vòng bắt đầu từ ai?</label>
                    <select name="nguoi_bat_dau_id" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500">
                        <?php foreach ($thanh_vien as $tv): ?>
                            <option value="<?= $tv['id'] ?>"><?= htmlspecialchars($tv['ten']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="document.getElementById('modalThemMon').classList.add('hidden')" class="w-1/2 bg-slate-100 hover:bg-slate-200 text-slate-600 font-medium py-2 rounded-lg text-sm transition">Huỷ</button>
                    <button type="submit" class="w-1/2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 rounded-lg text-sm transition">Thêm mới</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL THÊM KHOẢN NỢ -->
    <div id="modalThemNo" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm hidden flex items-center justify-center p-4 z-50">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-slate-800"><i class="fa-solid fa-file-invoice-dollar text-indigo-600"></i> Ghi sổ nợ mới</h3>
                <button onclick="document.getElementById('modalThemNo').classList.add('hidden')" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>
            <form action="actions/them_no.php" method="POST" class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-500 mb-1">Ai là người nợ?</label>
                    <select name="nguoi_no_id" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500">
                        <?php foreach ($thanh_vien as $tv): ?>
                            <option value="<?= $tv['id'] ?>"><?= htmlspecialchars($tv['ten']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-500 mb-1">Ai là người cho vay (trả tiền trước)?</label>
                    <select name="nguoi_cho_vay_id" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500">
                        <?php foreach ($thanh_vien as $tv): ?>
                            <option value="<?= $tv['id'] ?>"><?= htmlspecialchars($tv['ten']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-500 mb-1">Số tiền (VNĐ)</label>
                    <input type="number" name="so_tien_goc" required placeholder="Ví dụ: 100000" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-500 mb-1">Nợ tiền gì / Lý do</label>
                    <input type="text" name="ly_do" required placeholder="Ví dụ: Tiền điện, Tiền ăn lẩu..." class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500">
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="document.getElementById('modalThemNo').classList.add('hidden')" class="w-1/2 bg-slate-100 hover:bg-slate-200 text-slate-600 font-medium py-2 rounded-lg text-sm transition">Huỷ</button>
                    <button type="submit" class="w-1/2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 rounded-lg text-sm transition">Lưu khoản nợ</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL TRẢ NỢ -->
    <div id="modalTraNo" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm hidden flex items-center justify-center p-4 z-50">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-slate-800"><i class="fa-solid fa-hand-holding-dollar text-indigo-600"></i> Xác nhận trả nợ</h3>
                <button onclick="document.getElementById('modalTraNo').classList.add('hidden')" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>
            <form action="actions/tra_no.php" method="POST" class="space-y-4">
                <input type="hidden" name="so_no_id" id="tra_so_no_id">
                <div>
                    <p class="text-sm text-slate-600 mb-2">Khoản nợ: <strong id="tra_ly_do" class="text-slate-800"></strong></p>
                    <p class="text-sm text-slate-600 mb-4">Số tiền còn thiếu: <strong id="tra_con_lai" class="text-indigo-600"></strong></p>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-500 mb-1">Số tiền trả thêm (VNĐ)</label>
                    <input type="number" name="so_tien_tra" id="input_so_tien_tra" required class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500">
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="document.getElementById('modalTraNo').classList.add('hidden')" class="w-1/2 bg-slate-100 hover:bg-slate-200 text-slate-600 font-medium py-2 rounded-lg text-sm transition">Huỷ</button>
                    <button type="submit" class="w-1/2 bg-emerald-600 hover:bg-emerald-700 text-white font-medium py-2 rounded-lg text-sm transition">Xác nhận trả</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function moModalTraNo(id, lyDo, conLai) {
            document.getElementById('tra_so_no_id').value = id;
            document.getElementById('tra_ly_do').innerText = lyDo;
            document.getElementById('tra_con_lai').innerText = new Intl.NumberFormat('vi-VN').format(conLai) + ' đ';
            document.getElementById('input_so_tien_tra').value = conLai;
            document.getElementById('modalTraNo').classList.remove('hidden');
        }
    </script>

</body>
</html>