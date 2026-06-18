<?php $isPostList = (($data['cfg']['table'] ?? '') === 'BaiDang'); ?>
<section class="panel <?= $isPostList ? 'post-panel' : '' ?>">
    <?php if (!$isPostList): ?>
        <h1 class="page-title"><?= h($data['title']) ?></h1>
    <?php endif; ?>

    <?php if (!empty($data['search'])): ?>
        <form class="search-form" method="get" action="<?= url_for($data['controller'], $data['listAction']) ?>">
            <?php if ($data['search'] === 'members'): ?>
                <input class="form-control" type="text" name="mathanhvien" placeholder="Tìm kiếm mã thành viên..." value="<?= h($data['searchValues']['mathanhvien'] ?? '') ?>">
                <input class="form-control" type="text" name="hoten" placeholder="Tìm kiếm thành viên..." value="<?= h($data['searchValues']['hoten'] ?? '') ?>">
            <?php else: ?>
                <input class="form-control" type="text" name="maSuKien" placeholder="Tìm kiếm mã sự kiện..." value="<?= h($data['searchValues']['maSuKien'] ?? '') ?>">
                <input class="form-control" type="text" name="tenSuKien" placeholder="Tìm kiếm tên sự kiện..." value="<?= h($data['searchValues']['tenSuKien'] ?? '') ?>">
                <select class="form-control" name="maCLB">
                    <option value="">Tất cả CLB</option>
                    <?php foreach (($data['filterOptions']['clbs'] ?? []) as $option): ?>
                        <option value="<?= h($option['value']) ?>" <?= (string)($data['searchValues']['maCLB'] ?? '') === (string)$option['value'] ? 'selected' : '' ?>><?= h($option['label']) ?></option>
                    <?php endforeach; ?>
                </select>
                <select class="form-control" name="maLoaiSuKien">
                    <option value="">Tất cả loại</option>
                    <?php foreach (($data['filterOptions']['types'] ?? []) as $option): ?>
                        <option value="<?= h($option['value']) ?>" <?= (string)($data['searchValues']['maLoaiSuKien'] ?? '') === (string)$option['value'] ? 'selected' : '' ?>><?= h($option['label']) ?></option>
                    <?php endforeach; ?>
                </select>
                <select class="form-control" name="hocKy">
                    <option value="">Tất cả học kỳ</option>
                    <?php foreach (['HK1', 'HK2', 'HK3'] as $hk): ?>
                        <option value="<?= h($hk) ?>" <?= (string)($data['searchValues']['hocKy'] ?? '') === $hk ? 'selected' : '' ?>><?= h($hk) ?></option>
                    <?php endforeach; ?>
                </select>
                <input class="form-control" type="text" name="namHoc" placeholder="Năm học 2024-2025" value="<?= h($data['searchValues']['namHoc'] ?? '') ?>">
            <?php endif; ?>
            <button class="btn-main" type="submit">TÌM KIẾM</button>
        </form>
    <?php endif; ?>

    <?php if (($data['controller'] ?? '') === 'DiemRenLuyen_Admin_64131060'): ?>
        <form class="search-form" method="get" action="<?= url_for('DiemRenLuyen_Admin_64131060', 'ExportCsv') ?>">
            <select class="form-control" name="HocKy" required>
                <option value="HK1">HK1</option>
                <option value="HK2">HK2</option>
                <option value="HK3">HK3</option>
            </select>
            <input class="form-control" type="text" name="NamHoc" value="<?= h(date('Y') . '-' . ((int)date('Y') + 1)) ?>" pattern="\d{4}-\d{4}" required>
            <input class="form-control" type="text" name="MaCLB" placeholder="Mã CLB (nếu cần)">
            <button class="btn-main" type="submit">EXPORT CUỐI KỲ</button>
        </form>
    <?php endif; ?>

    <?php if (!$isPostList || !empty($data['canWrite'])): ?>
    <div class="toolbar">
        <?php if (!empty($data['canWrite'])): ?>
            <a class="btn-main" href="<?= url_for($data['controller'], 'Create') ?>">THÊM MỚI</a>
        <?php endif; ?>
        <?php if (!$isPostList && current_role() === 'TVCN'): ?>
            <a class="btn-back" href="<?= url_for('CLB_Admin_64131060', 'CLB_Admin_64131060') ?>">CLB</a>
            <a class="btn-back" href="<?= url_for('ThanhVienCLB_Admin_64131060', 'ThanhVienCLB_Admin_64131060') ?>">THÀNH VIÊN CLB</a>
            <a class="btn-back" href="<?= url_for('BaoCao_Admin_64131060', 'ThongKe') ?>">BÁO CÁO</a>
        <?php elseif (!$isPostList && current_role() === 'TVTG'): ?>
            <a class="btn-back" href="<?= url_for('CLB_Assitant_64131060', 'CLB_Assitant_64131060') ?>">CLB</a>
            <a class="btn-back" href="<?= url_for('ThanhVienCLB_Assitant_64131060', 'ThanhVienCLB_Assitant_64131060') ?>">THÀNH VIÊN CLB</a>
        <?php endif; ?>
        <?php if (!$isPostList && ($data['controller'] ?? '') === 'DiemRenLuyen_Admin_64131060'): ?>
            <a class="btn-back" href="<?= url_for('LoaiSuKien_Admin_64131060', 'LoaiSuKien_Admin_64131060') ?>">LOẠI SỰ KIỆN</a>
            <a class="btn-back" href="<?= url_for('QuyTacDiemRenLuyen_Admin_64131060', 'QuyTacDiemRenLuyen_Admin_64131060') ?>">QUY TẮC ĐIỂM</a>
            <a class="btn-back" href="<?= url_for('TongDiemRenLuyen_Admin_64131060', 'TongDiemRenLuyen_Admin_64131060') ?>">TỔNG ĐIỂM</a>
        <?php endif; ?>
        <?php if (!$isPostList && str_contains($data['controller'], 'ThanhVien_Admin')): ?>
            <a class="btn-back" href="<?= url_for('NhomHocTap_Admin_64131060', 'NhomHocTap_Admin_64131060') ?>">NHÓM HỌC TẬP</a>
        <?php elseif (!$isPostList && str_contains($data['controller'], 'ThanhVien_Assitant')): ?>
            <a class="btn-back" href="<?= url_for('NhomHocTap_Assitant_64131060', 'NhomHocTap_Assitant_64131060') ?>">NHÓM HỌC TẬP</a>
        <?php elseif (!$isPostList && str_contains($data['controller'], 'SuKien_Admin')): ?>
            <a class="btn-back" href="<?= url_for('ThanhVienSuKien_Admin_64131060', 'ThanhVienSuKien_Admin_64131060') ?>">THÀNH VIÊN THAM GIA</a>
            <a class="btn-back" href="<?= url_for('CheckinSuKien_Admin_64131060', 'CheckinSuKien_Admin_64131060') ?>">LOG CHECK-IN</a>
        <?php elseif (!$isPostList && str_contains($data['controller'], 'SuKien_Assitant')): ?>
            <a class="btn-back" href="<?= url_for('ThanhVienSuKien_Assitant_64131060', 'ThanhVienSuKien_Assitant_64131060') ?>">THÀNH VIÊN THAM GIA</a>
            <a class="btn-back" href="<?= url_for('CheckinSuKien_Assitant_64131060', 'CheckinSuKien_Assitant_64131060') ?>">LOG CHECK-IN</a>
        <?php elseif (!$isPostList && str_contains($data['controller'], 'ThanhVienSuKien_Member')): ?>
            <a class="btn-back" href="<?= url_for('CheckinSuKien_Member_64131060', 'CheckinSuKien_Member_64131060') ?>">LỊCH SỬ CHECK-IN</a>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <?php if (!empty($data['emptyMessage'])): ?><div class="alert alert-warning"><?= h($data['emptyMessage']) ?></div><?php endif; ?>
    <div id="api-message" class="alert" style="display:none;"></div>

    <?php if ($isPostList): ?>
        <div class="post-feed">
            <?php foreach ($data['rows'] as $row): ?>
                <?php
                $author = $row['TacGiaTen'] ?? $row['TacGia'] ?? 'CLB Tin Học';
                $avatarText = function_exists('mb_substr') ? mb_substr(trim((string)$author), 0, 1, 'UTF-8') : substr(trim((string)$author), 0, 1);
                $createdAt = $row['NgayTao'] ?? '';
                $createdText = $createdAt && strtotime((string)$createdAt) ? date('d/m/Y h:i:s A', strtotime((string)$createdAt)) : $createdAt;
                $params = [];
                foreach ($data['cfg']['pk'] as $pk) {
                    $params[$pk] = $row[$pk];
                }
                ?>
                <article class="post-card">
                    <header class="post-header">
                        <span class="post-avatar" aria-hidden="true"><?= h($avatarText ?: 'C') ?></span>
                        <div class="post-meta">
                            <strong><?= h($author) ?></strong>
                            <span><?= h($createdText) ?> · CLB Tin Học</span>
                        </div>
                    </header>
                    <div class="post-body">
                        <h2><?= h($row['TieuDe'] ?? '') ?></h2>
                        <div class="post-text"><?= nl2br(h($row['NoiDung'] ?? '')) ?></div>
                    </div>
                    <?php if (!empty($row['Anh'])): ?>
                        <div class="post-media">
                            <img class="post-image" src="<?= asset_url('Image/' . $row['Anh']) ?>" alt="<?= h($row['TieuDe'] ?? 'Bài đăng') ?>">
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($data['canWrite'])): ?>
                        <div class="post-admin-actions">
                            <a class="btn btn-sm btn-info text-white" href="<?= url_for($data['controller'], 'Details', $params) ?>">Chi tiết</a>
                            <a class="btn btn-sm btn-warning" href="<?= url_for($data['controller'], 'Edit', $params) ?>">Sửa</a>
                            <a class="btn btn-sm btn-danger" href="<?= url_for($data['controller'], 'Delete', $params) ?>">Xóa</a>
                        </div>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
            <thead><tr>
                <?php foreach ($data['cfg']['list'] as $label): ?><th><?= h($label) ?></th><?php endforeach; ?>
                <th>Thao tác</th>
            </tr></thead>
            <tbody>
            <?php foreach ($data['rows'] as $row): ?>
                <tr data-ma-sukien="<?= h($row['MaSuKien'] ?? '') ?>" data-ma-thanhvien="<?= h($row['MaThanhVien'] ?? '') ?>">
                    <?php foreach ($data['cfg']['list'] as $field => $label): ?>
                        <td class="<?= $field === 'TrangThaiThamGia' ? 'js-status-cell' : '' ?>">
                            <?php if ($field === 'Anh' && !empty($row[$field])): ?>
                                <img class="thumb" src="<?= asset_url('Image/' . $row[$field]) ?>" alt="<?= h($row[$field]) ?>">
                            <?php else: ?>
                                <?= nl2br(h($row[$field] ?? '')) ?>
                            <?php endif; ?>
                        </td>
                    <?php endforeach; ?>
                    <td style="white-space:nowrap;">
                        <?php $params = []; foreach ($data['cfg']['pk'] as $pk) { $params[$pk] = $row[$pk]; } ?>
                        <a class="btn btn-sm btn-info text-white" href="<?= url_for($data['controller'], 'Details', $params) ?>">Chi tiết</a>
                        <?php if (!empty($data['canWrite'])): ?>
                            <a class="btn btn-sm btn-warning" href="<?= url_for($data['controller'], 'Edit', $params) ?>">Sửa</a>
                            <a class="btn btn-sm btn-danger" href="<?= url_for($data['controller'], 'Delete', $params) ?>">Xóa</a>
                        <?php endif; ?>
                        <?php if (!empty($data['cfg']['ajaxConfirm']) && current_role() && in_array(current_role(), ['TVCN', 'TVTG'], true) && ($row['TrangThaiThamGia'] ?? '') !== 'Đã tham gia'): ?>
                            <button class="btn btn-sm btn-success js-confirm-attendance" type="button" data-event="<?= h($row['MaSuKien']) ?>" data-member="<?= h($row['MaThanhVien']) ?>">Xác nhận</button>
                        <?php endif; ?>
                        <?php if (($data['cfg']['table'] ?? '') === 'SuKien' && current_role() && in_array(current_role(), ['TVCN', 'TVTG'], true)): ?>
                            <a class="btn btn-sm btn-success" href="<?= url_for($data['controller'], 'QRCode', ['MaSuKien' => $row['MaSuKien']]) ?>">QR</a>
                        <?php endif; ?>
                        <?php if (($data['cfg']['table'] ?? '') === 'SuKien' && current_role() === 'TV'): ?>
                            <button class="btn btn-sm btn-success js-register-event" type="button" data-event="<?= h($row['MaSuKien']) ?>">Đăng ký</button>
                            <button class="btn btn-sm btn-outline-danger js-cancel-registration" type="button" data-event="<?= h($row['MaSuKien']) ?>">Hủy đăng ký</button>
                        <?php endif; ?>
                        <?php if (($data['cfg']['table'] ?? '') === 'ChungNhan'): ?>
                            <a class="btn btn-sm btn-primary" href="<?= url_for($data['controller'], 'In', ['MaChungNhan' => $row['MaChungNhan']]) ?>">In</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</section>
