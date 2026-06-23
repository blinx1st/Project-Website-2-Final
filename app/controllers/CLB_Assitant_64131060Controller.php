<?php
// Trợ giảng chỉ xem/cập nhật CLB trong phạm vi được phân quyền; website không tạo nhiều CLB.
class CLB_Assitant_64131060Controller extends Controller
{
    // Metadata này cho CrudSupport biết route quay về, tiêu đề trang và resource cần xử lý.
    private string $controllerName = 'CLB_Assitant_64131060';
    private string $listAction = 'CLB_Assitant_64131060';
    private string $pageTitle = 'Câu lạc bộ (Trợ giảng)';

    public function CLB_Assitant_64131060(): void { $this->index(); }

    public function index(): void
    {
        $this->requireRoles(['TVTG']);
        $this->renderCrudList($this->pageTitle, $this->controllerName, $this->listAction, $this->cfg(), $this->repo()->listClubs($this->currentMemberId()), false);
    }

    public function Details(...$params): void
    {
        $this->requireRoles(['TVTG']);
        $this->crudDetailsAction($this->controllerName, $this->listAction, $this->cfg(), $this->keys($params), fn($keys) => $this->repo()->findClub((string)$keys['MaCLB']), true, fn($row) => $this->guardClubScope((string)$row['MaCLB']));
    }

    public function Create(): void
    {
        $this->requireRoles(['TVTG']);
        $this->singleClubOnlyMessage();
    }

    public function Edit(...$params): void
    {
        $this->requireRoles(['TVTG']);
        $this->crudEditAction($this->controllerName, $this->listAction, $this->cfg(), $this->keys($params), fn($keys) => $this->repo()->findClub((string)$keys['MaCLB']), fn($keys, $data) => $this->repo()->updateClub((string)$keys['MaCLB'], $data), 'Cập nhật câu lạc bộ', fn($row) => $this->guardClubScope((string)$row['MaCLB']), fn($data) => $this->guardClubWrite($data));
    }

    public function Delete(...$params): void
    {
        $this->requireRoles(['TVTG']);
        $this->singleClubOnlyMessage();
    }

    private function guardClubScope(string $maCLB): void
    {
        if (!$this->repo()->canManageClub($maCLB, $this->currentMemberId())) {
            $this->denyUnauthorized();
        }
    }

    private function guardClubWrite(array $data): void
    {
        $memberId = $this->currentMemberId();
        if ((string)($data['ChuNhiem'] ?? '') === $memberId) {
            return;
        }
        if (!empty($data['MaCLB']) && $this->repo()->canManageClub((string)$data['MaCLB'], $memberId)) {
            return;
        }
        $this->denyUnauthorized();
    }

    private function singleClubOnlyMessage(): void
    {
        $this->render('generic/message', [
            'title' => 'Website chỉ quản lý một câu lạc bộ',
            'message' => 'Trợ giảng chỉ xem và cập nhật thông tin CLB trong phạm vi được phân quyền. Không tạo hoặc xóa thêm câu lạc bộ.',
            'buttonText' => 'QUAY VỀ CLB',
            'buttonUrl' => url_for($this->controllerName, $this->listAction),
        ]);
    }

    private function cfg(): array { return $this->resourceCfg('CLB'); }
    private function keys(array $params): array { return $this->keysFromRequest($this->cfg(), $params); }
}
