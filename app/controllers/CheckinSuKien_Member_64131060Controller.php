<?php
class CheckinSuKien_Member_64131060Controller extends Controller
{
    private string $controllerName = 'CheckinSuKien_Member_64131060';
    private string $listAction = 'CheckinSuKien_Member_64131060';
    private string $pageTitle = 'Lịch sử check-in';

    public function CheckinSuKien_Member_64131060(): void { $this->index(); }

    public function index(): void
    {
        $this->requireRoles(['TV']);
        $this->renderCrudList($this->pageTitle, $this->controllerName, $this->listAction, $this->cfg(), $this->repo()->listCheckins($this->currentMemberId()), false);
    }

    public function Details(...$params): void
    {
        $this->requireRoles(['TV']);
        $this->crudDetailsAction($this->controllerName, $this->listAction, $this->cfg(), $this->keys($params), fn($keys) => $this->repo()->findCheckin($keys['MaCheckin']), false, fn($row) => $this->guardOwn((string)$row['MaThanhVien']));
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

    private function cfg(): array { return $this->resourceCfg('CheckinSuKien'); }
    private function keys(array $params): array { return $this->keysFromRequest($this->cfg(), $params); }
}
