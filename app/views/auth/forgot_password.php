<?php // Form quên mật khẩu chỉ nhận email; controller quyết định có gửi link khôi phục hay không. ?>
<section class="login-panel">
    <div class="login-brand">
        <img src="<?= asset_url('Image/LogoVNU.jpg') ?>" alt="Câu lạc bộ tin học VNUIS">
        <span class="login-badge">VNUIS</span>
        <h1>Khôi phục mật khẩu</h1>
        <p>Nhập email đã đăng ký để nhận link đặt lại mật khẩu qua hộp thư của bạn.</p>
    </div>

    <div class="login-form-card">
        <h2>Quên mật khẩu</h2>
        <p class="login-subtitle">Nếu email tồn tại trong hệ thống, link khôi phục sẽ được gửi đến email đó.</p>

        <form method="post" action="<?= url_for('Login_64131060', 'ForgotPassword_64131060') ?>">
            <?php if (!empty($data['error'])): ?><div class="alert alert-danger"><?= h($data['error']) ?></div><?php endif; ?>
            <div class="login-field">
                <label class="form-label" for="email">Email</label>
                <input class="form-control" id="email" type="email" name="email" placeholder="name@example.com" value="<?= h($data['email'] ?? '') ?>" required>
            </div>
            <div class="login-actions">
                <button class="btn-main" type="submit">GỬI LINK KHÔI PHỤC</button>
                <a class="btn-back" href="<?= url_for('Login_64131060', 'Login_64131060') ?>">QUAY VỀ</a>
            </div>
        </form>
    </div>
</section>
