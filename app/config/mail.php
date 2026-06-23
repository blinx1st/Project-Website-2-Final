<?php
// Cấu hình SMTP mặc định ưu tiên biến môi trường; mail.local.php chỉ ghi đè khi file tồn tại.
// Dự án dùng Gmail SMTP nên tài khoản gửi cần App Password, không dùng mật khẩu đăng nhập Gmail thường.
$config = [
    'host' => getenv('MAIL_HOST') ?: 'smtp.gmail.com',
    'port' => (int)(getenv('MAIL_PORT') ?: 587),
    'timeout' => (int)(getenv('MAIL_TIMEOUT') ?: 20),
    'username' => getenv('MAIL_USERNAME') ?: '',
    'from_email' => getenv('MAIL_FROM_EMAIL') ?: '',
    'from' => getenv('MAIL_FROM') ?: '',
    'password' => getenv('MAIL_PASSWORD') ?: '',
];

$localConfig = __DIR__ . '/mail.local.php';
if (is_file($localConfig)) {
    // array_replace giữ giá trị mặc định cho các khóa không được cấu hình cục bộ.
    $config = array_replace($config, require $localConfig);
}

// Chuẩn hóa alias để Mailer và form hiển thị đều lấy được cùng một email gửi.
if (trim((string)$config['from']) === '') {
    $config['from'] = trim((string)($config['from_email'] ?: $config['username']));
}
if (trim((string)$config['username']) === '') {
    $config['username'] = trim((string)$config['from']);
}
if (trim((string)$config['from_email']) === '') {
    $config['from_email'] = trim((string)$config['from']);
}

return $config;
