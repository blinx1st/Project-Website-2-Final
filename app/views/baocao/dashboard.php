<section class="panel report-panel">
    <h1 class="page-title"><?= h($data['title']) ?></h1>
    <form class="search-form" method="get" action="<?= url_for('BaoCao_Admin_64131060', 'ThongKe') ?>">
        <select class="form-control" name="HocKy">
            <option value="">Tất cả học kỳ</option>
            <?php foreach (['HK1', 'HK2', 'HK3'] as $hk): ?>
                <option value="<?= h($hk) ?>" <?= ($data['filters']['HocKy'] ?? '') === $hk ? 'selected' : '' ?>><?= h($hk) ?></option>
            <?php endforeach; ?>
        </select>
        <input class="form-control" type="text" name="NamHoc" placeholder="2024-2025" value="<?= h($data['filters']['NamHoc'] ?? '') ?>">
        <select class="form-control" name="MaCLB">
            <option value="">Tất cả CLB</option>
            <?php foreach ($data['clbs'] as $clb): ?>
                <option value="<?= h($clb['value']) ?>" <?= ($data['filters']['MaCLB'] ?? '') === $clb['value'] ? 'selected' : '' ?>><?= h($clb['label']) ?></option>
            <?php endforeach; ?>
        </select>
        <button class="btn-main" type="submit">LỌC THỐNG KÊ</button>
    </form>

    <div class="report-grid">
        <div class="report-card"><span>Tổng sự kiện</span><strong><?= h($data['stats']['summary']['SoSuKien'] ?? 0) ?></strong></div>
        <div class="report-card"><span>Lượt đăng ký</span><strong><?= h($data['stats']['summary']['SoDangKy'] ?? 0) ?></strong></div>
        <div class="report-card"><span>Lượt check-in</span><strong><?= h($data['stats']['summary']['SoCheckin'] ?? 0) ?></strong></div>
        <div class="report-card"><span>Tổng điểm</span><strong><?= h($data['stats']['summary']['TongDiem'] ?? 0) ?></strong></div>
    </div>

    <h2>Thống kê theo CLB</h2>
    <table class="table table-bordered table-striped">
        <thead><tr><th>CLB</th><th>Số sự kiện</th><th>Lượt check-in</th><th>Tổng điểm</th></tr></thead>
        <tbody>
        <?php foreach ($data['stats']['byClub'] as $row): ?>
            <tr><td><?= h($row['TenCLB']) ?></td><td><?= h($row['SoSuKien']) ?></td><td><?= h($row['SoCheckin']) ?></td><td><?= h($row['TongDiem']) ?></td></tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <h2>Top sinh viên tích cực</h2>
    <table class="table table-bordered table-striped">
        <thead><tr><th>Mã sinh viên</th><th>Họ tên</th><th>Lượt tham gia</th><th>Tổng điểm</th></tr></thead>
        <tbody>
        <?php foreach ($data['stats']['topStudents'] as $row): ?>
            <tr><td><?= h($row['MaThanhVien']) ?></td><td><?= h($row['HoTen']) ?></td><td><?= h($row['SoLuotThamGia']) ?></td><td><?= h($row['TongDiem']) ?></td></tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</section>
