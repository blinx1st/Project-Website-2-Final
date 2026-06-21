<?php
class DiemDanh_Admin_64131060Controller extends Controller
{
    private string $controllerName = 'DiemDanh_Admin_64131060';
    private string $listAction = 'DiemDanh_Admin_64131060';
    private string $pageTitle = 'Điểm danh (Chủ nhiệm)';

    public function DiemDanh_Admin_64131060(): void
    {
        $this->index();
    }

    public function index(): void
    {
        $this->requireRoles(['TVCN']);
        $this->render('generic/list', [
            'title' => $this->pageTitle,
            'controller' => $this->controllerName,
            'listAction' => $this->listAction,
            'cfg' => $this->cfg(),
            'rows' => $this->repo()->listAttendance(),
            'canWrite' => true,
        ]);
    }

    public function Details(...$params): void
    {
        $this->requireRoles(['TVCN']);
        $id = $this->idFromRequest($params);
        $row = $this->repo()->findAttendance($id);
        if (!$row) {
            $this->notFound();
            return;
        }
        $this->render('generic/details', [
            'title' => 'Thông tin chi tiết điểm danh',
            'controller' => $this->controllerName,
            'listAction' => $this->listAction,
            'cfg' => $this->cfg(),
            'row' => $row,
            'keys' => ['MaDiemDanh' => $row['MaDiemDanh']],
            'canWrite' => true,
        ]);
    }

    public function Create(): void
    {
        $this->requireRoles(['TVCN']);
        $cfg = $this->cfg();
        if ($this->isPost()) {
            $row = $this->collectData();
            try {
                Validator::validateResource($cfg, $row);
                $this->repo()->createAttendance($row);
                redirect_to($this->controllerName, 'Create');
            } catch (Throwable $e) {
                $this->renderForm($row, 'Create', 'Thêm điểm danh', $e->getMessage());
            }
            return;
        }
        $this->renderForm([], 'Create', 'Thêm điểm danh');
    }

    public function Edit(...$params): void
    {
        $this->requireRoles(['TVCN']);
        $cfg = $this->cfg();
        $id = $this->idFromRequest($params);
        $row = $this->repo()->findAttendance($id);
        if (!$row) {
            $this->notFound();
            return;
        }
        if ($this->isPost()) {
            $data = $this->collectData();
            try {
                Validator::validateResource($cfg, $data);
                $this->repo()->updateAttendance($id, $data);
                redirect_to($this->controllerName, $this->listAction);
            } catch (Throwable $e) {
                $this->renderForm(array_merge($row, $data), 'Edit', 'Cập nhật điểm danh', $e->getMessage(), ['MaDiemDanh' => $id]);
            }
            return;
        }
        $this->renderForm($row, 'Edit', 'Cập nhật điểm danh', '', ['MaDiemDanh' => $id]);
    }

    public function Delete(...$params): void
    {
        $this->requireRoles(['TVCN']);
        $id = $this->idFromRequest($params);
        $row = $this->repo()->findAttendance($id);
        if (!$row) {
            $this->notFound();
            return;
        }
        if ($this->isPost()) {
            try {
                $this->repo()->deleteAttendance($id);
                redirect_to($this->controllerName, $this->listAction);
            } catch (Throwable $e) {
                $this->renderDelete($row, ['MaDiemDanh' => $id], 'Không thể xóa vì dữ liệu đang được sử dụng ở bảng khác. ' . $e->getMessage());
            }
            return;
        }
        $this->renderDelete($row, ['MaDiemDanh' => $id]);
    }

    private function cfg(): array
    {
        return [
            'table' => 'DiemDanh',
            'pk' => ['MaDiemDanh'],
            'auto' => ['MaDiemDanh'],
            'title' => 'Điểm danh',
            'fields' => [
                'MaDiemDanh' => ['label' => 'Mã điểm danh', 'type' => 'number', 'readonly' => true],
                'MaNhom' => ['label' => 'Nhóm', 'type' => 'select', 'relation' => ['table' => 'NhomHocTap', 'value' => 'MaNhom', 'label' => 'TenNhom'], 'required' => true],
                'MaThanhVien' => ['label' => 'Thành viên', 'type' => 'select', 'relation' => ['table' => 'ThanhVien', 'value' => 'MaThanhVien', 'label' => 'HoTen'], 'required' => true],
                'NgayDiemDanh' => ['label' => 'Ngày điểm danh', 'type' => 'date', 'required' => true],
                'TrangThai' => ['label' => 'Trạng thái', 'type' => 'select_static', 'options' => ['Có mặt' => 'Có mặt', 'Vắng' => 'Vắng', 'Muộn' => 'Muộn'], 'required' => true],
                'GhiChu' => ['label' => 'Ghi chú', 'type' => 'textarea'],
            ],
            'list' => ['MaDiemDanh' => 'Mã', 'TenNhom' => 'Nhóm', 'HoTen' => 'Thành viên', 'NgayDiemDanh' => 'Ngày', 'TrangThai' => 'Trạng thái', 'GhiChu' => 'Ghi chú'],
        ];
    }

    private function relations(): array
    {
        return [
            'MaNhom' => $this->repo()->options(['table' => 'NhomHocTap', 'value' => 'MaNhom', 'label' => 'TenNhom']),
            'MaThanhVien' => $this->repo()->options(['table' => 'ThanhVien', 'value' => 'MaThanhVien', 'label' => 'HoTen']),
        ];
    }

    private function collectData(): array
    {
        $date = trim($_POST['NgayDiemDanh'] ?? '');
        return [
            'MaNhom' => trim($_POST['MaNhom'] ?? ''),
            'MaThanhVien' => trim($_POST['MaThanhVien'] ?? ''),
            'NgayDiemDanh' => $date === '' ? date('Y-m-d') : $date,
            'TrangThai' => trim($_POST['TrangThai'] ?? ''),
            'GhiChu' => trim($_POST['GhiChu'] ?? ''),
        ];
    }

    private function idFromRequest(array $params): string
    {
        return (string)($_POST['MaDiemDanh'] ?? $_GET['MaDiemDanh'] ?? $params[0] ?? $_GET['id'] ?? '');
    }

    private function renderForm(array $row, string $action, string $title, string $error = '', array $keys = []): void
    {
        $this->render('generic/form', [
            'cfg' => $this->cfg(),
            'row' => $row,
            'action' => $action,
            'title' => $title,
            'error' => $error,
            'keys' => $keys,
            'relations' => $this->relations(),
            'controller' => $this->controllerName,
            'listAction' => $this->listAction,
            'canWrite' => true,
        ]);
    }

    private function renderDelete(array $row, array $keys, string $error = ''): void
    {
        $this->render('generic/delete', [
            'title' => 'Xóa điểm danh',
            'controller' => $this->controllerName,
            'listAction' => $this->listAction,
            'cfg' => $this->cfg(),
            'row' => $row,
            'keys' => $keys,
            'error' => $error,
            'canWrite' => true,
        ]);
    }
}
