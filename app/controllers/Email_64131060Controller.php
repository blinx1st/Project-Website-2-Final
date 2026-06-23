<?php
// Cung cấp form gửi email theo vai trò và chuyển kết quả gửi sang trang thông báo chung.
class Email_64131060Controller extends Controller
{
    public function SendMail_Asstant_64131060(): void { $this->requireRoles(['TVTG']); $this->send('Gửi email (Trợ giảng)', 'MailAlert1_64131060'); }
    public function SendMail_Admin_64131060(): void { $this->requireRoles(['TVCN']); $this->send('Gửi email (Chủ nhiệm)', 'MailAlert2_64131060'); }
    public function SendMail_Member_64131060(): void { $this->requireRoles(['TV']); $this->send('Gửi email (Thành viên)', 'MailAlert3_64131060'); }
    public function MailAlert1_64131060(): void { $this->alert(); }
    public function MailAlert2_64131060(): void { $this->alert(); }
    public function MailAlert3_64131060(): void { $this->alert(); }

    private function send(string $title, string $successAction): void
    {
        $mailFrom = $this->mailFrom();
        $recipients = $this->repo()->emailRecipients();
        $allowedEmails = array_map('strval', array_column($recipients, 'Email'));
        if ($this->isPost()) {
            $selectedTo = trim($_POST['To'] ?? '');
            $subject = trim($_POST['Subject'] ?? '');
            $body = trim($_POST['Body'] ?? '');
            try {
                if ($selectedTo === '') {
                    throw new InvalidArgumentException('Vui lòng chọn email nhận.');
                }
                if (!in_array($selectedTo, $allowedEmails, true)) {
                    throw new InvalidArgumentException('Email nhận không nằm trong danh sách thành viên câu lạc bộ.');
                }
                (new Mailer())->send('', '', $selectedTo, $subject, $body);
                redirect_to('Email_64131060', $successAction);
            } catch (Throwable $e) {
                $this->render('email/send', [
                    'title' => $title,
                    'error' => $e->getMessage(),
                    'mailFrom' => $mailFrom,
                    'recipients' => $recipients,
                    'selectedTo' => $selectedTo,
                    'subject' => $subject,
                    'body' => $body,
                ]);
            }
            return;
        }
        $this->render('email/send', ['title' => $title, 'mailFrom' => $mailFrom, 'recipients' => $recipients]);
    }

    private function mailFrom(): string
    {
        $config = require APP_PATH . '/config/mail.php';
        return (string)($config['from'] ?? '');
    }

    private function alert(): void
    {
        $this->requireLogin();
        $home = current_role() === 'TVCN' ? 'AdminPage_64131060' : (current_role() === 'TVTG' ? 'AssistantPage_64131060' : 'MemberPage_64131060');
        $this->render('generic/message', ['title' => 'Gửi email thành công', 'message' => 'Email đã được gửi.', 'buttonText' => 'QUAY VỀ', 'buttonUrl' => url_for('TrangChu_64131060', $home)]);
    }
}
