<section class="panel auth-panel">
    <h1 class="page-title"><?= h($data['title']) ?></h1>
    <?php if (!empty($data['error'])): ?><div class="alert alert-danger"><?= h($data['error']) ?></div><?php endif; ?>
    <?php if (!empty($data['message'])): ?><div class="alert alert-success"><?= h($data['message']) ?></div><?php endif; ?>
    <form method="post" data-validate-resource="1">
        <div class="form-grid">
            <div class="form-field">
                <label for="MatKhauCu">Mật khẩu cũ</label>
                <input class="form-control" id="MatKhauCu" name="MatKhauCu" type="password" required>
            </div>
            <div class="form-field">
                <label for="MatKhauMoi">Mật khẩu mới</label>
                <input class="form-control" id="MatKhauMoi" name="MatKhauMoi" type="password" minlength="6" required>
            </div>
            <div class="form-field">
                <label for="NhapLaiMatKhau">Nhập lại mật khẩu mới</label>
                <input class="form-control" id="NhapLaiMatKhau" name="NhapLaiMatKhau" type="password" minlength="6" required>
            </div>
        </div>
        <div class="toolbar" style="margin-top:18px;">
            <button class="btn-main" type="submit">ĐỔI MẬT KHẨU</button>
            <a class="btn-back" href="<?= url_for('TrangChu_64131060', current_role() === 'TVCN' ? 'AdminPage_64131060' : (current_role() === 'TVTG' ? 'AssistantPage_64131060' : 'MemberPage_64131060')) ?>">QUAY VỀ</a>
        </div>
    </form>
</section>
