<?php
class CLB_Assitant_64131060Controller extends Controller
{
    private string $controllerName = 'CLB_Assitant_64131060';
    private string $listAction = 'CLB_Assitant_64131060';
    private string $pageTitle = 'Câu lạc bộ (Trợ giảng)';

    public function CLB_Assitant_64131060(): void { $this->index(); }

    public function index(): void
    {
        $this->requireRoles(['TVTG']);
        $this->renderCrudList($this->pageTitle, $this->controllerName, $this->listAction, $this->cfg(), $this->repo()->listClubs($this->currentMemberId()), true);
    }

    public function Details(...$params): void
    {
        $this->requireRoles(['TVTG']);
        $this->crudDetailsAction($this->controllerName, $this->listAction, $this->cfg(), $this->keys($params), fn($keys) => $this->repo()->findClub((string)$keys['MaCLB']), true, fn($row) => $this->guardClubScope((string)$row['MaCLB']));
    }

    public function Create(): void
    {
        $this->requireRoles(['TVTG']);
        $this->crudCreateAction($this->controllerName, $this->listAction, $this->cfg(), fn($data) => $this->repo()->createClub($data), 'Thêm câu lạc bộ', null, fn($data) => $this->guardClubWrite($data));
    }

    public function Edit(...$params): void
    {
        $this->requireRoles(['TVTG']);
        $this->crudEditAction($this->controllerName, $this->listAction, $this->cfg(), $this->keys($params), fn($keys) => $this->repo()->findClub((string)$keys['MaCLB']), fn($keys, $data) => $this->repo()->updateClub((string)$keys['MaCLB'], $data), 'Cập nhật câu lạc bộ', fn($row) => $this->guardClubScope((string)$row['MaCLB']), fn($data) => $this->guardClubWrite($data));
    }

    public function Delete(...$params): void
    {
        $this->requireRoles(['TVTG']);
        $this->crudDeleteAction($this->controllerName, $this->listAction, $this->cfg(), $this->keys($params), fn($keys) => $this->repo()->findClub((string)$keys['MaCLB']), fn($keys) => $this->repo()->deleteClub((string)$keys['MaCLB']), true, fn($row) => $this->guardClubScope((string)$row['MaCLB']));
    }

    private function guardClubScope(string $maCLB): void
    {
        if (!$this->repo()->canManageClub($maCLB, $this->currentMemberId())) {
            $this->denyUnauthorized();
        }
    }

    private function guardClubWrite(array $data): void
    {
        $memberId = $this->currentMemberId();
        if ((string)($data['ChuNhiem'] ?? '') === $memberId) {
            return;
        }
        if (!empty($data['MaCLB']) && $this->repo()->canManageClub((string)$data['MaCLB'], $memberId)) {
            return;
        }
        $this->denyUnauthorized();
    }

    private function cfg(): array { return $this->resourceCfg('CLB'); }
    private function keys(array $params): array { return $this->keysFromRequest($this->cfg(), $params); }
}
