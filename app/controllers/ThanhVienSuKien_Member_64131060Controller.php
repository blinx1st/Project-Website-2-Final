<?php
class ThanhVienSuKien_Member_64131060Controller extends Controller
{
    private string $controllerName = 'ThanhVienSuKien_Member_64131060';
    private string $listAction = 'ThanhVienSuKien_Member_64131060';
    private string $pageTitle = 'Sự kiện đã đăng ký';

    public function ThanhVienSuKien_Member_64131060(): void { $this->index(); }

    public function index(): void
    {
        $this->requireRoles(['TV']);
        $this->renderCrudList($this->pageTitle, $this->controllerName, $this->listAction, $this->cfg(), $this->repo()->listEventRegistrations($this->currentMemberId()), false);
    }

    public function Details(...$params): void
    {
        $this->requireRoles(['TV']);
        $this->crudDetailsAction($this->controllerName, $this->listAction, $this->cfg(), $this->keys($params), fn($keys) => $this->repo()->findEventRegistration((string)$keys['MaSuKien'], (string)$keys['MaThanhVien']), false, fn($row) => $this->guardOwn((string)$row['MaThanhVien']));
    }

    public function Create(): void { $this->denyUnauthorized(); }
    public function Edit(...$params): void { $this->denyUnauthorized(); }
    public function Delete(...$params): void { $this->denyUnauthorized(); }

    private function guardOwn(string $maThanhVien): void
    {
        if ($maThanhVien !== $this->currentMemberId()) {
            $this->denyUnauthorized();
        }
    }

    private function cfg(): array { return $this->resourceCfg('ThanhVienSuKien'); }
    private function keys(array $params): array { return $this->keysFromRequest($this->cfg(), $params); }
}
