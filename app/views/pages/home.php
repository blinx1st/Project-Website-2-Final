<section class="panel">
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:24px;align-items:center;">
        <div>
            <h1 class="page-title"><?= h($data['title'] ?? 'CLB Tin Học VNUIS') ?></h1>
            <p style="font-size:19px;line-height:1.7;">Website hỗ trợ quản lý thành viên, sự kiện, nhóm học tập, bài đăng, điểm danh và gửi email cho Câu lạc bộ Tin học Trường Đại học Quốc Tế - ĐHQGHN</p>
            <div class="toolbar">
                <a class="btn-main" href="<?= h($data['primaryUrl'] ?? url_for('Login_64131060', 'Login_64131060')) ?>"><?= h($data['primaryText'] ?? 'ĐĂNG NHẬP') ?></a>
                <a class="btn-back" href="<?= url_for('TrangChu_64131060', $data['aboutAction'] ?? 'GioiThieu_64131060') ?>">GIỚI THIỆU</a>
            </div>
        </div>
        <img src="<?= asset_url('Image/BannerVNU.png') ?>" alt="CLB Tin Học" style="width:100%;border-radius:8px;object-fit:cover;max-height:360px;">
    </div>
</section>
<section class="panel" style="margin-top:18px;">
    <h2 class="page-title">Chức năng chính</h2>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:14px;">
        <?php foreach (($data['cards'] ?? []) as $card): ?>
            <a class="panel" style="box-shadow:none;display:block;" href="<?= h($card['url']) ?>">
                <h3 style="font-size:20px;color:#063b87;"><?= h($card['title']) ?></h3>
                <p style="color:#333;margin:0;"><?= h($card['desc']) ?></p>
            </a>
        <?php endforeach; ?>
    </div>
</section>