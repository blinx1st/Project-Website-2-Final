<?php $isRegistrationForm = (($data['controller'] ?? '') === 'ThanhVien_Member_64131060' && ($data['action'] ?? '') === 'Create'); ?>
<section class="panel <?= $isRegistrationForm ? 'registration-panel' : '' ?>">
    <?php if ($isRegistrationForm): ?>
        <div class="registration-heading">
            <span class="registration-badge">VNUIS</span>
            <h1><?= h($data['title']) ?></h1>
            <p>Tạo tài khoản thành viên để đăng ký sự kiện, xem điểm rèn luyện và chứng nhận tham gia.</p>
        </div>
    <?php else: ?>
        <h1 class="page-title"><?= h($data['title']) ?></h1>
    <?php endif; ?>
    <?php if (!empty($data['error'])): ?><div class="alert alert-danger"><?= h($data['error']) ?></div><?php endif; ?>
    <form method="post" enctype="multipart/form-data" action="" data-validate-resource="1">
        <?php foreach (($data['keys'] ?? []) as $pk => $value): ?><input type="hidden" name="<?= h($pk) ?>" value="<?= h($value) ?>"><?php endforeach; ?>
        <?php if ($isRegistrationForm): ?>
            <div class="registration-table-wrap">
                <table class="registration-table">
                    <tbody>
                        <?php foreach ($data['cfg']['fields'] as $field => $meta): ?>
                            <?php
                            $type = $meta['type'] ?? 'text';
                            $value = $data['row'][$field] ?? '';
                            $required = !empty($meta['required']) ? 'required' : '';
                            $maxLength = isset($meta['max_length']) ? 'maxlength="' . h($meta['max_length']) . '"' : '';
                            $pattern = isset($meta['pattern']) ? 'pattern="' . h(trim($meta['pattern'], '/')) . '"' : '';
                            ?>
                            <tr>
                                <th><label for="<?= h($field) ?>"><?= h($meta['label'] ?? $field) ?></label></th>
                                <td>
                                    <?php if ($type === 'textarea'): ?>
                                        <textarea class="form-control" id="<?= h($field) ?>" name="<?= h($field) ?>" <?= $required ?> <?= $maxLength ?>><?= h($value) ?></textarea>
                                    <?php else: ?>
                                        <input class="form-control" id="<?= h($field) ?>" name="<?= h($field) ?>" type="<?= h($type) ?>" value="<?= h($value) ?>" <?= $required ?> <?= $maxLength ?> <?= $pattern ?>>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="form-grid">
                <?php foreach ($data['cfg']['fields'] as $field => $meta): ?>
                    <?php
                    $type = $meta['type'] ?? 'text';
                    $value = $data['row'][$field] ?? '';
                    $isPk = in_array($field, $data['cfg']['pk'], true);
                    $disabled = (($data['action'] === 'Edit' && $isPk) || ($meta['readonly'] ?? false));
                    $required = !empty($meta['required']) ? 'required' : '';
                    $maxLength = isset($meta['max_length']) ? 'maxlength="' . h($meta['max_length']) . '"' : '';
                    $pattern = isset($meta['pattern']) ? 'pattern="' . h(trim($meta['pattern'], '/')) . '"' : '';
                    $min = isset($meta['min']) ? 'min="' . h($meta['min']) . '"' : '';
                    ?>
                    <div class="form-field">
                        <label for="<?= h($field) ?>"><?= h($meta['label'] ?? $field) ?></label>
                        <?php if ($type === 'textarea'): ?>
                            <textarea class="form-control" id="<?= h($field) ?>" name="<?= h($field) ?>" <?= $required ?> <?= $maxLength ?>><?= h($value) ?></textarea>
                        <?php elseif ($type === 'select'): ?>
                            <select class="form-control" id="<?= h($field) ?>" name="<?= h($field) ?>" <?= $required ?> <?= $disabled ? 'disabled' : '' ?>>
                                <option value="">-- Chọn --</option>
                                <?php foreach (($data['relations'][$field] ?? []) as $option): ?>
                                    <option value="<?= h($option['value']) ?>" <?= (string)$value === (string)$option['value'] ? 'selected' : '' ?>><?= h($option['label']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <?php if ($disabled): ?><input type="hidden" name="<?= h($field) ?>" value="<?= h($value) ?>"><?php endif; ?>
                        <?php elseif ($type === 'select_static'): ?>
                            <select class="form-control" id="<?= h($field) ?>" name="<?= h($field) ?>" <?= $required ?>>
                                <option value="">-- Chọn --</option>
                                <?php foreach (($meta['options'] ?? []) as $optionValue => $optionLabel): ?>
                                    <option value="<?= h($optionValue) ?>" <?= (string)$value === (string)$optionValue ? 'selected' : '' ?>><?= h($optionLabel) ?></option>
                                <?php endforeach; ?>
                            </select>
                        <?php elseif ($type === 'datetime'): ?>
                            <?php $dateValue = (($meta['nullable'] ?? false) && !$value) ? '' : format_datetime_for_input($value); ?>
                            <input class="form-control" id="<?= h($field) ?>" name="<?= h($field) ?>" type="datetime-local" value="<?= h($dateValue) ?>" <?= $required ?>>
                        <?php elseif ($type === 'date'): ?>
                            <input class="form-control" id="<?= h($field) ?>" name="<?= h($field) ?>" type="date" value="<?= h(format_date_for_input($value)) ?>" <?= $required ?>>
                        <?php elseif ($type === 'image'): ?>
                            <?php if ($value): ?><div><img class="thumb" src="<?= asset_url('Image/' . $value) ?>" alt="<?= h($value) ?>"></div><?php endif; ?>
                            <input type="hidden" name="<?= h($field) ?>" value="<?= h($value) ?>">
                            <input class="form-control" id="<?= h($field) ?>" name="<?= h($field) ?>_upload" type="file" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" <?= (!$value && $required) ? 'required' : '' ?>>
                        <?php elseif ($type === 'number'): ?>
                            <input class="form-control" id="<?= h($field) ?>" name="<?= h($field) ?>" type="number" step="any" value="<?= h($value) ?>" <?= $required ?> <?= $min ?> <?= $disabled ? 'readonly' : '' ?>>
                        <?php else: ?>
                            <input class="form-control" id="<?= h($field) ?>" name="<?= h($field) ?>" type="<?= h($type) ?>" value="<?= h($value) ?>" <?= $required ?> <?= $maxLength ?> <?= $pattern ?> <?= $disabled ? 'readonly' : '' ?>>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <div class="toolbar" style="margin-top:18px;">
            <button class="btn-main" type="submit"><?= $isRegistrationForm ? 'ĐĂNG KÝ' : 'LƯU' ?></button>
            <a class="btn-back" href="<?= h($data['backUrl'] ?? url_for($data['controller'], $data['listAction'])) ?>"><?= h($data['backText'] ?? 'QUAY VỀ') ?></a>
            <?php if (($data['controller'] ?? '') === 'DiemDanh_Assitant_64131060' && ($data['action'] ?? '') === 'Create'): ?>
                <a class="btn-back" href="<?= url_for('DiemDanh_Assitant_64131060', 'DiemDanh_Assitant_64131060') ?>">HIỂN THỊ ĐIỂM DANH</a>
            <?php endif; ?>
        </div>
    </form>
</section>