<?php
// Router đọc URL dạng Controller/Action/Params và gọi đúng controller/action tương ứng.
class Router
{
    public function dispatch(): void
    {
        // .htaccess chuyển URL đẹp thành query url; Router tách nó thành controller, action và params.
        $path = trim($_GET['url'] ?? '', '/');
        $parts = $path === '' ? [] : array_values(array_filter(explode('/', $path), fn($part) => $part !== ''));
        $controller = $this->clean($parts[0] ?? 'TrangChu_64131060');
        $action = $this->clean($parts[1] ?? $controller);
        $params = array_slice($parts, 2);

        // Quy ước tên ánh xạ URL sang file/class mà không cần đăng ký từng route thủ công.
        $file = APP_PATH . '/controllers/' . $controller . 'Controller.php';
        $class = $controller . 'Controller';
        if (!is_file($file)) {
            // Controller không tồn tại được đưa về trang chủ để ứng dụng vẫn có điểm rơi an toàn.
            $controller = 'TrangChu_64131060';
            $action = 'TrangChu_64131060';
            $params = [];
            $file = APP_PATH . '/controllers/TrangChu_64131060Controller.php';
            $class = 'TrangChu_64131060Controller';
        }
        require_once $file;
        $instance = new $class();
        // Nếu action yêu cầu không tồn tại thì thử index; không có nữa mới trả 404.
        if (!method_exists($instance, $action)) {
            $action = method_exists($instance, 'index') ? 'index' : '';
        }
        if ($action === '') {
            http_response_code(404);
            echo 'Action not found.';
            return;
        }
        // Các phần URL còn lại được truyền theo đúng thứ tự vào tham số của action.
        call_user_func_array([$instance, $action], $params);
    }

    private function clean(string $value): string
    {
        // Chỉ cho phép ký tự hợp lệ trong tên class/method, tránh chèn đường dẫn như ../ vào tên file.
        return preg_replace('/[^A-Za-z0-9_]/', '', $value) ?: 'TrangChu_64131060';
    }
}
