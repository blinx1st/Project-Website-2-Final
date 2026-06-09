<?php
class SuKien_Member_64131060Controller extends Controller
{
    private string $controllerName = 'SuKien_Member_64131060';
    private string $listAction = 'TimKiemSuKien_Member_64131060';
    private string $pageTitle = 'Trang sự kiện (Thành viên)';

    public function SuKien_Member_64131060(): void { $this->index(); }
    public function TimKiemSuKien_Member_64131060(): void { $this->searchEvents($this->listAction); }

    public function index(): void
    {
        $this->requireRoles(['TV']);
        $this->renderCrudList($this->pageTitle, $this->controllerName, $this->listAction, $this->cfg(), $this->repo()->listEvents(), false);
    }

    public function Details(...$params): void
    {
        $this->requireRoles(['TV']);
        $this->crudDetailsAction($this->controllerName, $this->listAction, $this->cfg(), $this->keys($params), fn($keys) => $this->repo()->findEvent((string)$keys['MaSuKien']), false);
    }

    public function Create(): void { $this->denyUnauthorized(); }
    public function Edit(...$params): void { $this->denyUnauthorized(); }
    public function Delete(...$params): void { $this->denyUnauthorized(); }

    private function searchEvents(string $actionName): void
    {
        $this->requireRoles(['TV']);
        $ma = trim($_GET['maSuKien'] ?? '');
        $ten = trim($_GET['tenSuKien'] ?? '');
        $maCLB = trim($_GET['maCLB'] ?? '');
        $maLoaiSuKien = trim($_GET['maLoaiSuKien'] ?? '');
        $hocKy = trim($_GET['hocKy'] ?? '');
        $namHoc = trim($_GET['namHoc'] ?? '');
        $rows = $this->repo()->searchEvents($ma, $ten, $maCLB, $maLoaiSuKien, $hocKy, $namHoc);
        $this->renderCrudList($this->pageTitle, $this->controllerName, $actionName, $this->cfg(), $rows, false, [
            'search' => 'events',
            'searchValues' => ['maSuKien' => $ma, 'tenSuKien' => $ten, 'maCLB' => $maCLB, 'maLoaiSuKien' => $maLoaiSuKien, 'hocKy' => $hocKy, 'namHoc' => $namHoc],
            'filterOptions' => [
                'clbs' => $this->repo()->options(['table' => 'CLB', 'value' => 'MaCLB', 'label' => 'TenCLB']),
                'types' => $this->repo()->options(['table' => 'LoaiSuKien', 'value' => 'MaLoaiSuKien', 'label' => 'TenLoaiSuKien']),
            ],
            'emptyMessage' => $rows ? '' : 'Không tìm thấy sự kiện phù hợp.',
        ]);
    }

    private function cfg(): array { return $this->resourceCfg('SuKien'); }
    private function keys(array $params): array { return $this->keysFromRequest($this->cfg(), $params); }
}
