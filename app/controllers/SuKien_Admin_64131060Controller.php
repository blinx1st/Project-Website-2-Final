<?php
class SuKien_Admin_64131060Controller extends Controller
{
    private string $controllerName = 'SuKien_Admin_64131060';
    private string $listAction = 'TimKiemSuKien_Admin_64131060';
    private string $pageTitle = 'Trang sự kiện (Chủ nhiệm)';

    public function SuKien_Admin_64131060(): void { $this->index(); }
    public function TimKiemSuKien_Admin_64131060(): void { $this->searchEvents($this->listAction); }

    public function index(): void
    {
        $this->requireRoles(['TVCN']);
        $this->renderCrudList($this->pageTitle, $this->controllerName, $this->listAction, $this->cfg(), $this->repo()->listEvents(), true);
    }

    public function Details(...$params): void
    {
        $this->requireRoles(['TVCN']);
        $this->crudDetailsAction($this->controllerName, $this->listAction, $this->cfg(), $this->keys($params), fn($keys) => $this->repo()->findEvent((string)$keys['MaSuKien']), true);
    }

    public function Create(): void
    {
        $this->requireRoles(['TVCN']);
        $this->crudCreateAction($this->controllerName, $this->listAction, $this->cfg(), fn($data) => $this->repo()->createEvent($data), 'Thêm sự kiện');
    }

    public function Edit(...$params): void
    {
        $this->requireRoles(['TVCN']);
        $this->crudEditAction($this->controllerName, $this->listAction, $this->cfg(), $this->keys($params), fn($keys) => $this->repo()->findEvent((string)$keys['MaSuKien']), fn($keys, $data) => $this->repo()->updateEvent((string)$keys['MaSuKien'], $data), 'Cập nhật sự kiện');
    }

    public function Delete(...$params): void
    {
        $this->requireRoles(['TVCN']);
        $this->crudDeleteAction($this->controllerName, $this->listAction, $this->cfg(), $this->keys($params), fn($keys) => $this->repo()->findEvent((string)$keys['MaSuKien']), fn($keys) => $this->repo()->deleteEvent((string)$keys['MaSuKien']));
    }

    public function QRCode(): void
    {
        $this->requireRoles(['TVCN']);
        $maSuKien = trim($_GET['MaSuKien'] ?? '');
        $event = $this->repo()->findEvent($maSuKien);
        if (!$event) {
            $this->notFound('Không tìm thấy sự kiện.');
            return;
        }
        $token = $this->repo()->ensureEventToken($maSuKien);
        $scanUrl = url_for('CheckInSuKien_64131060', 'Scan', ['MaSuKien' => $maSuKien, 'Token' => $token]);
        $this->render('sukien/qr', ['title' => 'QR check-in sự kiện', 'event' => $event, 'scanUrl' => $scanUrl, 'backUrl' => url_for($this->controllerName, $this->listAction)]);
    }

    private function searchEvents(string $actionName): void
    {
        $this->requireRoles(['TVCN']);
        $ma = trim($_GET['maSuKien'] ?? '');
        $ten = trim($_GET['tenSuKien'] ?? '');
        $maCLB = trim($_GET['maCLB'] ?? '');
        $maLoaiSuKien = trim($_GET['maLoaiSuKien'] ?? '');
        $hocKy = trim($_GET['hocKy'] ?? '');
        $namHoc = trim($_GET['namHoc'] ?? '');
        $rows = $this->repo()->searchEvents($ma, $ten, $maCLB, $maLoaiSuKien, $hocKy, $namHoc);
        $this->renderCrudList($this->pageTitle, $this->controllerName, $actionName, $this->cfg(), $rows, true, [
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
