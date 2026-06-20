<section class="certificate-page">
    <div class="toolbar no-print">
        <button class="btn-main" type="button" onclick="window.print()">IN / TẢI PDF</button>
        <a class="btn-back" href="javascript:history.back()">QUAY VỀ</a>
    </div>
    <article class="certificate-card">
        <div class="certificate-kicker">VNUIS</div>
        <h1>CHỨNG NHẬN THAM GIA</h1>
        <p class="certificate-line">Chứng nhận sinh viên</p>
        <h2><?= h($data['cert']['HoTen'] ?? $data['cert']['MaThanhVien'] ?? '') ?></h2>
        <p>đã tham gia sự kiện</p>
        <h3><?= h($data['cert']['TenSuKien'] ?? $data['cert']['MaSuKien'] ?? '') ?></h3>
        <p><?= h($data['cert']['NoiDung'] ?? '') ?></p>
        <div class="certificate-meta">
            <span>Mã chứng nhận: <?= h($data['cert']['MaChungNhan'] ?? '') ?></span>
            <span>Ngày cấp: <?= h($data['cert']['NgayCap'] ?? '') ?></span>
        </div>
        <div class="certificate-sign">
            <strong>Đơn vị cấp</strong>
            <span>CLB Tin học VNUIS</span>
        </div>
    </article>
</section>