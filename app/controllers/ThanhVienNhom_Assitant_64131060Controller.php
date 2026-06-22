<?php
class ThanhVienNhom_Assitant_64131060Controller extends Controller
{
    private string $controllerName = 'ThanhVienNhom_Assitant_64131060';
    private string $listAction = 'ThanhVienNhom_Assitant_64131060';
    private string $pageTitle = 'Thành viên nhóm học tập (Trợ giảng)';

    public function ThanhVienNhom_Assitant_64131060(): void { $this->index(); }

    public function index(): void
    {
        $this->requireRoles(['TVTG']);
        $assistantId = $this->currentMemberId();
        $maNhom = trim($_GET['MaNhom'] ?? '');
        if ($maNhom !== '') {
            $this->guardStudyGroupScope($maNhom);
        }
        $this->renderCrudList($this->pageTitle, $this->controllerName, $this->listAction, $this->cfg(), $this->repo()->listStudyGroupMembers($assistantId, $maNhom ?: null), true, [
            'groupFilter' => true,
            'groupOptions' => $this->groupOptions(),
            'selectedGroup' => $maNhom,
        ]);
    }

    public function Details(...$params): void
    {
        $this->requireRoles(['TVTG']);
        $this->crudDetailsAction($this->controllerName, $this->listAction, $this->cfg(), $this->keys($params), fn($keys) => $this->repo()->findStudyGroupMember((string)$keys['MaNhom'], (string)$keys['MaThanhVien']), true, fn($row) => $this->guardStudyGroupScope((string)$row['MaNhom']));
    }

    public function Create(): void
    {
        $this->requireRoles(['TVTG']);
        $this->crudCreateAction($this->controllerName, $this->listAction, $this->cfg(), fn($data) => $this->repo()->createStudyGroupMember($data), 'Thêm thành viên nhóm học tập', null, fn($data) => $this->guardStudyGroupScope((string)($data['MaNhom'] ?? '')), true, $this->relations());
    }

    public function Edit(...$params): void
    {
        $this->requireRoles(['TVTG']);
        $this->crudEditAction($this->controllerName, $this->listAction, $this->cfg(), $this->keys($params), fn($keys) => $this->repo()->findStudyGroupMember((string)$keys['MaNhom'], (string)$keys['MaThanhVien']), fn($keys, $data) => $this->repo()->updateStudyGroupMember((string)$keys['MaNhom'], (string)$keys['MaThanhVien'], $data), 'Cập nhật thành viên nhóm học tập', fn($row) => $this->guardStudyGroupScope((string)$row['MaNhom']), fn($data) => $this->guardStudyGroupScope((string)($data['MaNhom'] ?? '')), true, $this->relations());
    }

    public function Delete(...$params): void
    {
        $this->requireRoles(['TVTG']);
        $this->crudDeleteAction($this->controllerName, $this->listAction, $this->cfg(), $this->keys($params), fn($keys) => $this->repo()->findStudyGroupMember((string)$keys['MaNhom'], (string)$keys['MaThanhVien']), fn($keys) => $this->repo()->deleteStudyGroupMember((string)$keys['MaNhom'], (string)$keys['MaThanhVien']), true, fn($row) => $this->guardStudyGroupScope((string)$row['MaNhom']));
    }

    private function guardStudyGroupScope(string $maNhom): void
    {
        if (!$maNhom || !$this->repo()->canManageStudyGroup($maNhom, $this->currentMemberId())) {
            $this->denyUnauthorized();
        }
    }

    private function groupOptions(): array
    {
        return array_map(fn($group) => ['value' => $group['MaNhom'], 'label' => $group['TenNhom']], $this->repo()->listStudyGroups($this->currentMemberId()));
    }

    private function relations(): array
    {
        return [
            'MaNhom' => $this->groupOptions(),
            'MaThanhVien' => $this->repo()->options(['table' => 'ThanhVien', 'value' => 'MaThanhVien', 'label' => 'HoTen']),
        ];
    }

    private function cfg(): array { return $this->resourceCfg('ThanhVienNhom'); }
    private function keys(array $params): array { return $this->keysFromRequest($this->cfg(), $params); }
}
