<?php
class SuKien_Assitant_64131060Controller extends Controller
{
    private string $controllerName = 'SuKien_Assitant_64131060';
    private string $listAction = 'TimKiemSuKien_Assitant_64131060';
    private string $pageTitle = 'Trang sự kiện (Trợ giảng)';

    public function SuKien_Assitant_64131060(): void { $this->index(); }
    public function TimKiemSuKien_Assitant_64131060(): void { $this->searchEvents($this->listAction); }

    public function index(): void
    {
        $this->requireRoles(['TVTG']);
        $this->renderCrudList($this->pageTitle, $this->controllerName, $this->listAction, $this->cfg(), $this->repo()->listEvents($this->currentMemberId()), true);
    }

    public function Details(...$params): void
    {
        $this->requireRoles(['TVTG']);
        $this->crudDetailsAction($this->controllerName, $this->listAction, $this->cfg(), $this->keys($params), fn($keys) => $this->repo()->findEvent((string)$keys['MaSuKien']), true, fn($row) => $this->guardEventScope((string)$row['MaSuKien']));
    }

    public function Create(): void
    {
        $this->requireRoles(['TVTG']);
        $this->crudCreateAction($this->controllerName, $this->listAction, $this->cfg(), fn($data) => $this->repo()->createEvent($data), 'Thêm sự kiện', null, fn($data) => $this->guardClubScope((string)($data['MaCLB'] ?? '')));
    }

    public function Edit(...$params): void
    {
        $this->requireRoles(['TVTG']);
        $this->crudEditAction($this->controllerName, $this->listAction, $this->cfg(), $this->keys($params), fn($keys) => $this->repo()->findEvent((string)$keys['MaSuKien']), fn($keys, $data) => $this->repo()->updateEvent((string)$keys['MaSuKien'], $data), 'Cập nhật sự kiện', fn($row) => $this->guardEventScope((string)$row['MaSuKien']), fn($data) => $this->guardClubScope((string)($data['MaCLB'] ?? '')));
    }

    public function Delete(...$params): void
    {
        $this->requireRoles(['TVTG']);
        $this->crudDeleteAction($this->controllerName, $this->listAction, $this->cfg(), $this->keys($params), fn($keys) => $this->repo()->findEvent((string)$keys['MaSuKien']), fn($keys) => $this->repo()->deleteEvent((string)$keys['MaSuKien']), true, fn($row) => $this->guardEventScope((string)$row['MaSuKien']));
    }

    public function QRCode(): void
    {
        $this->requireRoles(['TVTG']);
        $maSuKien = trim($_GET['MaSuKien'] ?? '');
        $this->guardEventScope($maSuKien);
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
        $this->requireRoles(['TVTG']);
        $ma = trim($_GET['maSuKien'] ?? '');
        $ten = trim($_GET['tenSuKien'] ?? '');
        $maCLB = trim($_GET['maCLB'] ?? '');
        $maLoaiSuKien = trim($_GET['maLoaiSuKien'] ?? '');
        $hocKy = trim($_GET['hocKy'] ?? '');
        $namHoc = trim($_GET['namHoc'] ?? '');
        $rows = $this->repo()->searchEvents($ma, $ten, $maCLB, $maLoaiSuKien, $hocKy, $namHoc, $this->currentMemberId());
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

    private function guardClubScope(string $maCLB): void
    {
        if (!$maCLB || !$this->repo()->canManageClub($maCLB, $this->currentMemberId())) {
            $this->denyUnauthorized();
        }
    }

    private function guardEventScope(string $maSuKien): void
    {
        if (!$maSuKien || !$this->repo()->canManageEvent($maSuKien, $this->currentMemberId())) {
            $this->denyUnauthorized();
        }
    }

    private function cfg(): array { return $this->resourceCfg('SuKien'); }
    private function keys(array $params): array { return $this->keysFromRequest($this->cfg(), $params); }
}
