<?php
class LoaiSuKien_Admin_64131060Controller extends Controller
{
    private string $controllerName = 'LoaiSuKien_Admin_64131060';
    private string $listAction = 'LoaiSuKien_Admin_64131060';
    private string $pageTitle = 'Loại sự kiện';

    public function LoaiSuKien_Admin_64131060(): void
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
            'rows' => $this->repo()->listEventTypes(),
            'canWrite' => true,
        ]);
    }

    public function Details(...$params): void
    {
        $this->requireRoles(['TVCN']);
        $id = $this->idFromRequest($params);
        $row = $this->repo()->findEventType($id);
        if (!$row) {
            $this->notFound();
            return;
        }
        $this->render('generic/details', [
            'title' => 'Thông tin chi tiết loại sự kiện',
            'controller' => $this->controllerName,
            'listAction' => $this->listAction,
            'cfg' => $this->cfg(),
            'row' => $row,
            'keys' => ['MaLoaiSuKien' => $row['MaLoaiSuKien']],
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
                $this->repo()->createEventType($row);
                redirect_to($this->controllerName, $this->listAction);
            } catch (Throwable $e) {
                $this->renderForm($row, 'Create', 'Thêm loại sự kiện', $e->getMessage());
            }
            return;
        }
        $this->renderForm([], 'Create', 'Thêm loại sự kiện');
    }

    public function Edit(...$params): void
    {
        $this->requireRoles(['TVCN']);
        $cfg = $this->cfg();
        $id = $this->idFromRequest($params);
        $row = $this->repo()->findEventType($id);
        if (!$row) {
            $this->notFound();
            return;
        }
        if ($this->isPost()) {
            $data = $this->collectData();
            try {
                Validator::validateResource($cfg, array_merge($data, ['MaLoaiSuKien' => $id]));
                $this->repo()->updateEventType($id, $data);
                redirect_to($this->controllerName, $this->listAction);
            } catch (Throwable $e) {
                $this->renderForm(array_merge($row, $data), 'Edit', 'Cập nhật loại sự kiện', $e->getMessage(), ['MaLoaiSuKien' => $id]);
            }
            return;
        }
        $this->renderForm($row, 'Edit', 'Cập nhật loại sự kiện', '', ['MaLoaiSuKien' => $id]);
    }

    public function Delete(...$params): void
    {
        $this->requireRoles(['TVCN']);
        $id = $this->idFromRequest($params);
        $row = $this->repo()->findEventType($id);
        if (!$row) {
            $this->notFound();
            return;
        }
        if ($this->isPost()) {
            try {
                $this->repo()->deleteEventType($id);
                redirect_to($this->controllerName, $this->listAction);
            } catch (Throwable $e) {
                $this->renderDelete($row, ['MaLoaiSuKien' => $id], 'Không thể xóa vì dữ liệu đang được sử dụng ở bảng khác. ' . $e->getMessage());
            }
            return;
        }
        $this->renderDelete($row, ['MaLoaiSuKien' => $id]);
    }

    private function cfg(): array
    {
        return [
            'table' => 'LoaiSuKien',
            'pk' => ['MaLoaiSuKien'],
            'title' => 'Loại sự kiện',
            'fields' => [
                'MaLoaiSuKien' => ['label' => 'Mã loại', 'type' => 'text', 'required' => true, 'max_length' => 50],
                'TenLoaiSuKien' => ['label' => 'Tên loại sự kiện', 'type' => 'text', 'required' => true, 'max_length' => 100],
                'MoTa' => ['label' => 'Mô tả', 'type' => 'textarea'],
            ],
            'list' => ['MaLoaiSuKien' => 'Mã loại', 'TenLoaiSuKien' => 'Tên loại', 'MoTa' => 'Mô tả'],
        ];
    }

    private function collectData(): array
    {
        return [
            'MaLoaiSuKien' => trim($_POST['MaLoaiSuKien'] ?? ''),
            'TenLoaiSuKien' => trim($_POST['TenLoaiSuKien'] ?? ''),
            'MoTa' => trim($_POST['MoTa'] ?? ''),
        ];
    }

    private function idFromRequest(array $params): string
    {
        return (string)($_POST['MaLoaiSuKien'] ?? $_GET['MaLoaiSuKien'] ?? $params[0] ?? $_GET['id'] ?? '');
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
            'relations' => [],
            'controller' => $this->controllerName,
            'listAction' => $this->listAction,
            'canWrite' => true,
        ]);
    }

    private function renderDelete(array $row, array $keys, string $error = ''): void
    {
        $this->render('generic/delete', [
            'title' => 'Xóa loại sự kiện',
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
