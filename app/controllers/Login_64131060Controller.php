<?php
// Xử lý đăng nhập, đăng xuất, đổi mật khẩu và khôi phục mật khẩu qua email.
class Login_64131060Controller extends Controller
{
    // GET hiển thị form; POST xác thực email/mật khẩu và tạo session cho các request tiếp theo.
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
            // MaVaiTro là nguồn dữ liệu mà requireRoles() dùng để chặn hoặc cho phép controller.
            $_SESSION['Email'] = $member['Email'];
            $_SESSION['MaVaiTro'] = $member['MaVaiTro'];
            $_SESSION['MaThanhVien'] = $member['MaThanhVien'];
            $_SESSION['HoTen'] = $member['HoTen'] ?? '';
            // Mỗi vai trò được đưa về đúng trang chủ và menu tương ứng sau khi đăng nhập.
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

    // GET hiển thị form nhập email; POST tạo link khôi phục nếu email thuộc một tài khoản thật.
    public function ForgotPassword_64131060(): void
    {
        $email = trim($_POST['email'] ?? '');
        if ($this->isPost()) {
            if ($email === '') {
                $this->render('auth/forgot_password', [
                    'title' => 'Quên mật khẩu',
                    'error' => 'Vui lòng nhập email.',
                    'email' => $email,
                ]);
                return;
            }
            if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->render('auth/forgot_password', [
                    'title' => 'Quên mật khẩu',
                    'error' => 'Email không đúng định dạng.',
                    'email' => $email,
                ]);
                return;
            }

            try {
                $member = $this->repo()->findMemberByEmail($email);
                if ($member) {
                    $resetUrl = $this->absoluteUrlFor('Login_64131060', 'ResetPassword_64131060', [
                        'token' => $this->createPasswordResetToken($member),
                    ]);
                    $this->sendPasswordResetEmail($member, $resetUrl);
                }
                // Không nói email có tồn tại hay không để tránh lộ danh sách tài khoản.
                $this->render('generic/message', [
                    'title' => 'Kiểm tra email',
                    'message' => 'Nếu email tồn tại, hệ thống đã gửi link khôi phục mật khẩu.',
                    'buttonText' => 'QUAY VỀ ĐĂNG NHẬP',
                    'buttonUrl' => url_for('Login_64131060', 'Login_64131060'),
                ]);
            } catch (Throwable $e) {
                $this->render('auth/forgot_password', [
                    'title' => 'Quên mật khẩu',
                    'error' => $e->getMessage(),
                    'email' => $email,
                ]);
            }
            return;
        }

