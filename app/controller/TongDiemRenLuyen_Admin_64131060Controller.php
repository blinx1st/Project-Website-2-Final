<?php
class TongDiemRenLuyen_Admin_64131060Controller extends Controller
{
    private string $controllerName = 'TongDiemRenLuyen_Admin_64131060';
    private string $listAction = 'TongDiemRenLuyen_Admin_64131060';
    private string $pageTitle = 'Tổng điểm rèn luyện';

    public function TongDiemRenLuyen_Admin_64131060(): void { $this->index(); }

    public function index(): void
    {
        $this->requireRoles(['TVCN']);
        $this->renderCrudList($this->pageTitle, $this->controllerName, $this->listAction, $this->cfg(), $this->repo()->listPointTotals(), false);
    }

    public function Details(...$params): void
    {
        $this->requireRoles(['TVCN']);
        $this->crudDetailsAction($this->controllerName, $this->listAction, $this->cfg(), $this->keys($params), fn($keys) => $this->repo()->findPointTotal($keys['MaTongDiem']), false);
    }

    public function Create(): void { $this->requireRoles(['TVCN']); $this->renderGeneratedPointWriteBlocked($this->controllerName, $this->listAction); }
    public function Edit(...$params): void { $this->requireRoles(['TVCN']); $this->renderGeneratedPointWriteBlocked($this->controllerName, $this->listAction); }
    public function Delete(...$params): void { $this->requireRoles(['TVCN']); $this->renderGeneratedPointWriteBlocked($this->controllerName, $this->listAction); }

    private function cfg(): array { return $this->resourceCfg('TongDiemRenLuyen'); }
    private function keys(array $params): array { return $this->keysFromRequest($this->cfg(), $params); }
}
