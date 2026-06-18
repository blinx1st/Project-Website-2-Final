<?php
class Mailer
{
    public function send(string $from, string $password, string $to, string $subject, string $body): void
    {
        $config = require APP_PATH . '/config/mail.php';
        $from = trim($from) !== '' ? trim($from) : trim((string)($config['from'] ?? ''));
        $password = trim($password) !== '' ? trim($password) : trim((string)($config['password'] ?? ''));
        $to = trim($to);
        if ($from === '' || $password === '') {
            throw new RuntimeException('Chưa cấu hình email gửi hoặc App Password Gmail.');
        }
        if ($to === '') {
            throw new RuntimeException('Vui lòng nhập email nhận.');
        }
        $socket = stream_socket_client('tcp://' . $config['host'] . ':' . (int)$config['port'], $errno, $errstr, (int)$config['timeout']);
        if (!$socket) {
            throw new RuntimeException('Không thể kết nối SMTP: ' . $errstr);
        }
        $this->expect($socket, 220);
        $this->command($socket, 'EHLO localhost', 250);
        $this->command($socket, 'STARTTLS', 220);
        if (!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
            throw new RuntimeException('Không thể bật TLS cho SMTP.');
        }
        $this->command($socket, 'EHLO localhost', 250);
        $this->command($socket, 'AUTH LOGIN', 334);
        $this->command($socket, base64_encode($from), 334);
        $this->command($socket, base64_encode($password), 235);
        $this->command($socket, 'MAIL FROM:<' . $from . '>', 250);
        $this->command($socket, 'RCPT TO:<' . $to . '>', [250, 251]);
        $this->command($socket, 'DATA', 354);
        $headers = [
            'From: ' . $from,
            'To: ' . $to,
            'Subject: ' . $subject,
            'MIME-Version: 1.0',
            'Content-Type: text/html; charset=UTF-8',
        ];
        fwrite($socket, implode("\r\n", $headers) . "\r\n\r\n" . $body . "\r\n.\r\n");
        $this->expect($socket, 250);
        $this->command($socket, 'QUIT', 221);
        fclose($socket);
    }

    private function command($socket, string $command, $expected): string
    {
        fwrite($socket, $command . "\r\n");
        return $this->expect($socket, $expected);
    }

    private function expect($socket, $expected): string
    {
        $response = '';
        while (($line = fgets($socket, 515)) !== false) {
            $response .= $line;
            if (isset($line[3]) && $line[3] === ' ') {
                break;
            }
        }
        $code = (int)substr($response, 0, 3);
        $expectedCodes = is_array($expected) ? $expected : [$expected];
        if (!in_array($code, $expectedCodes, true)) {
            throw new RuntimeException('SMTP lỗi: ' . trim($response));
        }
        return $response;
    }
}
