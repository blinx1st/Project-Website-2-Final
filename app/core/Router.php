<?php
// Router đọc URL dạng Controller/Action/Params và gọi đúng controller/action tương ứng.
class Router
{
    public function dispatch(): void
    {
        $path = trim($_GET['url'] ?? '', '/');
        $parts = $path === '' ? [] : array_values(array_filter(explode('/', $path), fn($part) => $part !== ''));
        $controller = $this->clean($parts[0] ?? 'TrangChu_64131060');
        $action = $this->clean($parts[1] ?? $controller);
        $params = array_slice($parts, 2);

        $file = APP_PATH . '/controllers/' . $controller . 'Controller.php';
        $class = $controller . 'Controller';
        if (!is_file($file)) {
            $controller = 'TrangChu_64131060';
            $action = 'TrangChu_64131060';
            $params = [];
            $file = APP_PATH . '/controllers/TrangChu_64131060Controller.php';
            $class = 'TrangChu_64131060Controller';
        }
        require_once $file;
        $instance = new $class();
        if (!method_exists($instance, $action)) {
            $action = method_exists($instance, 'index') ? 'index' : '';
        }
        if ($action === '') {
            http_response_code(404);
            echo 'Action not found.';
            return;
        }
        call_user_func_array([$instance, $action], $params);
    }

    private function clean(string $value): string
    {
        return preg_replace('/[^A-Za-z0-9_]/', '', $value) ?: 'TrangChu_64131060';
    }
}
