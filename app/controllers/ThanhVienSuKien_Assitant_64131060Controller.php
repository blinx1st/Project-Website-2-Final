<?php
class ThanhVienSuKien_Assitant_64131060Controller extends Controller
{
    private string $controllerName = 'ThanhVienSuKien_Assitant_64131060';
    private string $listAction = 'ThanhVienSuKien_Assitant_64131060';
    private string $pageTitle = 'Thành viên tham gia sự kiện (Trợ giảng)';

    public function ThanhVienSuKien_Assitant_64131060(): void { $this->index(); }

    public function index(): void
    {
        $this->requireRoles(['TVTG']);
        $this->renderCrudList($this->pageTitle, $this->controllerName, $this->listAction, $this->cfg(), $this->repo()->listEventRegistrations(null, $this->currentMemberId()), true);
    }

    public function Details(...$params): void
    {
        $this->requireRoles(['TVTG']);
        $this->crudDetailsAction($this->controllerName, $this->listAction, $this->cfg(), $this->keys($params), fn($keys) => $this->repo()->findEventRegistration((string)$keys['MaSuKien'], (string)$keys['MaThanhVien']), true, fn($row) => $this->guardEventScope((string)$row['MaSuKien']));
    }

    public function Create(): void
    {
        $this->requireRoles(['TVTG']);
        $this->crudCreateAction($this->controllerName, $this->listAction, $this->cfg(), fn($data) => $this->repo()->createEventRegistration($data), 'Thêm thành viên tham gia sự kiện', null, fn($data) => $this->guardEventScope((string)($data['MaSuKien'] ?? '')));
    }

    public function Edit(...$params): void
    {
        $this->requireRoles(['TVTG']);
        $this->crudEditAction($this->controllerName, $this->listAction, $this->cfg(), $this->keys($params), fn($keys) => $this->repo()->findEventRegistration((string)$keys['MaSuKien'], (string)$keys['MaThanhVien']), fn($keys, $data) => $this->repo()->updateEventRegistration((string)$keys['MaSuKien'], (string)$keys['MaThanhVien'], $data), 'Cập nhật thành viên tham gia sự kiện', fn($row) => $this->guardEventScope((string)$row['MaSuKien']), fn($data) => $this->guardEventScope((string)($data['MaSuKien'] ?? '')));
    }

    public function Delete(...$params): void
    {
        $this->requireRoles(['TVTG']);
        $this->crudDeleteAction($this->controllerName, $this->listAction, $this->cfg(), $this->keys($params), fn($keys) => $this->repo()->findEventRegistration((string)$keys['MaSuKien'], (string)$keys['MaThanhVien']), fn($keys) => $this->repo()->deleteEventRegistration((string)$keys['MaSuKien'], (string)$keys['MaThanhVien']), true, fn($row) => $this->guardEventScope((string)$row['MaSuKien']));
    }

    private function guardEventScope(string $maSuKien): void
    {
        if (!$maSuKien || !$this->repo()->canManageEvent($maSuKien, $this->currentMemberId())) {
            $this->denyUnauthorized();
        }
    }

    private function cfg(): array { return $this->resourceCfg('ThanhVienSuKien'); }
    private function keys(array $params): array { return $this->keysFromRequest($this->cfg(), $params); }
}
