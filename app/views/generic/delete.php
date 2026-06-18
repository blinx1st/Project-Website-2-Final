<section class="panel">
    <h1 class="page-title"><?= h($data['title']) ?></h1>
    <?php if (!empty($data['error'])): ?><div class="alert alert-danger"><?= h($data['error']) ?></div><?php endif; ?>
    <p>Bạn có chắc chắn muốn xóa bản ghi này?</p>
    <table class="table table-bordered">
        <?php foreach ($data['cfg']['fields'] as $field => $meta): ?>
            <tr><th style="width:240px;"><?= h($meta['label'] ?? $field) ?></th><td><?= nl2br(h($data['row'][$field] ?? '')) ?></td></tr>
        <?php endforeach; ?>
    </table>
    <form method="post" action="">
        <?php foreach ($data['keys'] as $pk => $value): ?><input type="hidden" name="<?= h($pk) ?>" value="<?= h($value) ?>"><?php endforeach; ?>
        <div class="toolbar">
            <button class="btn-danger-soft" type="submit">XÓA</button>
            <a class="btn-back" href="<?= url_for($data['controller'], $data['listAction']) ?>">QUAY VỀ</a>
        </div>
    </form>
</section>
