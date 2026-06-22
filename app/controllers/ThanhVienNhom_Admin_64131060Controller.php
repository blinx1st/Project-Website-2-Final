<?php
class ThanhVienNhom_Admin_64131060Controller extends Controller
{
    private string $controllerName = 'ThanhVienNhom_Admin_64131060';
    private string $listAction = 'ThanhVienNhom_Admin_64131060';
    private string $pageTitle = 'Thành viên nhóm học tập';

    public function ThanhVienNhom_Admin_64131060(): void { $this->index(); }

    public function index(): void
    {
        $this->requireRoles(['TVCN']);
        $maNhom = trim($_GET['MaNhom'] ?? '');
        $this->renderCrudList($this->pageTitle, $this->controllerName, $this->listAction, $this->cfg(), $this->repo()->listStudyGroupMembers(null, $maNhom ?: null), true, [
            'groupFilter' => true,
            'groupOptions' => $this->groupOptions(),
            'selectedGroup' => $maNhom,
        ]);
    }

    public function Details(...$params): void
    {
        $this->requireRoles(['TVCN']);
        $this->crudDetailsAction($this->controllerName, $this->listAction, $this->cfg(), $this->keys($params), fn($keys) => $this->repo()->findStudyGroupMember((string)$keys['MaNhom'], (string)$keys['MaThanhVien']), true);
    }

    public function Create(): void
    {
        $this->requireRoles(['TVCN']);
        $this->crudCreateAction($this->controllerName, $this->listAction, $this->cfg(), fn($data) => $this->repo()->createStudyGroupMember($data), 'Thêm thành viên nhóm học tập');
    }

    public function Edit(...$params): void
    {
        $this->requireRoles(['TVCN']);
        $this->crudEditAction($this->controllerName, $this->listAction, $this->cfg(), $this->keys($params), fn($keys) => $this->repo()->findStudyGroupMember((string)$keys['MaNhom'], (string)$keys['MaThanhVien']), fn($keys, $data) => $this->repo()->updateStudyGroupMember((string)$keys['MaNhom'], (string)$keys['MaThanhVien'], $data), 'Cập nhật thành viên nhóm học tập');
    }

    public function Delete(...$params): void
    {
        $this->requireRoles(['TVCN']);
        $this->crudDeleteAction($this->controllerName, $this->listAction, $this->cfg(), $this->keys($params), fn($keys) => $this->repo()->findStudyGroupMember((string)$keys['MaNhom'], (string)$keys['MaThanhVien']), fn($keys) => $this->repo()->deleteStudyGroupMember((string)$keys['MaNhom'], (string)$keys['MaThanhVien']));
    }

    private function groupOptions(): array
    {
        return array_map(fn($group) => ['value' => $group['MaNhom'], 'label' => $group['TenNhom']], $this->repo()->listStudyGroups());
    }

    private function cfg(): array { return $this->resourceCfg('ThanhVienNhom'); }
    private function keys(array $params): array { return $this->keysFromRequest($this->cfg(), $params); }
}
