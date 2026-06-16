<?php
class CheckInSuKien_64131060Controller extends Controller
{
    public function Scan(): void
    {
        $this->requireRoles(['TV']);
        $maSuKien = trim($_GET['MaSuKien'] ?? '');
        $token = trim($_GET['Token'] ?? '');
        $result = null;
        $error = '';
        try {
            if ($maSuKien === '' || $token === '') {
                throw new InvalidArgumentException('Thiếu thông tin QR check-in.');
            }
            $result = $this->repo()->checkInEvent($maSuKien, (string)current_member_id(), $token);
        } catch (Throwable $e) {
            $error = $e->getMessage();
        }
        $event = $maSuKien ? $this->repo()->findEvent($maSuKien) : null;
        $this->render('checkin/scan', [
            'title' => 'Check-in sự kiện',
            'event' => $event,
            'result' => $result,
            'error' => $error,
        ]);
    }
}
