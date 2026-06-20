<?php
class TongDiemRenLuyen_Member_64131060Controller extends Controller
{
    private string $controllerName = 'TongDiemRenLuyen_Member_64131060';
    private string $listAction = 'TongDiemRenLuyen_Member_64131060';
    private string $pageTitle = 'Tổng điểm rèn luyện của tôi';

    public function TongDiemRenLuyen_Member_64131060(): void { $this->index(); }

    public function index(): void
    {
        $this->requireRoles(['TV']);
        $this->renderCrudList($this->pageTitle, $this->controllerName, $this->listAction, $this->cfg(), $this->repo()->listPointTotals($this->currentMemberId()), false);
    }

    public function Details(...$params): void
    {
        $this->requireRoles(['TV']);
        $this->crudDetailsAction($this->controllerName, $this->listAction, $this->cfg(), $this->keys($params), fn($keys) => $this->repo()->findPointTotal($keys['MaTongDiem']), false, fn($row) => $this->guardOwn((string)$row['MaThanhVien']));
    }

    public function Create(): void { $this->requireRoles(['TV']); $this->renderGeneratedPointWriteBlocked($this->controllerName, $this->listAction); }
    public function Edit(...$params): void { $this->requireRoles(['TV']); $this->renderGeneratedPointWriteBlocked($this->controllerName, $this->listAction); }
    public function Delete(...$params): void { $this->requireRoles(['TV']); $this->renderGeneratedPointWriteBlocked($this->controllerName, $this->listAction); }

    private function guardOwn(string $maThanhVien): void
    {
        if ($maThanhVien !== $this->currentMemberId()) {
            $this->denyUnauthorized();
        }
    }

    private function cfg(): array { return $this->resourceCfg('TongDiemRenLuyen'); }
    private function keys(array $params): array { return $this->keysFromRequest($this->cfg(), $params); }
}
