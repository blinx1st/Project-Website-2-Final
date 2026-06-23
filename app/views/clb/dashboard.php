<?php
// Trang CLB hiện đóng vai trò “trung tâm chức năng” cho một câu lạc bộ duy nhất của website.
$club = $data['club'] ?? null;
$clubId = (string)($club['MaCLB'] ?? '');
$stats = $data['stats'] ?? [];
?>
<section class="panel">
    <h1 class="page-title"><?= h($data['title'] ?? 'Câu lạc bộ') ?></h1>

    <?php if ($club): ?>
        <div class="report-grid">
            <div class="report-card"><span>Thành viên</span><strong><?= h($stats['members'] ?? 0) ?></strong></div>
            <div class="report-card"><span>Chủ nhiệm / Trợ giảng</span><strong><?= h($stats['assistants'] ?? 0) ?></strong></div>
            <div class="report-card"><span>Nhóm học tập</span><strong><?= h($stats['groups'] ?? 0) ?></strong></div>
            <div class="report-card"><span>Sự kiện</span><strong><?= h($stats['events'] ?? 0) ?></strong></div>
        </div>

        <div class="panel" style="box-shadow:none;margin-top:18px;">
            <h2 style="margin-top:0;"><?= h($club['TenCLB'] ?? 'CLB') ?></h2>
            <p><strong>Mã CLB:</strong> <?= h($club['MaCLB'] ?? '') ?></p>
            <p><strong>Chủ nhiệm:</strong> <?= h($club['ChuNhiemTen'] ?? $club['ChuNhiem'] ?? '') ?></p>
            <p><strong>Ngày thành lập:</strong> <?= h($club['NgayThanhLap'] ?? '') ?></p>
            <?php if (!empty($club['MoTa'])): ?>
                <p><strong>Mô tả:</strong> <?= h($club['MoTa']) ?></p>
            <?php endif; ?>
        </div>

        <?php // Các nút này thay cho việc quản lý nhiều CLB: đi thẳng tới dữ liệu vận hành của CLB hiện tại. ?>
        <div class="toolbar" style="margin-top:18px;">
            <a class="btn-main" href="<?= url_for('ThanhVien_Admin_64131060', 'TimKiemTV_Admin_64131060') ?>">XEM THÀNH VIÊN</a>
            <a class="btn-main" href="<?= url_for('ThanhVienCLB_Admin_64131060', 'ThanhVienCLB_Admin_64131060') ?>">THÀNH VIÊN CLB</a>
            <a class="btn-main" href="<?= url_for('NhomHocTap_Admin_64131060', 'NhomHocTap_Admin_64131060') ?>">XEM NHÓM HỌC TẬP</a>
            <a class="btn-main" href="<?= url_for('SuKien_Admin_64131060', 'TimKiemSuKien_Admin_64131060') ?>">XEM SỰ KIỆN</a>
            <a class="btn-back" href="<?= url_for('CLB_Admin_64131060', 'Edit', ['MaCLB' => $clubId]) ?>">CẬP NHẬT THÔNG TIN CLB</a>
        </div>
    <?php else: ?>
        <div class="alert alert-warning">Chưa có dữ liệu câu lạc bộ chính trong hệ thống.</div>
    <?php endif; ?>
</section>
