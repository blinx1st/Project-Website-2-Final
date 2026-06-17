<?php
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$absoluteScanUrl = $scheme . '://' . $host . $data['scanUrl'];
$qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=280x280&data=' . rawurlencode($absoluteScanUrl);
?>
<section class="panel qr-panel">
    <h1 class="page-title"><?= h($data['title']) ?></h1>
    <div class="qr-layout">
        <div>
            <h2><?= h($data['event']['TenSuKien'] ?? '') ?></h2>
            <p><strong>Mã sự kiện:</strong> <?= h($data['event']['MaSuKien'] ?? '') ?></p>
            <p><strong>Thời gian:</strong> <?= h($data['event']['NgayBatDau'] ?? '') ?> - <?= h($data['event']['NgayKetThuc'] ?? '') ?></p>
            <p><strong>QR hiệu lực:</strong> <?= h($data['event']['CheckinMoLuc'] ?? '') ?> - <?= h($data['event']['CheckinDongLuc'] ?? '') ?></p>
            <p>Sinh viên đã đăng ký dùng camera điện thoại để quét mã QR này, đăng nhập và check-in sự kiện.</p>
            <div class="toolbar">
                <a class="btn-back" href="<?= h($data['backUrl']) ?>">QUAY VỀ</a>
                <a class="btn-main" href="<?= h($absoluteScanUrl) ?>" target="_blank" rel="noopener">MỞ LINK CHECK-IN</a>
            </div>
        </div>
        <div class="qr-box">
            <img src="<?= h($qrUrl) ?>" alt="QR check-in sự kiện">
            <code><?= h($absoluteScanUrl) ?></code>
        </div>
    </div>
</section>
