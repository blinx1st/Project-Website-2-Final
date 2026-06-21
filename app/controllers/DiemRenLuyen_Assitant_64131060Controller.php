<?php
class DiemRenLuyen_Assitant_64131060Controller extends Controller
{
    private string $controllerName = 'DiemRenLuyen_Assitant_64131060';
    private string $listAction = 'DiemRenLuyen_Assitant_64131060';
    private string $pageTitle = 'Điểm rèn luyện (Trợ giảng)';

    public function DiemRenLuyen_Assitant_64131060(): void { $this->index(); }

    public function index(): void
    {
        $this->requireRoles(['TVTG']);
        $this->renderCrudList($this->pageTitle, $this->controllerName, $this->listAction, $this->cfg(), $this->repo()->listPoints(null, null, null, $this->currentMemberId()), false);
    }

    public function Details(...$params): void
    {
        $this->requireRoles(['TVTG']);
        $this->crudDetailsAction($this->controllerName, $this->listAction, $this->cfg(), $this->keys($params), fn($keys) => $this->repo()->findPoint($keys['MaDiem']), false, fn($row) => $this->guardEventScope((string)$row['MaSuKien']));
    }

    public function Create(): void { $this->requireRoles(['TVTG']); $this->renderGeneratedPointWriteBlocked($this->controllerName, $this->listAction); }
    public function Edit(...$params): void { $this->requireRoles(['TVTG']); $this->renderGeneratedPointWriteBlocked($this->controllerName, $this->listAction); }
    public function Delete(...$params): void { $this->requireRoles(['TVTG']); $this->renderGeneratedPointWriteBlocked($this->controllerName, $this->listAction); }

    private function guardEventScope(string $maSuKien): void
    {
        if (!$maSuKien || !$this->repo()->canManageEvent($maSuKien, $this->currentMemberId())) {
            $this->denyUnauthorized();
        }
    }

    private function cfg(): array { return $this->resourceCfg('DiemRenLuyen'); }
    private function keys(array $params): array { return $this->keysFromRequest($this->cfg(), $params); }
}
