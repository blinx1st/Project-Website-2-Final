<?php // View chi tiết tái sử dụng cfg fields cho mọi resource thay vì viết một bảng riêng. ?>
<section class="panel">
    <h1 class="page-title"><?= h($data['title']) ?></h1>
    <table class="table table-bordered">
        <?php // Vòng lặp này biến metadata resource thành từng dòng nhãn - giá trị. ?>
        <?php foreach ($data['cfg']['fields'] as $field => $meta): ?>
            <tr>
                <th style="width:240px;"><?= h($meta['label'] ?? $field) ?></th>
                <td>
                    <?php if (($meta['type'] ?? '') === 'image' && !empty($data['row'][$field])): ?>
                        <img class="thumb" src="<?= asset_url('Image/' . $data['row'][$field]) ?>" alt="<?= h($data['row'][$field]) ?>">
                    <?php else: ?>
                        <?= nl2br(h($data['row'][$field] ?? '')) ?>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
    <div class="toolbar">
        <?php // canWrite chỉ điều khiển nút; controller vẫn phải kiểm tra quyền khi mở URL Edit. ?>
        <?php if (!empty($data['canWrite'])): ?><a class="btn-main" href="<?= url_for($data['controller'], 'Edit', $data['keys']) ?>">CẬP NHẬT</a><?php endif; ?>
        <a class="btn-back" href="<?= url_for($data['controller'], $data['listAction']) ?>">QUAY VỀ</a>
    </div>
</section>
