<?php
$config = [
    'host' => 'smtp.gmail.com',
    'port' => 587,
    'timeout' => 20,
    'from' => getenv('MAIL_FROM') ?: '',
    'password' => getenv('MAIL_PASSWORD') ?: '',
];

$localConfig = __DIR__ . '/mail.local.php';
if (is_file($localConfig)) {
    $config = array_replace($config, require $localConfig);
}

return $config;
