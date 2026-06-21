<section class="panel">
    <h1 class="page-title"><?= h($data['title']) ?></h1>
    <table class="table table-bordered">
        <?php foreach ($data['cfg']['list'] as $field => $label): ?>
            <tr><th style="width:240px;"><?= h($label) ?></th><td><?= h($data['row'][$field] ?? '') ?></td></tr>
        <?php endforeach; ?>
    </table>
    <div class="toolbar">
        <a class="btn-main" href="<?= url_for($data['controller'], $data['editAction'], ['MaThanhVien' => $data['row']['MaThanhVien']]) ?>">CẬP NHẬT</a>
        <a class="btn-back" href="<?= url_for('Login_64131060', 'Logout_64131060') ?>">ĐĂNG XUẤT</a>
    </div>
</section>
