<?php
// Cấu hình SMTP mặc định ưu tiên biến môi trường; mail.local.php chỉ ghi đè khi file tồn tại.
$config = [
    'host' => 'smtp.gmail.com',
    'port' => 587,
    'timeout' => 20,
    'from' => getenv('MAIL_FROM') ?: '',
    'password' => getenv('MAIL_PASSWORD') ?: '',
];

$localConfig = __DIR__ . '/mail.local.php';
if (is_file($localConfig)) {
    // array_replace giữ giá trị mặc định cho các khóa không được cấu hình cục bộ.
    $config = array_replace($config, require $localConfig);
}

return $config;
