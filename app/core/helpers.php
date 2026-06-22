<?php
// Các helper toàn cục được nạp từ index.php để controller và view cùng dùng một quy ước.

// Escape dữ liệu trước khi đưa vào HTML nhằm hạn chế chèn script/XSS.
function h($value): string
{
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

function base_url(string $path = ''): string
{
    // Lấy thư mục chứa index.php để URL vẫn đúng khi project chạy trong thư mục con của localhost.
    $script = $_SERVER['SCRIPT_NAME'] ?? '/index.php';
    $base = rtrim(str_replace('\\', '/', dirname($script)), '/');
    if ($base === '' || $base === '.') {
        $base = '';
    }
    return $base . ($path !== '' ? '/' . ltrim($path, '/') : '');
}

function url_for(string $controller, string $action = '', array $params = []): string
{
    // Router hiểu đường dẫn theo dạng Controller/Action; tham số bổ sung được đưa vào query string.
    $path = trim($controller, '/');
    if ($action !== '') {
        $path .= '/' . trim($action, '/');
    }
    $url = base_url($path);
    return $params ? $url . '?' . http_build_query($params) : $url;
}

function asset_url(string $path): string
{
    // Asset cũng dùng cùng base URL nhưng không cần controller/action.
    return base_url($path);
}

function redirect_to(string $controller, string $action = '', array $params = []): void
{
    // Sau redirect phải dừng request để code phía dưới không tiếp tục ghi dữ liệu hoặc render HTML.
    header('Location: ' . url_for($controller, $action, $params));
    exit;
}

function current_role(): ?string
{
    // MaVaiTro được Login Controller ghi vào session sau khi xác thực thành công.
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
    // Ưu tiên tên hiển thị; email và mã thành viên là phương án dự phòng cho session cũ/thiếu dữ liệu.
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
    // datetime-local yêu cầu ký tự T giữa ngày và giờ thay vì định dạng DATETIME của MySQL.
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
    // mb_strtolower xử lý đúng dấu tiếng Việt khi extension mbstring khả dụng.
    return function_exists('mb_strtolower') ? mb_strtolower($value, 'UTF-8') : strtolower($value);
}
