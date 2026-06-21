<?php
class ThanhVienCLB_Admin_64131060Controller extends Controller
{
    private string $controllerName = 'ThanhVienCLB_Admin_64131060';
    private string $listAction = 'ThanhVienCLB_Admin_64131060';
    private string $pageTitle = 'Thành viên CLB';

    public function ThanhVienCLB_Admin_64131060(): void { $this->index(); }

    public function index(): void
    {
        $this->requireRoles(['TVCN']);
        $this->renderCrudList($this->pageTitle, $this->controllerName, $this->listAction, $this->cfg(), $this->repo()->listClubMembers(), true);
    }

    public function Details(...$params): void
    {
        $this->requireRoles(['TVCN']);
        $this->crudDetailsAction($this->controllerName, $this->listAction, $this->cfg(), $this->keys($params), fn($keys) => $this->repo()->findClubMember((string)$keys['MaCLB'], (string)$keys['MaThanhVien']), true);
    }

    public function Create(): void
    {
        $this->requireRoles(['TVCN']);
        $this->crudCreateAction($this->controllerName, $this->listAction, $this->cfg(), fn($data) => $this->repo()->createClubMember($data), 'Thêm thành viên CLB');
    }

    public function Edit(...$params): void
    {
        $this->requireRoles(['TVCN']);
        $this->crudEditAction($this->controllerName, $this->listAction, $this->cfg(), $this->keys($params), fn($keys) => $this->repo()->findClubMember((string)$keys['MaCLB'], (string)$keys['MaThanhVien']), fn($keys, $data) => $this->repo()->updateClubMember((string)$keys['MaCLB'], (string)$keys['MaThanhVien'], $data), 'Cập nhật thành viên CLB');
    }

    public function Delete(...$params): void
    {
        $this->requireRoles(['TVCN']);
        $this->crudDeleteAction($this->controllerName, $this->listAction, $this->cfg(), $this->keys($params), fn($keys) => $this->repo()->findClubMember((string)$keys['MaCLB'], (string)$keys['MaThanhVien']), fn($keys) => $this->repo()->deleteClubMember((string)$keys['MaCLB'], (string)$keys['MaThanhVien']));
    }

    private function cfg(): array { return $this->resourceCfg('ThanhVienCLB'); }
    private function keys(array $params): array { return $this->keysFromRequest($this->cfg(), $params); }
}
