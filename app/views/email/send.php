<?php // Form gửi mail lấy danh sách email từ ThanhVien; controller đã sắp xếp ưu tiên TVCN, TVTG rồi mới đến TV. ?>
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
                <select class="form-control" id="mailTo" name="To" required>
                    <option value="">-- Chọn email nhận --</option>
                    <?php foreach (($data['recipients'] ?? []) as $recipient): ?>
                        <?php
                        $role = $recipient['MaVaiTro'] ?? '';
                        $roleLabel = $role === 'TVCN' ? 'Chủ nhiệm' : ($role === 'TVTG' ? 'Trợ giảng' : 'Thành viên');
                        $email = (string)($recipient['Email'] ?? '');
                        $label = $roleLabel . ' - ' . ($recipient['HoTen'] ?? '') . ' <' . $email . '>';
                        ?>
                        <option value="<?= h($email) ?>" <?= (string)($data['selectedTo'] ?? '') === $email ? 'selected' : '' ?>><?= h($label) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-field">
                <label for="mailSubject">Tiêu đề</label>
                <input class="form-control" id="mailSubject" type="text" name="Subject" value="<?= h($data['subject'] ?? '') ?>" required>
            </div>
        </div>
        <div class="form-field mail-body-field">
            <label for="mailBody">Nội dung</label>
            <textarea class="form-control" id="mailBody" name="Body" required><?= h($data['body'] ?? '') ?></textarea>
        </div>
        <div class="toolbar mail-actions">
            <button class="btn-main" type="submit">GỬI MAIL</button>
        </div>
    </form>
</section>
