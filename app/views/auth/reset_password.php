<?php // Form đặt lại mật khẩu nhận token ẩn từ URL email; controller xác thực token trước khi render và trước khi ghi DB. ?>
<section class="login-panel">
    <div class="login-brand">
        <img src="<?= asset_url('Image/LogoVNU.jpg') ?>" alt="Câu lạc bộ tin học VNUIS">
        <span class="login-badge">VNUIS</span>
        <h1>Đặt lại mật khẩu</h1>
        <p>Chọn mật khẩu mới cho tài khoản của bạn. Link khôi phục chỉ có hiệu lực trong thời gian ngắn.</p>
    </div>

    <div class="login-form-card">
        <h2>Mật khẩu mới</h2>
        <p class="login-subtitle">Mật khẩu mới cần có ít nhất 6 ký tự.</p>

        <form method="post" action="<?= url_for('Login_64131060', 'ResetPassword_64131060') ?>">
            <?php if (!empty($data['error'])): ?><div class="alert alert-danger"><?= h($data['error']) ?></div><?php endif; ?>
            <input type="hidden" name="token" value="<?= h($data['token'] ?? '') ?>">
            <div class="login-field">
                <label class="form-label" for="MatKhauMoi">Mật khẩu mới</label>
                <input class="form-control" id="MatKhauMoi" type="password" name="MatKhauMoi" minlength="6" required>
            </div>
            <div class="login-field">
                <label class="form-label" for="NhapLaiMatKhau">Nhập lại mật khẩu mới</label>
                <input class="form-control" id="NhapLaiMatKhau" type="password" name="NhapLaiMatKhau" minlength="6" required>
            </div>
            <div class="login-actions">
                <button class="btn-main" type="submit">ĐẶT LẠI MẬT KHẨU</button>
                <a class="btn-back" href="<?= url_for('Login_64131060', 'Login_64131060') ?>">QUAY VỀ</a>
            </div>
        </form>
    </div>
</section>
