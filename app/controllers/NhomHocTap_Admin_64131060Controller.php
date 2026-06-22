<?php
// Chủ nhiệm quản lý toàn bộ nhóm học tập; các action CRUD được ủy quyền cho CrudSupport.
class NhomHocTap_Admin_64131060Controller extends Controller
{
    // Metadata này cho CrudSupport biết route quay về, tiêu đề trang và resource cần xử lý.
    private string $controllerName = 'NhomHocTap_Admin_64131060';
    private string $listAction = 'NhomHocTap_Admin_64131060';
    private string $pageTitle = 'Nhóm học tập';

    // Alias giữ tương thích với route cũ rồi chuyển sang action danh sách chuẩn.
    public function NhomHocTap_Admin_64131060(): void
    {
        $this->index();
    }

    // Lấy toàn bộ nhóm vì TVCN không bị giới hạn scope theo trợ giảng.
    public function index(): void
    {
        $this->requireRoles(['TVCN']);
        $this->renderCrudList(
            $this->pageTitle,
            $this->controllerName,
            $this->listAction,
            $this->cfg(),
            $this->repo()->listStudyGroups(),
            true
        );
    }

    // Callback find nói cho CrudSupport cách tìm nhóm từ khóa MaNhom.
    public function Details(...$params): void
    {
        $this->requireRoles(['TVCN']);
        $this->crudDetailsAction(
            $this->controllerName,
            $this->listAction,
            $this->cfg(),
            $this->keys($params),
            fn($keys) => $this->repo()->findStudyGroup((string)$keys['MaNhom']),
            true
        );
    }

    // CrudSupport điều phối GET hiển thị form và POST validate/lưu dữ liệu.
    public function Create(): void
    {
        $this->requireRoles(['TVCN']);
        $this->crudCreateAction(
            $this->controllerName,
            $this->listAction,
            $this->cfg(),
            fn($data) => $this->repo()->createStudyGroup($data),
            'Thêm nhóm học tập'
        );
    }

    // Hai callback lần lượt đọc bản ghi hiện tại và cập nhật dữ liệu mới.
    public function Edit(...$params): void
    {
        $this->requireRoles(['TVCN']);
        $this->crudEditAction(
            $this->controllerName,
            $this->listAction,
            $this->cfg(),
            $this->keys($params),
            fn($keys) => $this->repo()->findStudyGroup((string)$keys['MaNhom']),
            fn($keys, $data) => $this->repo()->updateStudyGroup((string)$keys['MaNhom'], $data),
            'Cập nhật nhóm học tập'
        );
    }

    // Việc xóa vẫn có thể bị khóa ngoại từ thành viên nhóm hoặc điểm danh từ chối.
    public function Delete(...$params): void
    {
        $this->requireRoles(['TVCN']);
        $this->crudDeleteAction(
            $this->controllerName,
            $this->listAction,
            $this->cfg(),
            $this->keys($params),
            fn($keys) => $this->repo()->findStudyGroup((string)$keys['MaNhom']),
            fn($keys) => $this->repo()->deleteStudyGroup((string)$keys['MaNhom'])
        );
    }

    private function cfg(): array
    {
        // Resource NhomHocTap định nghĩa field, label, kiểu input và khóa chính cho CRUD chung.
        return $this->resourceCfg('NhomHocTap');
    }
    private function keys(array $params): array
    {
        return $this->keysFromRequest($this->cfg(), $params);
    }
}
