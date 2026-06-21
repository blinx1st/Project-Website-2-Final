<?php
class ThanhVien_Member_64131060Controller extends Controller
{
    private string $controllerName = 'ThanhVien_Member_64131060';
    private string $listAction = 'Index';
    private string $pageTitle = 'Thành viên';

    public function ThanhVien_Member_64131060(): void { $this->index(); }

    public function index(): void
    {
        $this->requireRoles(['TV']);
        $this->renderCrudList($this->pageTitle, $this->controllerName, $this->listAction, $this->cfg(), $this->repo()->listMembers($this->currentMemberId()), true);
    }

    public function Details(...$params): void
    {
        $this->requireRoles(['TV']);
        $this->crudDetailsAction($this->controllerName, $this->listAction, $this->cfg(), $this->keys($params), fn($keys) => $this->repo()->findMember((string)$keys['MaThanhVien']), true, fn($row) => $this->guardOwnMember((string)$row['MaThanhVien']));
    }

    public function Create(): void
    {
        $cfg = $this->registrationConfig();
        if ($this->isPost()) {
            $row = [
                'MaThanhVien' => trim($_POST['MaThanhVien'] ?? ''),
                'HoTen' => trim($_POST['HoTen'] ?? ''),
                'Email' => trim($_POST['Email'] ?? ''),
                'MatKhau' => trim($_POST['MatKhau'] ?? ''),
            ];
            try {
                Validator::validateResource($cfg, $row);
                $this->repo()->createMember($row + ['MaVaiTro' => 'TV']);
                redirect_to($this->controllerName, 'SigninAlert_64131060');
            } catch (PDOException $e) {
                $this->renderRegistrationForm($cfg, $row, 'Mã số hoặc email đã tồn tại, vui lòng kiểm tra lại.');
            } catch (Throwable $e) {
                $this->renderRegistrationForm($cfg, $row, $e->getMessage());
            }
            return;
        }
        $this->renderRegistrationForm($cfg);
    }

    public function Edit(...$params): void
    {
        $this->requireRoles(['TV']);
        $this->crudEditAction($this->controllerName, $this->listAction, $this->cfg(), $this->keys($params), fn($keys) => $this->repo()->findMember((string)$keys['MaThanhVien']), fn($keys, $data) => $this->repo()->updateMember((string)$keys['MaThanhVien'], $data), 'Cập nhật thành viên', fn($row) => $this->guardOwnMember((string)$row['MaThanhVien']));
    }

    public function Delete(...$params): void
    {
        $this->requireRoles(['TV']);
        $this->crudDeleteAction($this->controllerName, $this->listAction, $this->cfg(), $this->keys($params), fn($keys) => $this->repo()->findMember((string)$keys['MaThanhVien']), fn($keys) => $this->repo()->deleteMember((string)$keys['MaThanhVien']), true, fn($row) => $this->guardOwnMember((string)$row['MaThanhVien']));
    }

    public function Member_Page_64131060(): void { $this->renderProfile($this->controllerName, 'Edit_Member_64131060'); }
    public function Edit_Member_64131060(...$params): void { $this->Edit(...$params); }
    public function SigninAlert_64131060(): void { $this->renderAlert('Đăng ký thành công', 'Tài khoản thành viên đã được tạo.', 'ĐĂNG NHẬP', 'Login_64131060', 'Login_64131060'); }

    private function guardOwnMember(string $maThanhVien): void
    {
        if ($maThanhVien !== $this->currentMemberId()) {
            $this->denyUnauthorized();
        }
    }

    private function registrationConfig(): array
    {
        return [
            'table' => 'ThanhVien',
            'pk' => ['MaThanhVien'],
            'title' => 'Đăng ký tài khoản',
            'fields' => [
                'MaThanhVien' => ['label' => 'Mã số', 'type' => 'text', 'required' => true, 'max_length' => 50],
                'HoTen' => ['label' => 'Họ tên', 'type' => 'text', 'required' => true, 'max_length' => 100],
                'Email' => ['label' => 'Email', 'type' => 'email', 'required' => true, 'max_length' => 100],
                'MatKhau' => ['label' => 'Mật khẩu', 'type' => 'password', 'required' => true, 'max_length' => 255],
            ],
            'list' => [],
        ];
    }

    private function renderRegistrationForm(array $cfg, array $row = [], string $error = ''): void
    {
        $this->render('generic/form', [
            'cfg' => $cfg,
            'row' => $row,
            'action' => 'Create',
            'title' => 'Đăng ký tài khoản',
            'error' => $error,
            'keys' => [],
            'relations' => [],
            'controller' => $this->controllerName,
            'listAction' => $this->listAction,
            'canWrite' => true,
            'backUrl' => url_for('Login_64131060', 'Login_64131060'),
            'backText' => 'QUAY VỀ ĐĂNG NHẬP',
        ]);
    }

    private function cfg(): array { return $this->resourceCfg('ThanhVien'); }
    private function keys(array $params): array { return $this->keysFromRequest($this->cfg(), $params); }
}
