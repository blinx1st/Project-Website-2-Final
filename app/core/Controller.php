<?php
require_once APP_PATH . '/core/CrudSupport.php';

// Base Controller chứa các hàm nền dùng chung: render view, kiểm tra đăng nhập, phân quyền và trả JSON.
class Controller
{
    use CrudSupport;

    protected ?Repository $repo = null;

    public function __construct()
    {
    }

    protected function repo(): Repository
    {
        if ($this->repo === null) {
            $this->repo = new Repository();
        }
        return $this->repo;
    }

    protected function render(string $view, array $data = [], string $layout = 'main'): void
    {
        $viewFile = APP_PATH . '/views/' . $view . '.php';
        if (!is_file($viewFile)) {
            http_response_code(500);
            echo 'View not found: ' . h($view);
            return;
        }
        ob_start();
        require $viewFile;
        $content = ob_get_clean();
        $layoutFile = APP_PATH . '/views/layouts/' . $layout . '.php';
        if (is_file($layoutFile)) {
            require $layoutFile;
            return;
        }
        echo $content;
    }

    protected function notFound(string $message = 'Không tìm thấy dữ liệu.'): void
    {
        http_response_code(404);
        $this->render('generic/message', ['title' => 'Không tìm thấy', 'message' => $message]);
    }

    protected function isPost(): bool
    {
        return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
    }

    protected function requireLogin(): void
    {
        if (!current_role()) {
            if ($this->wantsJson()) {
                $this->json(['success' => false, 'message' => 'Bạn cần đăng nhập.'], 401);
            }
            redirect_to('Login_64131060', 'Login_64131060');
        }
    }

    protected function requireRoles(array $roles): void
    {
        $this->requireLogin();
        if (!in_array((string)current_role(), $roles, true)) {
            $this->denyUnauthorized();
        }
    }

    protected function denyUnauthorized(): void
    {
        if ($this->wantsJson()) {
            $this->json(['success' => false, 'message' => 'Bạn không có quyền thực hiện thao tác này.'], 403);
        }
        http_response_code(403);
        $home = current_role() === 'TVCN' ? 'AdminPage_64131060' : (current_role() === 'TVTG' ? 'AssistantPage_64131060' : 'MemberPage_64131060');
        $this->render('generic/message', [
            'title' => 'Không có quyền',
            'message' => 'Bạn không có quyền truy cập chức năng này.',
            'buttonText' => 'QUAY VỀ',
            'buttonUrl' => url_for('TrangChu_64131060', $home),
        ]);
        exit;
    }

    protected function wantsJson(): bool
    {
        $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
        $requestedWith = $_SERVER['HTTP_X_REQUESTED_WITH'] ?? '';
        return str_contains($accept, 'application/json') || strtolower($requestedWith) === 'xmlhttprequest';
    }

    protected function json(array $payload, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($payload, JSON_UNESCAPED_UNICODE);
        exit;
    }

    protected function resourceCfg(string $resource): array
    {
        static $configs = null;
        if ($configs === null) {
            $configs = require APP_PATH . '/config/resources.php';
        }
        if (!isset($configs[$resource])) {
            throw new InvalidArgumentException('Unknown resource config: ' . $resource);
        }
        return $configs[$resource];
    }

    protected function currentMemberId(): string
    {
        if (current_member_id()) {
            return (string)current_member_id();
        }
        $member = $this->repo()->findMemberByEmail(current_email());
        if ($member) {
            return (string)$member['MaThanhVien'];
        }
        redirect_to('Login_64131060', 'Login_64131060');
    }

    protected function renderProfile(string $controller, string $editAction): void
    {
        $this->requireLogin();
        $member = $this->repo()->findMemberByEmail(current_email());
        if (!$member) {
            redirect_to('Login_64131060', 'Login_64131060');
        }
        $this->render('generic/profile', [
            'title' => 'Trang cá nhân',
            'controller' => $controller,
            'editAction' => $editAction,
            'cfg' => $this->resourceCfg('ThanhVien'),
            'row' => $member,
            'canWrite' => true,
        ]);
    }

    protected function renderAlert(string $title, string $message, string $buttonText, string $controller, string $action): void
    {
        $this->render('generic/message', [
            'title' => $title,
            'message' => $message,
            'buttonText' => $buttonText,
            'buttonUrl' => url_for($controller, $action),
        ]);
    }

    protected function renderGeneratedPointWriteBlocked(string $controller, string $listAction): void
    {
        $this->render('generic/message', [
            'title' => 'Không nhập điểm thủ công',
            'message' => 'Điểm rèn luyện được tự động tính từ quy tắc điểm khi sinh viên tham gia sự kiện.',
            'buttonText' => 'QUAY VỀ',
            'buttonUrl' => url_for($controller, $listAction),
        ]);
    }


}
