<?php
class BaoCao_Admin_64131060Controller extends Controller
{
    private string $controllerName = 'BaoCao_Admin_64131060';
    private string $listAction = 'BaoCao_Admin_64131060';
    private string $pageTitle = 'Báo cáo';

    public function BaoCao_Admin_64131060(): void { $this->index(); }

    public function index(): void
    {
        $this->requireRoles(['TVCN']);
        $this->renderCrudList($this->pageTitle, $this->controllerName, $this->listAction, $this->cfg(), $this->repo()->listReports(), true);
    }

    public function Details(...$params): void
    {
        $this->requireRoles(['TVCN']);
        $this->crudDetailsAction($this->controllerName, $this->listAction, $this->cfg(), $this->keys($params), fn($keys) => $this->repo()->findReport($keys['MaBaoCao']), true);
    }

    public function Create(): void
    {
        $this->requireRoles(['TVCN']);
        $this->crudCreateAction($this->controllerName, $this->listAction, $this->cfg(), fn($data) => $this->repo()->createReport($data), 'Thêm báo cáo');
    }

    public function Edit(...$params): void
    {
        $this->requireRoles(['TVCN']);
        $this->crudEditAction($this->controllerName, $this->listAction, $this->cfg(), $this->keys($params), fn($keys) => $this->repo()->findReport($keys['MaBaoCao']), fn($keys, $data) => $this->repo()->updateReport($keys['MaBaoCao'], $data), 'Cập nhật báo cáo');
    }

    public function Delete(...$params): void
    {
        $this->requireRoles(['TVCN']);
        $this->crudDeleteAction($this->controllerName, $this->listAction, $this->cfg(), $this->keys($params), fn($keys) => $this->repo()->findReport($keys['MaBaoCao']), fn($keys) => $this->repo()->deleteReport($keys['MaBaoCao']));
    }

    public function ThongKe(): void
    {
        $this->requireRoles(['TVCN']);
        $hocKy = trim($_GET['HocKy'] ?? '') ?: null;
        $namHoc = trim($_GET['NamHoc'] ?? '') ?: null;
        $maCLB = trim($_GET['MaCLB'] ?? '') ?: null;
        try {
            $this->repo()->syncTrainingPointsFromRules();
        } catch (Throwable $e) {
            $this->render('generic/message', [
                'title' => 'Không thể thống kê điểm',
                'message' => $e->getMessage(),
                'buttonText' => 'QUAY VỀ QUY TẮC ĐIỂM',
                'buttonUrl' => url_for('QuyTacDiemRenLuyen_Admin_64131060', 'QuyTacDiemRenLuyen_Admin_64131060'),
            ]);
            return;
        }
        $this->render('baocao/dashboard', [
            'title' => 'Thống kê tổng hợp',
            'stats' => $this->repo()->dashboardStats($hocKy, $namHoc, $maCLB),
            'filters' => ['HocKy' => $hocKy, 'NamHoc' => $namHoc, 'MaCLB' => $maCLB],
            'clbs' => $this->repo()->options(['table' => 'CLB', 'value' => 'MaCLB', 'label' => 'TenCLB']),
        ]);
    }

    private function cfg(): array { return $this->resourceCfg('BaoCao'); }
    private function keys(array $params): array { return $this->keysFromRequest($this->cfg(), $params); }
}
