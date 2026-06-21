<?php
class Login_64131060Controller extends Controller
{
    public function Login_64131060(): void
    {
        if ($this->isPost()) {
            $email = trim($_POST['email'] ?? '');
            $password = trim($_POST['matKhau'] ?? '');
            $member = $this->repo()->login($email, $password);
            if (!$member) {
                $this->render('auth/login', ['title' => 'Đăng nhập', 'error' => 'Email hoặc mật khẩu không đúng.']);
                return;
            }
            $_SESSION['Email'] = $member['Email'];
            $_SESSION['MaVaiTro'] = $member['MaVaiTro'];
            $_SESSION['MaThanhVien'] = $member['MaThanhVien'];
            $_SESSION['HoTen'] = $member['HoTen'] ?? '';
            if ($member['MaVaiTro'] === 'TVCN') {
                redirect_to('TrangChu_64131060', 'AdminPage_64131060');
            }
            if ($member['MaVaiTro'] === 'TVTG') {
                redirect_to('TrangChu_64131060', 'AssistantPage_64131060');
            }
            if ($member['MaVaiTro'] === 'TV') {
                redirect_to('TrangChu_64131060', 'MemberPage_64131060');
            }
            $this->render('auth/login', ['title' => 'Đăng nhập', 'error' => 'Vai trò không hợp lệ.']);
            return;
        }
        $this->render('auth/login', ['title' => 'Đăng nhập']);
    }

    public function Logout_64131060(): void
    {
        session_unset();
        session_destroy();
        session_start();
        redirect_to('Login_64131060', 'Login_64131060');
    }

    public function DoiMatKhau_64131060(): void
    {
        $this->requireLogin();
        $error = '';
        $message = '';
        if ($this->isPost()) {
            $old = trim($_POST['MatKhauCu'] ?? '');
            $new = trim($_POST['MatKhauMoi'] ?? '');
            $confirm = trim($_POST['NhapLaiMatKhau'] ?? '');
            try {
                if ($old === '' || $new === '' || $confirm === '') {
                    throw new InvalidArgumentException('Vui lòng nhập đầy đủ thông tin mật khẩu.');
                }
                if (strlen($new) < 6) {
                    throw new InvalidArgumentException('Mật khẩu mới phải có ít nhất 6 ký tự.');
                }
                if ($new !== $confirm) {
                    throw new InvalidArgumentException('Mật khẩu nhập lại không khớp.');
                }
                $this->repo()->updatePassword((string)current_member_id(), $old, $new);
                $message = 'Đổi mật khẩu thành công.';
            } catch (Throwable $e) {
                $error = $e->getMessage();
            }
        }
        $this->render('auth/change_password', ['title' => 'Đổi mật khẩu', 'error' => $error, 'message' => $message]);
    }
}