        $this->render('auth/forgot_password', ['title' => 'Quên mật khẩu', 'email' => $email]);
    }

    // GET kiểm tra token từ email; POST ghi mật khẩu mới nếu token còn hạn và chưa bị vô hiệu.
    public function ResetPassword_64131060(): void
    {
        $token = trim($_POST['token'] ?? $_GET['token'] ?? '');
        $member = $this->memberFromResetToken($token);
        if (!$member) {
            $this->render('generic/message', [
                'title' => 'Link không hợp lệ',
                'message' => 'Liên kết khôi phục mật khẩu không hợp lệ hoặc đã hết hạn.',
                'buttonText' => 'QUAY VỀ ĐĂNG NHẬP',
                'buttonUrl' => url_for('Login_64131060', 'Login_64131060'),
            ]);
            return;
        }

        if ($this->isPost()) {
            $new = trim($_POST['MatKhauMoi'] ?? '');
            $confirm = trim($_POST['NhapLaiMatKhau'] ?? '');
            try {
                if ($new === '' || $confirm === '') {
                    throw new InvalidArgumentException('Vui lòng nhập đầy đủ mật khẩu mới.');
                }
                if (strlen($new) < 6) {
                    throw new InvalidArgumentException('Mật khẩu mới phải có ít nhất 6 ký tự.');
                }
                if ($new !== $confirm) {
                    throw new InvalidArgumentException('Mật khẩu nhập lại không khớp.');
                }
                if ($this->repo()->passwordMatches($new, (string)$member['MatKhau'])) {
                    throw new InvalidArgumentException('Mật khẩu mới không được trùng mật khẩu hiện tại.');
                }
                $this->repo()->resetPassword((string)$member['MaThanhVien'], $new);
                $this->render('generic/message', [
                    'title' => 'Đặt lại mật khẩu thành công',
                    'message' => 'Mật khẩu đã được cập nhật. Bạn có thể đăng nhập bằng mật khẩu mới.',
                    'buttonText' => 'ĐĂNG NHẬP',
                    'buttonUrl' => url_for('Login_64131060', 'Login_64131060'),
                ]);
            } catch (Throwable $e) {
                $this->render('auth/reset_password', [
                    'title' => 'Đặt lại mật khẩu',
                    'error' => $e->getMessage(),
                    'token' => $token,
                ]);
            }
            return;
        }

        $this->render('auth/reset_password', ['title' => 'Đặt lại mật khẩu', 'token' => $token]);
    }

    // Xóa toàn bộ dấu vết đăng nhập và tạo session rỗng trước khi quay lại form.
    public function Logout_64131060(): void
    {
        session_unset();
        session_destroy();
        session_start();
        redirect_to('Login_64131060', 'Login_64131060');
    }

    // Chỉ người đã đăng nhập mới đổi được mật khẩu; GET/POST dùng chung một view.
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
                // Controller kiểm tra dữ liệu biểu mẫu; Repository kiểm tra mật khẩu cũ và ghi mật khẩu mới.
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

    private function createPasswordResetToken(array $member): string
    {
        // Token không lưu database: payload public chứa hạn dùng, chữ ký HMAC mới là phần chống sửa token.
        $payload = [
            'id' => (string)$member['MaThanhVien'],
            'email' => (string)$member['Email'],
            'exp' => time() + 30 * 60,
        ];
        $payloadEncoded = $this->base64UrlEncode((string)json_encode($payload, JSON_UNESCAPED_UNICODE));
        $signature = $this->passwordResetSignature($payloadEncoded, $member);
        return $payloadEncoded . '.' . $signature;
    }

    private function memberFromResetToken(string $token): ?array
    {
        // Chỉ cần đổi mật khẩu là chữ ký cũ sai vì secret có chứa MatKhau hiện tại của tài khoản.
        $parts = explode('.', $token, 2);
        if (count($parts) !== 2) {
            return null;
        }
        [$payloadEncoded, $signature] = $parts;
        $payloadJson = $this->base64UrlDecode($payloadEncoded);
        if ($payloadJson === null) {
            return null;
        }
        $payload = json_decode($payloadJson, true);
        if (!is_array($payload) || empty($payload['id']) || empty($payload['email']) || empty($payload['exp'])) {
            return null;
        }
        if ((int)$payload['exp'] < time()) {
            return null;
        }
        $member = $this->repo()->findMember((string)$payload['id']);
        if (!$member || (string)$member['Email'] !== (string)$payload['email']) {
            return null;
        }
        $expectedSignature = $this->passwordResetSignature($payloadEncoded, $member);
        return hash_equals($expectedSignature, $signature) ? $member : null;
    }

    private function passwordResetSignature(string $payloadEncoded, array $member): string
    {
        $secret = hash('sha256', ROOT_PATH . '|' . (string)$member['MaThanhVien'] . '|' . (string)$member['Email'] . '|' . (string)$member['MatKhau']);
        return $this->base64UrlEncode(hash_hmac('sha256', $payloadEncoded, $secret, true));
    }

    private function sendPasswordResetEmail(array $member, string $resetUrl): void
    {
        // Mailer đọc Gmail gửi từ config; controller chỉ dựng nội dung có link reset tuyệt đối.
        $name = h($member['HoTen'] ?? $member['Email'] ?? 'bạn');
        $safeUrl = h($resetUrl);
        $body = '<p>Xin chào ' . $name . ',</p>'
            . '<p>Hệ thống nhận được yêu cầu khôi phục mật khẩu cho tài khoản của bạn.</p>'
            . '<p><a href="' . $safeUrl . '">Bấm vào đây để đặt lại mật khẩu</a></p>'
            . '<p>Link này có hiệu lực trong 30 phút. Nếu bạn không yêu cầu, hãy bỏ qua email này.</p>';
        (new Mailer())->send('', '', (string)$member['Email'], 'Khôi phục mật khẩu', $body);
    }

    private function absoluteUrlFor(string $controller, string $action, array $params = []): string
    {
        $https = $_SERVER['HTTPS'] ?? '';
        $scheme = ($https !== '' && strtolower((string)$https) !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        return $scheme . '://' . $host . str_replace(' ', '%20', url_for($controller, $action, $params));
    }

    private function base64UrlEncode(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }

    private function base64UrlDecode(string $value): ?string
    {
        $padding = strlen($value) % 4;
        if ($padding > 0) {
            $value .= str_repeat('=', 4 - $padding);
        }
        $decoded = base64_decode(strtr($value, '-_', '+/'), true);
        return $decoded === false ? null : $decoded;
    }
}
