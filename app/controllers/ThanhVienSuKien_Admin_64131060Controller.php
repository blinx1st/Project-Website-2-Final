<?php
class ThanhVienSuKien_Admin_64131060Controller extends Controller
{
    private string $controllerName = 'ThanhVienSuKien_Admin_64131060';
    private string $listAction = 'ThanhVienSuKien_Admin_64131060';
    private string $pageTitle = 'Thành viên tham gia sự kiện';

    public function ThanhVienSuKien_Admin_64131060(): void { $this->index(); }

    public function index(): void
    {
        $this->requireRoles(['TVCN']);
        $this->renderCrudList($this->pageTitle, $this->controllerName, $this->listAction, $this->cfg(), $this->repo()->listEventRegistrations(), true);
    }

    public function Details(...$params): void
    {
        $this->requireRoles(['TVCN']);
        $this->crudDetailsAction($this->controllerName, $this->listAction, $this->cfg(), $this->keys($params), fn($keys) => $this->repo()->findEventRegistration((string)$keys['MaSuKien'], (string)$keys['MaThanhVien']), true);
    }

    public function Create(): void
    {
        $this->requireRoles(['TVCN']);
        $this->crudCreateAction($this->controllerName, $this->listAction, $this->cfg(), fn($data) => $this->repo()->createEventRegistration($data), 'Thêm thành viên tham gia sự kiện');
    }

    public function Edit(...$params): void
    {
        $this->requireRoles(['TVCN']);
        $this->crudEditAction($this->controllerName, $this->listAction, $this->cfg(), $this->keys($params), fn($keys) => $this->repo()->findEventRegistration((string)$keys['MaSuKien'], (string)$keys['MaThanhVien']), fn($keys, $data) => $this->repo()->updateEventRegistration((string)$keys['MaSuKien'], (string)$keys['MaThanhVien'], $data), 'Cập nhật thành viên tham gia sự kiện');
    }

    public function Delete(...$params): void
    {
        $this->requireRoles(['TVCN']);
        $this->crudDeleteAction($this->controllerName, $this->listAction, $this->cfg(), $this->keys($params), fn($keys) => $this->repo()->findEventRegistration((string)$keys['MaSuKien'], (string)$keys['MaThanhVien']), fn($keys) => $this->repo()->deleteEventRegistration((string)$keys['MaSuKien'], (string)$keys['MaThanhVien']));
    }

    private function cfg(): array { return $this->resourceCfg('ThanhVienSuKien'); }
    private function keys(array $params): array { return $this->keysFromRequest($this->cfg(), $params); }
}
