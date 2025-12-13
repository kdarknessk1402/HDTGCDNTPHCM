<?php
/**
 * View: Danh sách Hợp đồng (Admin)
 * File: views/admin/hop_dong/index.php
 */

if (!isLoggedIn()) { redirect('/login'); exit; }
$pageTitle = 'Quản lý Hợp đồng';
require_once __DIR__ . '/../../layouts/header.php';
?>

<div class="container-fluid px-4">
    <h1 class="mt-4"><?= $pageTitle ?></h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item active">Quản lý Hợp đồng</li>
    </ol>

    <?php displayFlashMessage(); ?>

    <div class="card mb-4">
        <div class="card-header"><i class="fas fa-filter me-1"></i> Lọc</div>
        <div class="card-body">
            <form method="GET" action="/admin/hop-dong" class="row g-3">
                <div class="col-md-2">
                    <label class="form-label">Khoa</label>
                    <select name="khoa_id" class="form-select">
                        <option value="">-- Tất cả --</option>
                        <?php foreach ($khoa_list as $k): ?>
                            <option value="<?= $k['khoa_id'] ?>" <?= (isset($_GET['khoa_id']) && $_GET['khoa_id'] == $k['khoa_id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($k['ten_khoa']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Giảng viên</label>
                    <select name="giang_vien_id" class="form-select">
                        <option value="">-- Tất cả --</option>
                        <?php foreach ($giang_vien_list as $gv): ?>
                            <option value="<?= $gv['giang_vien_id'] ?>" <?= (isset($_GET['giang_vien_id']) && $_GET['giang_vien_id'] == $gv['giang_vien_id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($gv['ten_giang_vien']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Trạng thái</label>
                    <select name="trang_thai" class="form-select">
                        <option value="">-- Tất cả --</option>
                        <option value="Mới tạo" <?= (isset($_GET['trang_thai']) && $_GET['trang_thai'] == 'Mới tạo') ? 'selected' : '' ?>>Mới tạo</option>
                        <option value="Đã duyệt" <?= (isset($_GET['trang_thai']) && $_GET['trang_thai'] == 'Đã duyệt') ? 'selected' : '' ?>>Đã duyệt</option>
                        <option value="Đang thực hiện" <?= (isset($_GET['trang_thai']) && $_GET['trang_thai'] == 'Đang thực hiện') ? 'selected' : '' ?>>Đang thực hiện</option>
                        <option value="Hoàn thành" <?= (isset($_GET['trang_thai']) && $_GET['trang_thai'] == 'Hoàn thành') ? 'selected' : '' ?>>Hoàn thành</option>
                        <option value="Hủy" <?= (isset($_GET['trang_thai']) && $_GET['trang_thai'] == 'Hủy') ? 'selected' : '' ?>>Hủy</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Năm</label>
                    <select name="nam_hop_dong" class="form-select">
                        <option value="">-- Tất cả --</option>
                        <?php for($y = date('Y'); $y >= 2020; $y--): ?>
                            <option value="<?= $y ?>" <?= (isset($_GET['nam_hop_dong']) && $_GET['nam_hop_dong'] == $y) ? 'selected' : '' ?>><?= $y ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Lọc</button>
                        <a href="/admin/hop-dong" class="btn btn-secondary"><i class="fas fa-redo"></i> Reset</a>
                        <a href="/admin/hop-dong/create" class="btn btn-success"><i class="fas fa-plus"></i> Tạo HĐ</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header"><i class="fas fa-table me-1"></i> Danh sách (<?= count($hop_dong_list) ?> hợp đồng)</div>
        <div class="card-body">
            <?php if (empty($hop_dong_list)): ?>
                <div class="alert alert-info"><i class="fas fa-info-circle"></i> Chưa có hợp đồng nào.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-sm">
                        <thead class="table-light">
                            <tr>
                                <th width="3%">STT</th>
                                <th width="10%">Số HĐ</th>
                                <th width="8%">Ngày HĐ</th>
                                <th width="15%">Giảng viên</th>
                                <th width="12%">Môn học</th>
                                <th width="10%">Lớp</th>
                                <th width="8%">Cơ sở</th>
                                <th width="6%">Giờ</th>
                                <th width="10%">Tổng tiền</th>
                                <th width="10%">Trạng thái</th>
                                <th width="8%">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($hop_dong_list as $index => $hd): ?>
                                <tr>
                                    <td class="text-center"><?= $index + 1 ?></td>
                                    <td><strong><?= htmlspecialchars($hd['so_hop_dong']) ?></strong></td>
                                    <td><?= date('d/m/Y', strtotime($hd['ngay_hop_dong'])) ?></td>
                                    <td>
                                        <small><?= htmlspecialchars($hd['ten_giang_vien']) ?></small>
                                        <br><small class="text-muted"><?= htmlspecialchars($hd['so_dien_thoai']) ?></small>
                                    </td>
                                    <td><small><?= htmlspecialchars($hd['ten_mon_hoc']) ?></small></td>
                                    <td><small><?= htmlspecialchars($hd['ten_lop']) ?></small></td>
                                    <td><small><?= htmlspecialchars($hd['ten_co_so']) ?></small></td>
                                    <td class="text-center"><?= $hd['tong_gio_mon_hoc'] ?></td>
                                    <td class="text-end">
                                        <strong><?= number_format($hd['tong_tien'], 0, ',', '.') ?></strong>
                                    </td>
                                    <td class="text-center">
                                        <?php
                                        $badge_class = [
                                            'Mới tạo' => 'bg-secondary',
                                            'Đã duyệt' => 'bg-info',
                                            'Đang thực hiện' => 'bg-primary',
                                            'Hoàn thành' => 'bg-success',
                                            'Hủy' => 'bg-danger'
                                        ];
                                        $class = $badge_class[$hd['trang_thai']] ?? 'bg-secondary';
                                        ?>
                                        <span class="badge <?= $class ?>"><?= $hd['trang_thai'] ?></span>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm">
                                            <a href="/admin/hop-dong/edit/<?= $hd['hop_dong_id'] ?>" class="btn btn-warning" title="Sửa">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button class="btn btn-danger btn-delete" data-id="<?= $hd['hop_dong_id'] ?>" 
                                                    data-name="<?= htmlspecialchars($hd['so_hop_dong']) ?>" title="Xóa">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fas fa-exclamation-triangle"></i> Xác nhận xóa</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Xóa hợp đồng: <strong id="deleteName"></strong>?</p>
            </div>
            <div class="modal-footer">
                <form method="POST" id="deleteForm">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-danger"><i class="fas fa-trash"></i> Xóa</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('.btn-delete').forEach(btn => {
    btn.addEventListener('click', function() {
        document.getElementById('deleteName').textContent = this.dataset.name;
        document.getElementById('deleteForm').action = `/admin/hop-dong/delete/${this.dataset.id}`;
        new bootstrap.Modal(document.getElementById('deleteModal')).show();
    });
});
</script>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>