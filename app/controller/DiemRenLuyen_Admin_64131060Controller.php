<?php
class DiemRenLuyen_Admin_64131060Controller extends Controller
{
    private string $controllerName = 'DiemRenLuyen_Admin_64131060';
    private string $listAction = 'DiemRenLuyen_Admin_64131060';
    private string $pageTitle = 'Điểm rèn luyện';

    public function DiemRenLuyen_Admin_64131060(): void { $this->index(); }

    public function index(): void
    {
        $this->requireRoles(['TVCN']);
        $this->renderCrudList($this->pageTitle, $this->controllerName, $this->listAction, $this->cfg(), $this->repo()->listPoints(), false);
    }

    public function Details(...$params): void
    {
        $this->requireRoles(['TVCN']);
        $this->crudDetailsAction($this->controllerName, $this->listAction, $this->cfg(), $this->keys($params), fn($keys) => $this->repo()->findPoint($keys['MaDiem']), false);
    }

    public function Create(): void { $this->requireRoles(['TVCN']); $this->renderGeneratedPointWriteBlocked($this->controllerName, $this->listAction); }
    public function Edit(...$params): void { $this->requireRoles(['TVCN']); $this->renderGeneratedPointWriteBlocked($this->controllerName, $this->listAction); }
    public function Delete(...$params): void { $this->requireRoles(['TVCN']); $this->renderGeneratedPointWriteBlocked($this->controllerName, $this->listAction); }

    public function ExportCsv(): void
    {
        try {
            $this->requireRoles(['TVCN']);
            $hocKy = trim($_GET['HocKy'] ?? '');
            $namHoc = trim($_GET['NamHoc'] ?? '');
            $maCLB = trim($_GET['MaCLB'] ?? '') ?: null;
            Validator::validateResource([
                'table' => 'ExportDiem',
                'fields' => [
                    'HocKy' => ['label' => 'Học kỳ', 'type' => 'select_static', 'required' => true],
                    'NamHoc' => ['label' => 'Năm học', 'type' => 'text', 'required' => true, 'pattern' => '/^\d{4}-\d{4}$/'],
                ],
            ], ['HocKy' => $hocKy, 'NamHoc' => $namHoc]);

            $this->repo()->syncTrainingPointsFromRules();
            $rows = $this->repo()->termPointTotals($hocKy, $namHoc, $maCLB);
        } catch (Throwable $e) {
            $this->render('generic/message', [
                'title' => 'Không thể export điểm',
                'message' => $e->getMessage(),
                'buttonText' => 'QUAY VỀ',
                'buttonUrl' => url_for($this->controllerName, $this->listAction),
            ]);
            return;
        }
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="diem-ren-luyen-' . $hocKy . '-' . $namHoc . '.csv"');
        echo "\xEF\xBB\xBF";
        $out = fopen('php://output', 'w');
        fputcsv($out, ['Mã thành viên', 'Họ tên', 'Email', 'Học kỳ', 'Năm học', 'Tổng điểm', 'Cập nhật lúc']);
        foreach ($rows as $row) {
            fputcsv($out, [$row['MaThanhVien'], $row['HoTen'], $row['Email'], $row['HocKy'], $row['NamHoc'], $row['TongDiem'], $row['CapNhatLuc']]);
        }
        fclose($out);
        exit;
    }

    private function cfg(): array { return $this->resourceCfg('DiemRenLuyen'); }
    private function keys(array $params): array { return $this->keysFromRequest($this->cfg(), $params); }
}
