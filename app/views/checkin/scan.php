<section class="panel checkin-panel">
    <h1 class="page-title"><?= h($data['title']) ?></h1>
    <?php if (!empty($data['event'])): ?>
        <h2><?= h($data['event']['TenSuKien'] ?? '') ?></h2>
        <p><strong>Mã sự kiện:</strong> <?= h($data['event']['MaSuKien'] ?? '') ?></p>
    <?php endif; ?>

    <?php if (!empty($data['error'])): ?>
        <div class="alert alert-danger"><?= h($data['error']) ?></div>
    <?php else: ?>
        <div class="alert alert-success">Check-in thành công. Hệ thống đã ghi log, cộng điểm rèn luyện và cấp chứng nhận nếu đủ điều kiện.</div>
        <?php if (!empty($data['result'])): ?>
            <table class="table table-bordered">
                <tr><th>Mã sự kiện</th><td><?= h($data['result']['MaSuKien'] ?? '') ?></td></tr>
                <tr><th>Mã sinh viên</th><td><?= h($data['result']['MaThanhVien'] ?? '') ?></td></tr>
                <tr><th>Điểm cộng</th><td><?= h($data['result']['SoDiem'] ?? '') ?></td></tr>
                <tr><th>Mã chứng nhận</th><td><?= h($data['result']['MaChungNhan'] ?? '') ?></td></tr>
            </table>
        <?php endif; ?>
    <?php endif; ?>

    <div class="toolbar">
        <a class="btn-main" href="<?= url_for('SuKien_Member_64131060', 'TimKiemSuKien_Member_64131060') ?>">XEM SỰ KIỆN</a>
        <a class="btn-back" href="<?= url_for('CheckinSuKien_Member_64131060', 'CheckinSuKien_Member_64131060') ?>">LỊCH SỬ CHECK-IN</a>
    </div>
</section>
