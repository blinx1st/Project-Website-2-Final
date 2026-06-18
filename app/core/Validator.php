<?php
// Validator kiểm tra dữ liệu ở backend trước khi ghi database, tránh chỉ phụ thuộc vào HTML/JS phía client.
class Validator
{
    public static function validateResource(array $cfg, array $data): void
    {
        $errors = [];
        foreach ($cfg['fields'] as $field => $meta) {
            if (($meta['readonly'] ?? false) && in_array($field, $cfg['auto'] ?? [], true)) {
                continue;
            }
            $label = $meta['label'] ?? $field;
            $value = trim((string)($data[$field] ?? ''));
            if (!empty($meta['required']) && $value === '') {
                $errors[] = $label . ' không được để trống.';
                continue;
            }
            if ($value === '') {
                continue;
            }
            $type = $meta['type'] ?? 'text';
            if ($type === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                $errors[] = $label . ' không đúng định dạng email.';
            }
            if (in_array($type, ['number', 'decimal'], true) && !is_numeric($value)) {
                $errors[] = $label . ' phải là số.';
            }
            if (isset($meta['min']) && is_numeric($value) && (float)$value < (float)$meta['min']) {
                $errors[] = $label . ' phải lớn hơn hoặc bằng ' . $meta['min'] . '.';
            }
            $length = function_exists('mb_strlen') ? mb_strlen($value, 'UTF-8') : strlen($value);
            if (isset($meta['max_length']) && $length > (int)$meta['max_length']) {
                $errors[] = $label . ' không được vượt quá ' . $meta['max_length'] . ' ký tự.';
            }
            if (isset($meta['pattern']) && !preg_match($meta['pattern'], $value)) {
                $errors[] = $label . ' không đúng định dạng.';
            }
        }

        $table = $cfg['table'] ?? '';
        if ($table === 'SuKien' && !empty($data['NgayBatDau']) && !empty($data['NgayKetThuc'])) {
            $start = strtotime((string)$data['NgayBatDau']);
            $end = strtotime((string)$data['NgayKetThuc']);
            if ($start !== false && $end !== false && $end < $start) {
                $errors[] = 'Ngày kết thúc phải sau hoặc bằng ngày bắt đầu.';
            }
        }
        if ($table === 'SuKien' && !empty($data['CheckinMoLuc']) && !empty($data['CheckinDongLuc'])) {
            $open = strtotime((string)$data['CheckinMoLuc']);
            $close = strtotime((string)$data['CheckinDongLuc']);
            if ($open !== false && $close !== false && $close < $open) {
                $errors[] = 'Thời gian đóng QR phải sau hoặc bằng thời gian mở QR.';
            }
        }
        if (array_key_exists('HocKy', $data) && trim((string)$data['HocKy']) !== '' && !in_array($data['HocKy'], ['HK1', 'HK2', 'HK3'], true)) {
            $errors[] = 'Học kỳ chỉ được chọn HK1, HK2 hoặc HK3.';
        }
        if (array_key_exists('NamHoc', $data) && trim((string)$data['NamHoc']) !== '' && !preg_match('/^\d{4}-\d{4}$/', (string)$data['NamHoc'])) {
            $errors[] = 'Năm học phải có dạng 2024-2025.';
        }
        foreach (['Diem', 'SoDiem', 'TongDiem'] as $field) {
            if (array_key_exists($field, $data) && trim((string)$data[$field]) !== '' && (float)$data[$field] < 0) {
                $errors[] = $field . ' không được âm.';
            }
        }

        if ($errors) {
            throw new InvalidArgumentException(implode(' ', $errors));
        }
    }

    public static function validateImageUpload(array $file, int $maxBytes = 2097152): void
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return;
        }
        if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
            throw new InvalidArgumentException('File ảnh tải lên không hợp lệ.');
        }
        if (($file['size'] ?? 0) > $maxBytes) {
            throw new InvalidArgumentException('File ảnh không được vượt quá 2MB.');
        }
        $extension = strtolower(pathinfo((string)($file['name'] ?? ''), PATHINFO_EXTENSION));
        if (!in_array($extension, ['jpg', 'jpeg', 'png', 'webp'], true)) {
            throw new InvalidArgumentException('Ảnh chỉ chấp nhận JPG, PNG hoặc WEBP.');
        }
        $info = @getimagesize((string)$file['tmp_name']);
        if (!$info || !in_array($info['mime'] ?? '', ['image/jpeg', 'image/png', 'image/webp'], true)) {
            throw new InvalidArgumentException('Nội dung file không phải ảnh hợp lệ.');
        }
    }
}
