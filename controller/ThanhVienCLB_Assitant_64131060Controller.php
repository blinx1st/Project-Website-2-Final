<?php
class ThanhVienCLB_Assitant_64131060Controller extends Controller
{
    private string $controllerName = 'ThanhVienCLB_Assitant_64131060';
    private string $listAction = 'ThanhVienCLB_Assitant_64131060';
    private string $pageTitle = 'Thành viên CLB (Trợ giảng)';

    public function ThanhVienCLB_Assitant_64131060(): void { $this->index(); }

    public function index(): void
    {
        $this->requireRoles(['TVTG']);
        $this->renderCrudList($this->pageTitle, $this->controllerName, $this->listAction, $this->cfg(), $this->repo()->listClubMembers($this->currentMemberId()), true);
    }

    public function Details(...$params): void
    {
        $this->requireRoles(['TVTG']);
        $this->crudDetailsAction($this->controllerName, $this->listAction, $this->cfg(), $this->keys($params), fn($keys) => $this->repo()->findClubMember((string)$keys['MaCLB'], (string)$keys['MaThanhVien']), true, fn($row) => $this->guardClubScope((string)$row['MaCLB']));
    }

    public function Create(): void
    {
        $this->requireRoles(['TVTG']);
        $this->crudCreateAction($this->controllerName, $this->listAction, $this->cfg(), fn($data) => $this->repo()->createClubMember($data), 'Thêm thành viên CLB', null, fn($data) => $this->guardClubScope((string)($data['MaCLB'] ?? '')));
    }

    public function Edit(...$params): void
    {
        $this->requireRoles(['TVTG']);
        $this->crudEditAction($this->controllerName, $this->listAction, $this->cfg(), $this->keys($params), fn($keys) => $this->repo()->findClubMember((string)$keys['MaCLB'], (string)$keys['MaThanhVien']), fn($keys, $data) => $this->repo()->updateClubMember((string)$keys['MaCLB'], (string)$keys['MaThanhVien'], $data), 'Cập nhật thành viên CLB', fn($row) => $this->guardClubScope((string)$row['MaCLB']), fn($data) => $this->guardClubScope((string)($data['MaCLB'] ?? '')));
    }

    public function Delete(...$params): void
    {
        $this->requireRoles(['TVTG']);
        $this->crudDeleteAction($this->controllerName, $this->listAction, $this->cfg(), $this->keys($params), fn($keys) => $this->repo()->findClubMember((string)$keys['MaCLB'], (string)$keys['MaThanhVien']), fn($keys) => $this->repo()->deleteClubMember((string)$keys['MaCLB'], (string)$keys['MaThanhVien']), true, fn($row) => $this->guardClubScope((string)$row['MaCLB']));
    }

    private function guardClubScope(string $maCLB): void
    {
        if (!$maCLB || !$this->repo()->canManageClub($maCLB, $this->currentMemberId())) {
            $this->denyUnauthorized();
        }
    }

    private function cfg(): array { return $this->resourceCfg('ThanhVienCLB'); }
    private function keys(array $params): array { return $this->keysFromRequest($this->cfg(), $params); }
}
