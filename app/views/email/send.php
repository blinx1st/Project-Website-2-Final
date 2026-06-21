<section class="panel mail-panel">
    <h1 class="page-title"><?= h($data['title'] ?? 'Gửi email') ?></h1>
    <?php if (!empty($data['error'])): ?><div class="alert alert-danger"><?= h($data['error']) ?></div><?php endif; ?>
    <form class="mail-form" method="post" action="">
        <div class="mail-stack">
            <div class="form-field">
                <label for="mailFrom">Email gửi</label>
                <input class="form-control" id="mailFrom" type="email" value="<?= h($data['mailFrom'] ?? '') ?>" readonly>
            </div>
            <div class="form-field">
                <label for="mailTo">Email nhận</label>
                <input class="form-control" id="mailTo" type="email" name="To" required>
            </div>
            <div class="form-field">
                <label for="mailSubject">Tiêu đề</label>
                <input class="form-control" id="mailSubject" type="text" name="Subject" required>
            </div>
        </div>
        <div class="form-field mail-body-field">
            <label for="mailBody">Nội dung</label>
            <textarea class="form-control" id="mailBody" name="Body" required></textarea>
        </div>
        <div class="toolbar mail-actions">
            <button class="btn-main" type="submit">GỬI MAIL</button>
        </div>
    </form>
</section>
