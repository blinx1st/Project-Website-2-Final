<?php
// Chủ nhiệm quản lý trang chức năng của một CLB duy nhất, không tạo thêm nhiều CLB.
class CLB_Admin_64131060Controller extends Controller
{
    // Metadata này cho CrudSupport biết route quay về, tiêu đề trang và resource cần xử lý.
    private string $controllerName = 'CLB_Admin_64131060';
    private string $listAction = 'CLB_Admin_64131060';
    private string $pageTitle = 'Câu lạc bộ';

    public function CLB_Admin_64131060(): void { $this->index(); }

    public function index(): void
    {
        $this->requireRoles(['TVCN']);
        $this->render('clb/dashboard', [
            'title' => $this->pageTitle,
            'club' => $this->repo()->primaryClub(),
            'stats' => $this->repo()->singleClubDashboardStats(),
        ]);
    }

    public function Details(...$params): void
    {
        $this->requireRoles(['TVCN']);
        $this->crudDetailsAction($this->controllerName, $this->listAction, $this->cfg(), $this->keys($params), fn($keys) => $this->repo()->findClub((string)$keys['MaCLB']), true);
    }

    public function Create(): void
    {
        $this->requireRoles(['TVCN']);
        $this->singleClubOnlyMessage();
    }

    public function Edit(...$params): void
    {
        $this->requireRoles(['TVCN']);
        $this->crudEditAction($this->controllerName, $this->listAction, $this->cfg(), $this->keys($params), fn($keys) => $this->repo()->findClub((string)$keys['MaCLB']), fn($keys, $data) => $this->repo()->updateClub((string)$keys['MaCLB'], $data), 'Cập nhật câu lạc bộ');
    }

    public function Delete(...$params): void
    {
        $this->requireRoles(['TVCN']);
        $this->singleClubOnlyMessage();
    }

    private function singleClubOnlyMessage(): void
    {
        $this->render('generic/message', [
            'title' => 'Website chỉ quản lý một câu lạc bộ',
            'message' => 'Chức năng CLB hiện dùng để xem thông tin và điều hướng tới Thành viên, Nhóm học tập, Sự kiện. Không tạo hoặc xóa thêm câu lạc bộ.',
            'buttonText' => 'QUAY VỀ CLB',
            'buttonUrl' => url_for($this->controllerName, $this->listAction),
        ]);
    }

    private function cfg(): array { return $this->resourceCfg('CLB'); }
    private function keys(array $params): array { return $this->keysFromRequest($this->cfg(), $params); }
}
