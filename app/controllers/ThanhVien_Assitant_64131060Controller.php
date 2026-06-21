<?php
class ThanhVien_Assitant_64131060Controller extends Controller
{
    private string $controllerName = 'ThanhVien_Assitant_64131060';
    private string $listAction = 'TimKiemTV_Assitant_64131060';
    private string $pageTitle = 'Thành viên của câu lạc bộ (Trợ giảng)';

    public function ThanhVien_Assitant_64131060(): void { $this->index(); }

    public function index(): void
    {
        $this->requireRoles(['TVTG']);
        $this->renderCrudList($this->pageTitle, $this->controllerName, $this->listAction, $this->cfg(), $this->repo()->listMembers(), true);
    }

    public function TimKiemTV_Assitant_64131060(): void
    {
        $this->requireRoles(['TVTG']);
        $ma = trim($_GET['mathanhvien'] ?? '');
        $ten = trim($_GET['hoten'] ?? '');
        $rows = $this->repo()->searchMembers($ma, $ten);
        $this->renderCrudList($this->pageTitle, $this->controllerName, 'TimKiemTV_Assitant_64131060', $this->cfg(), $rows, true, [
            'search' => 'members',
            'searchValues' => ['mathanhvien' => $ma, 'hoten' => $ten],
            'emptyMessage' => $rows ? '' : 'Không tìm thấy sinh viên này.',
        ]);
    }

    public function Details(...$params): void
    {
        $this->requireRoles(['TVTG']);
        $this->crudDetailsAction($this->controllerName, $this->listAction, $this->cfg(), $this->keys($params), fn($keys) => $this->repo()->findMember((string)$keys['MaThanhVien']), true);
    }

    public function Create(): void
    {
        $this->requireRoles(['TVTG']);
        $this->crudCreateAction($this->controllerName, $this->listAction, $this->cfg(), fn($data) => $this->repo()->createMember($data), 'Thêm thành viên');
    }

    public function Edit(...$params): void
    {
        $this->requireRoles(['TVTG']);
        $this->crudEditAction($this->controllerName, $this->listAction, $this->cfg(), $this->keys($params), fn($keys) => $this->repo()->findMember((string)$keys['MaThanhVien']), fn($keys, $data) => $this->repo()->updateMember((string)$keys['MaThanhVien'], $data), 'Cập nhật thành viên');
    }

    public function Delete(...$params): void
    {
        $this->requireRoles(['TVTG']);
        $this->crudDeleteAction($this->controllerName, $this->listAction, $this->cfg(), $this->keys($params), fn($keys) => $this->repo()->findMember((string)$keys['MaThanhVien']), fn($keys) => $this->repo()->deleteMember((string)$keys['MaThanhVien']));
    }

    public function Assitant_Page_64131060(): void { $this->renderProfile($this->controllerName, 'Edit_Assistant_64131060'); }
    public function Edit_Assistant_64131060(...$params): void { $this->Edit(...$params); }

    private function cfg(): array { return $this->resourceCfg('ThanhVien'); }
    private function keys(array $params): array { return $this->keysFromRequest($this->cfg(), $params); }
}
