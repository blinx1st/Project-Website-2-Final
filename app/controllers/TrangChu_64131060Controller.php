<?php
class TrangChu_64131060Controller extends Controller
{
    public function TrangChu_64131060(): void
    {
        $this->home('Trang chủ', 'ĐĂNG NHẬP', url_for('Login_64131060', 'Login_64131060'), 'GioiThieu_64131060');
    }

    public function AdminPage_64131060(): void
    {
        $this->requireRoles(['TVCN']);
        $this->home('Trang chủ Chủ nhiệm', 'QUẢN LÝ THÀNH VIÊN', url_for('ThanhVien_Admin_64131060', 'TimKiemTV_Admin_64131060'), 'GioiThieu_AdminPage_64131060');
    }

    public function AssistantPage_64131060(): void
    {
        $this->requireRoles(['TVTG']);
        $this->home('Trang chủ Trợ giảng', 'QUẢN LÝ SỰ KIỆN', url_for('SuKien_Assitant_64131060', 'TimKiemSuKien_Assitant_64131060'), 'GioiThieu_AssitantPage_64131060');
    }

    public function MemberPage_64131060(): void
    {
        $this->requireRoles(['TV']);
        $this->home('Trang chủ Thành viên', 'XEM SỰ KIỆN', url_for('SuKien_Member_64131060', 'TimKiemSuKien_Member_64131060'), 'GioiThieu_MemberPage_64131060');
    }

    public function GioiThieu_64131060(): void { $this->about('Giới thiệu về câu lạc bộ', 'TrangChu_64131060'); }
    public function GioiThieu_AdminPage_64131060(): void { $this->requireRoles(['TVCN']); $this->about('Trang giới thiệu (Chủ nhiệm)', 'AdminPage_64131060'); }
    public function GioiThieu_AssitantPage_64131060(): void { $this->requireRoles(['TVTG']); $this->about('Trang giới thiệu (Trợ giảng)', 'AssistantPage_64131060'); }
    public function GioiThieu_MemberPage_64131060(): void { $this->requireRoles(['TV']); $this->about('Trang giới thiệu (Thành viên)', 'MemberPage_64131060'); }

    private function home(string $title, string $primaryText, string $primaryUrl, string $aboutAction): void
    {
        $this->render('pages/home', [
            'title' => $title,
            'primaryText' => $primaryText,
            'primaryUrl' => $primaryUrl,
            'aboutAction' => $aboutAction,
            'cards' => [
                ['title' => 'Tin tức', 'desc' => 'Xem và quản lý bài đăng CLB.', 'url' => url_for(current_role() === 'TVCN' ? 'BaiDang_Admin_64131060' : (current_role() === 'TVTG' ? 'BaiDang_Assitant_64131060' : (current_role() === 'TV' ? 'BaiDang_Member_64131060' : 'BaiDang_64131060')), current_role() === 'TVCN' ? 'BaiDang_Admin_64131060' : (current_role() === 'TVTG' ? 'BaiDang_Assitant_64131060' : (current_role() === 'TV' ? 'BaiDang_Member_64131060' : 'BaiDang_64131060')))],
                ['title' => 'Sự kiện', 'desc' => 'Tạo, tìm kiếm và theo dõi sự kiện.', 'url' => $primaryUrl],
                ['title' => 'Điểm rèn luyện', 'desc' => 'Theo dõi điểm hoạt động ngoại khóa theo học kỳ.', 'url' => url_for(current_role() === 'TVCN' ? 'DiemRenLuyen_Admin_64131060' : (current_role() === 'TVTG' ? 'DiemRenLuyen_Assitant_64131060' : 'DiemRenLuyen_Member_64131060'), current_role() === 'TVCN' ? 'DiemRenLuyen_Admin_64131060' : (current_role() === 'TVTG' ? 'DiemRenLuyen_Assitant_64131060' : 'DiemRenLuyen_Member_64131060'))],
                ['title' => 'Chứng nhận', 'desc' => 'Xem chứng nhận tham gia sự kiện đã được cấp.', 'url' => url_for(current_role() === 'TVCN' ? 'ChungNhan_Admin_64131060' : (current_role() === 'TVTG' ? 'ChungNhan_Assitant_64131060' : 'ChungNhan_Member_64131060'), current_role() === 'TVCN' ? 'ChungNhan_Admin_64131060' : (current_role() === 'TVTG' ? 'ChungNhan_Assitant_64131060' : 'ChungNhan_Member_64131060'))],
                ['title' => 'Điểm danh', 'desc' => 'Ghi nhận tham gia nhóm học tập.', 'url' => url_for(current_role() === 'TVCN' ? 'DiemDanh_Admin_64131060' : (current_role() === 'TVTG' ? 'DiemDanh_Assitant_64131060' : 'DiemDanh_Member_64131060'), current_role() ? 'Create' : 'DiemDanh_Member_64131060')],
            ],
        ]);
    }

    private function about(string $title, string $homeAction): void
    {
        $this->render('pages/about', ['title' => $title, 'homeAction' => $homeAction]);
    }
}
