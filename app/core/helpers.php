<?php
function h($value): string
{
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

function base_url(string $path = ''): string
{
    $script = $_SERVER['SCRIPT_NAME'] ?? '/index.php';
    $base = rtrim(str_replace('\\', '/', dirname($script)), '/');
    if ($base === '' || $base === '.') {
        $base = '';
    }
    return $base . ($path !== '' ? '/' . ltrim($path, '/') : '');
}

function url_for(string $controller, string $action = '', array $params = []): string
{
    $path = trim($controller, '/');
    if ($action !== '') {
        $path .= '/' . trim($action, '/');
    }
    $url = base_url($path);
    return $params ? $url . '?' . http_build_query($params) : $url;
}

function asset_url(string $path): string
{
    return base_url($path);
}

function redirect_to(string $controller, string $action = '', array $params = []): void
{
    header('Location: ' . url_for($controller, $action, $params));
    exit;
}

function current_role(): ?string
{
    return $_SESSION['MaVaiTro'] ?? null;
}

function current_email(): ?string
{
    return $_SESSION['Email'] ?? null;
}

function current_member_id(): ?string
{
    return $_SESSION['MaThanhVien'] ?? null;
}

function current_user_name(): string
{
    foreach (['HoTen', 'Email', 'MaThanhVien'] as $key) {
        $value = trim((string)($_SESSION[$key] ?? ''));
        if ($value !== '') {
            return $value;
        }
    }
    return '';
}

function format_datetime_for_input($value): string
{
    if (!$value) {
        return date('Y-m-d\TH:i');
    }
    $ts = strtotime((string)$value);
    return $ts ? date('Y-m-d\TH:i', $ts) : (string)$value;
}

function format_date_for_input($value): string
{
    if (!$value) {
        return date('Y-m-d');
    }
    $ts = strtotime((string)$value);
    return $ts ? date('Y-m-d', $ts) : (string)$value;
}

function lower_text(string $value): string
{
    return function_exists('mb_strtolower') ? mb_strtolower($value, 'UTF-8') : strtolower($value);
}
