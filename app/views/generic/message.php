<section class="panel" style="text-align:center;">
    <h1 class="page-title"><?= h($data['title'] ?? 'Thông báo') ?></h1>
    <p style="font-size:20px;"><?= h($data['message'] ?? '') ?></p>
    <?php if (!empty($data['buttonUrl'])): ?>
        <a class="btn-main" href="<?= h($data['buttonUrl']) ?>"><?= h($data['buttonText'] ?? 'TIẾP TỤC') ?></a>
    <?php endif; ?>
</section>
