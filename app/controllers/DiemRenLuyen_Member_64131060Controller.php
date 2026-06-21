<?php
class DiemRenLuyen_Member_64131060Controller extends Controller
{
    private string $controllerName = 'DiemRenLuyen_Member_64131060';
    private string $listAction = 'DiemRenLuyen_Member_64131060';
    private string $pageTitle = 'Điểm rèn luyện của tôi';

    public function DiemRenLuyen_Member_64131060(): void { $this->index(); }

    public function index(): void
    {
        $this->requireRoles(['TV']);
        $this->renderCrudList($this->pageTitle, $this->controllerName, $this->listAction, $this->cfg(), $this->repo()->listPoints(null, null, $this->currentMemberId()), false);
    }

    public function Details(...$params): void
    {
        $this->requireRoles(['TV']);
        $this->crudDetailsAction($this->controllerName, $this->listAction, $this->cfg(), $this->keys($params), fn($keys) => $this->repo()->findPoint($keys['MaDiem']), false, fn($row) => $this->guardOwn((string)$row['MaThanhVien']));
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

    private function cfg(): array { return $this->resourceCfg('DiemRenLuyen'); }
    private function keys(array $params): array { return $this->keysFromRequest($this->cfg(), $params); }
}
