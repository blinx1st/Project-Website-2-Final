<?php
// Trợ giảng chỉ thấy và quản lý nhóm có cột TroGiang trùng tài khoản hiện tại.
class NhomHocTap_Assitant_64131060Controller extends Controller
{
    // Metadata này cho CrudSupport biết route quay về, tiêu đề trang và resource cần xử lý.
    private string $controllerName = 'NhomHocTap_Assitant_64131060';
    private string $listAction = 'NhomHocTap_Assitant_64131060';
    private string $pageTitle = 'Nhóm học tập (Trợ giảng)';

    public function NhomHocTap_Assitant_64131060(): void { $this->index(); }

    // Danh sách truyền currentMemberId để Repository lọc theo cột TroGiang.
    public function index(): void
    {
        $this->requireRoles(['TVTG']);
        $this->renderCrudList($this->pageTitle, $this->controllerName, $this->listAction, $this->cfg(), $this->repo()->listStudyGroups($this->currentMemberId()), true);
    }

    // Ngoài role chung, guard kiểm tra quyền trên chính nhóm được yêu cầu.
    public function Details(...$params): void
    {
        $this->requireRoles(['TVTG']);
        $this->crudDetailsAction($this->controllerName, $this->listAction, $this->cfg(), $this->keys($params), fn($keys) => $this->repo()->findStudyGroup((string)$keys['MaNhom']), true, fn($row) => $this->guardStudyGroupScope((string)$row['MaNhom']));
    }

    // Khi tạo, TroGiang bị ép bằng tài khoản hiện tại thay vì tin dữ liệu gửi từ trình duyệt.
    public function Create(): void
    {
        $this->requireRoles(['TVTG']);
        $cfg = $this->cfg();
        $assistantId = $this->currentMemberId();
        if ($this->isPost()) {
            $row = $this->collectResourceData($cfg);
            $row['TroGiang'] = $assistantId;
            try {
                Validator::validateResource($cfg, $row);
                $this->repo()->createStudyGroup($row);
                redirect_to($this->controllerName, $this->listAction);
            } catch (Throwable $e) {
                $this->renderCrudForm($this->controllerName, $this->listAction, $cfg, $row, 'Create', 'Thêm nhóm học tập', $e->getMessage(), [], true, $this->assistantRelations());
            }
            return;
        }
        $this->renderCrudForm($this->controllerName, $this->listAction, $cfg, ['TroGiang' => $assistantId], 'Create', 'Thêm nhóm học tập', '', [], true, $this->assistantRelations());
    }

    // Hook trước khi lưu ngăn trợ giảng chuyển nhóm sang cho tài khoản khác bằng request thủ công.
    public function Edit(...$params): void
    {
        $this->requireRoles(['TVTG']);
        $this->crudEditAction($this->controllerName, $this->listAction, $this->cfg(), $this->keys($params), fn($keys) => $this->repo()->findStudyGroup((string)$keys['MaNhom']), fn($keys, $data) => $this->repo()->updateStudyGroup((string)$keys['MaNhom'], $data), 'Cập nhật nhóm học tập', fn($row) => $this->guardStudyGroupScope((string)$row['MaNhom']), fn($data) => $this->guardAssignedAssistant((string)($data['TroGiang'] ?? '')), true, $this->assistantRelations());
    }

    // Guard chạy trước callback xóa để bảo vệ cả trường hợp truy cập URL trực tiếp.
    public function Delete(...$params): void
    {
        $this->requireRoles(['TVTG']);
        $this->crudDeleteAction($this->controllerName, $this->listAction, $this->cfg(), $this->keys($params), fn($keys) => $this->repo()->findStudyGroup((string)$keys['MaNhom']), fn($keys) => $this->repo()->deleteStudyGroup((string)$keys['MaNhom']), true, fn($row) => $this->guardStudyGroupScope((string)$row['MaNhom']));
    }

    private function guardStudyGroupScope(string $maNhom): void
    {
        if (!$maNhom || !$this->repo()->canManageStudyGroup($maNhom, $this->currentMemberId())) {
            $this->denyUnauthorized();
        }
    }

    private function guardAssignedAssistant(string $assistantId): void
    {
        if ($assistantId !== $this->currentMemberId()) {
            $this->denyUnauthorized();
        }
    }

    private function assistantRelations(): array
    {
        // Select trợ giảng chỉ có một lựa chọn là chính người đang đăng nhập.
        return ['TroGiang' => [['value' => $this->currentMemberId(), 'label' => current_user_name()]]];
    }

    private function cfg(): array { return $this->resourceCfg('NhomHocTap'); }
    private function keys(array $params): array { return $this->keysFromRequest($this->cfg(), $params); }
}
