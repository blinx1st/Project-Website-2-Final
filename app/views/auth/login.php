<section class="login-panel">
    <div class="login-brand">
        <img src="<?= asset_url('Image/LogoVNU.jpg') ?>" alt="Câu lạc bộ tin học VNUIS">
        <span class="login-badge">VNUIS</span>
        <h1>Chào mừng trở lại</h1>
        <p>Đăng nhập để quản lý sự kiện, điểm danh, điểm rèn luyện và chứng nhận tham gia câu lạc bộ.</p>
    </div>

    <div class="login-form-card">
        <h2>Đăng nhập</h2>
        <p class="login-subtitle">Sử dụng email và mật khẩu tài khoản của bạn.</p>

        <form method="post" action="<?= url_for('Login_64131060', 'Login_64131060') ?>">
            <?php if (!empty($data['error'])): ?><div class="alert alert-danger"><?= h($data['error']) ?></div><?php endif; ?>
            <div class="login-field">
                <label class="form-label" for="email">Email</label>
                <input class="form-control" id="email" type="email" name="email" placeholder="name@example.com" required>
            </div>
            <div class="login-field">
                <label class="form-label" for="matKhau">Mật khẩu</label>
                <input class="form-control" id="matKhau" type="password" name="matKhau" placeholder="Nhập mật khẩu" required>
            </div>
            <div class="login-actions">
                <button class="btn-main" type="submit">ĐĂNG NHẬP</button>
                <a class="btn-back" href="<?= url_for('ThanhVien_Member_64131060', 'Create') ?>">ĐĂNG KÝ TÀI KHOẢN</a>
            </div>
        </form>
    </div>
</section>