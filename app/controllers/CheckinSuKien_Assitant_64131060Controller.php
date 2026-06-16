<?php
class CheckinSuKien_Assitant_64131060Controller extends Controller
{
    private string $controllerName = 'CheckinSuKien_Assitant_64131060';
    private string $listAction = 'CheckinSuKien_Assitant_64131060';
    private string $pageTitle = 'Log check-in sự kiện (Trợ giảng)';

    public function CheckinSuKien_Assitant_64131060(): void { $this->index(); }

    public function index(): void
    {
        $this->requireRoles(['TVTG']);
        $this->renderCrudList($this->pageTitle, $this->controllerName, $this->listAction, $this->cfg(), $this->repo()->listCheckins(null, $this->currentMemberId()), false);
    }

    public function Details(...$params): void
    {
        $this->requireRoles(['TVTG']);
        $this->crudDetailsAction($this->controllerName, $this->listAction, $this->cfg(), $this->keys($params), fn($keys) => $this->repo()->findCheckin($keys['MaCheckin']), false, fn($row) => $this->guardEventScope((string)$row['MaSuKien']));
    }

    public function Create(): void { $this->denyUnauthorized(); }
    public function Edit(...$params): void { $this->denyUnauthorized(); }
    public function Delete(...$params): void { $this->denyUnauthorized(); }

    private function guardEventScope(string $maSuKien): void
    {
        if (!$maSuKien || !$this->repo()->canManageEvent($maSuKien, $this->currentMemberId())) {
            $this->denyUnauthorized();
        }
    }

    private function cfg(): array { return $this->resourceCfg('CheckinSuKien'); }
    private function keys(array $params): array { return $this->keysFromRequest($this->cfg(), $params); }
}
