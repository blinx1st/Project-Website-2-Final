<?php
// Thành viên chỉ xem lịch sử điểm danh; mọi URL tạo/sửa/xóa đều bị chặn.
class DiemDanh_Member_64131060Controller extends Controller
{
    // Metadata này cho CrudSupport biết route quay về, tiêu đề trang và resource cần xử lý.
    private string $controllerName = 'DiemDanh_Member_64131060';
    private string $listAction = 'DiemDanh_Member_64131060';
    private string $pageTitle = 'Điểm danh (Thành viên)';

    public function DiemDanh_Member_64131060(): void
    {
        $this->index();
    }

    // Mã thành viên lấy từ session và canWrite=false làm giao diện chỉ đọc.
    public function index(): void
    {
        $this->requireRoles(['TV']);
        $memberId = $this->currentMemberId();
        $this->render('generic/list', [
            'title' => $this->pageTitle,
            'controller' => $this->controllerName,
            'listAction' => $this->listAction,
            'cfg' => $this->cfg(),
            'rows' => $this->repo()->listAttendance($memberId),
            'canWrite' => false,
        ]);
    }

    // Repository nhận thêm memberId để URL chi tiết của người khác trả về không tìm thấy.
    public function Details(...$params): void
    {
        $this->requireRoles(['TV']);
        $memberId = $this->currentMemberId();
        $id = $this->idFromRequest($params);
        $row = $this->repo()->findAttendance($id, $memberId);
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
            'canWrite' => false,
        ]);
    }

    // Chặn tại server là lớp bảo vệ thật; việc ẩn nút trên giao diện chỉ hỗ trợ trải nghiệm.
    public function Create(): void
    {
        $this->requireRoles(['TV']);
        $this->denyUnauthorized();
    }

    // Sửa trực tiếp bằng URL vẫn bị trả 403.
    public function Edit(...$params): void
    {
        $this->requireRoles(['TV']);
        $this->denyUnauthorized();
    }

    // Thành viên không có quyền xóa lịch sử điểm danh.
    public function Delete(...$params): void
    {
        $this->requireRoles(['TV']);
        $this->denyUnauthorized();
    }

    public function Alert_Member_64131060(): void
    {
        $this->requireRoles(['TV']);
        $this->render('generic/message', [
            'title' => 'Điểm danh thành công',
            'message' => 'Thông tin điểm danh của bạn đã được ghi nhận.',
            'buttonText' => 'QUAY VỀ',
            'buttonUrl' => url_for('TrangChu_64131060', 'MemberPage_64131060'),
        ]);
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
        $member = $this->repo()->findMemberByEmail(current_email());
        return [
            'MaNhom' => $this->repo()->options(['table' => 'NhomHocTap', 'value' => 'MaNhom', 'label' => 'TenNhom']),
            'MaThanhVien' => $member ? [['value' => $member['MaThanhVien'], 'label' => $member['HoTen']]] : [],
        ];
    }

    private function collectData(): array
    {
        $date = trim($_POST['NgayDiemDanh'] ?? '');
        return [
            'MaNhom' => trim($_POST['MaNhom'] ?? ''),
            'MaThanhVien' => $this->currentMemberId(),
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
