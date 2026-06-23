<?php
// Nhóm truy vấn tài khoản thành viên: danh sách, tìm kiếm, CRUD, đăng nhập và đổi mật khẩu.
trait MemberRepositoryTrait
{
    public function listMembers(?string $maThanhVien = null): array
    {
        // Khi có mã thành viên, WHERE giúp trang Thành viên chỉ lấy đúng hồ sơ của chính họ.
        $sql = 'SELECT ThanhVien.*, VaiTro.TenVaiTro
            FROM ThanhVien
            LEFT JOIN VaiTro ON VaiTro.MaVaiTro = ThanhVien.MaVaiTro';
        $params = [];
        if ($maThanhVien !== null && $maThanhVien !== '') {
            $sql .= ' WHERE ThanhVien.MaThanhVien = :maThanhVien';
            $params['maThanhVien'] = $maThanhVien;
        }
        $sql .= ' ORDER BY ThanhVien.MaThanhVien ASC';
        return $this->fetchAll($sql, $params);
    }

    public function searchMembers(string $maThanhVien, string $hoTen): array
    {
        // Điều kiện rỗng hoạt động như wildcard để cùng một query hỗ trợ tìm theo mã, tên hoặc cả hai.
        return $this->fetchAll("SELECT ThanhVien.*, VaiTro.TenVaiTro
            FROM ThanhVien
            LEFT JOIN VaiTro ON VaiTro.MaVaiTro = ThanhVien.MaVaiTro
            WHERE (:ma = '' OR ThanhVien.MaThanhVien LIKE :maLike)
                AND (:ten = '' OR ThanhVien.HoTen LIKE :tenLike)
            ORDER BY ThanhVien.MaThanhVien ASC", [
            'ma' => $maThanhVien,
            'maLike' => '%' . $maThanhVien . '%',
            'ten' => $hoTen,
            'tenLike' => '%' . $hoTen . '%',
        ]);
    }

    public function findMember(string $maThanhVien): ?array
    {
        return $this->fetchOne('SELECT ThanhVien.*, VaiTro.TenVaiTro
            FROM ThanhVien
            LEFT JOIN VaiTro ON VaiTro.MaVaiTro = ThanhVien.MaVaiTro
            WHERE ThanhVien.MaThanhVien = :maThanhVien
            LIMIT 1', ['maThanhVien' => $maThanhVien]);
    }

    public function findMemberByEmail(?string $email): ?array
    {
        // Email trong session được dùng để khôi phục MaThanhVien khi session cũ chưa lưu mã.
        if (!$email) {
            return null;
        }
        return $this->fetchOne('SELECT ThanhVien.*, VaiTro.TenVaiTro
            FROM ThanhVien
            LEFT JOIN VaiTro ON VaiTro.MaVaiTro = ThanhVien.MaVaiTro
            WHERE ThanhVien.Email = :email
            LIMIT 1', ['email' => $email]);
    }

    public function emailRecipients(): array
    {
        // Danh sách gửi mail ưu tiên Chủ nhiệm (TVCN), sau đó Trợ giảng (TVTG), cuối cùng Thành viên (TV).
        return $this->fetchAll("SELECT ThanhVien.MaThanhVien,
                ThanhVien.HoTen,
                ThanhVien.Email,
                ThanhVien.MaVaiTro,
                VaiTro.TenVaiTro
            FROM ThanhVien
            LEFT JOIN VaiTro ON VaiTro.MaVaiTro = ThanhVien.MaVaiTro
            WHERE ThanhVien.Email <> ''
            ORDER BY CASE ThanhVien.MaVaiTro
                    WHEN 'TVCN' THEN 1
                    WHEN 'TVTG' THEN 2
                    ELSE 3
                END,
                ThanhVien.HoTen ASC,
                ThanhVien.Email ASC");
    }

    public function createMember(array $data): void
    {
        $stmt = $this->db->prepare('INSERT INTO ThanhVien (MaThanhVien, HoTen, Email, MatKhau, MaVaiTro, NgayTao) VALUES (:MaThanhVien, :HoTen, :Email, :MatKhau, :MaVaiTro, :NgayTao)');
        $stmt->execute([
            'MaThanhVien' => $data['MaThanhVien'] ?? '',
            'HoTen' => $data['HoTen'] ?? '',
            'Email' => $data['Email'] ?? '',
            // Mọi tài khoản tạo mới đều lưu hash, không lưu mật khẩu gốc.
            'MatKhau' => $this->hashPassword((string)($data['MatKhau'] ?? '')),
            'MaVaiTro' => $data['MaVaiTro'] ?? 'TV',
            'NgayTao' => $data['NgayTao'] ?? date('Y-m-d H:i:s'),
        ]);
    }

    public function updateMember(string $maThanhVien, array $data): void
    {
        $existing = $this->findMember($maThanhVien);
        $submittedPassword = trim((string)($data['MatKhau'] ?? ''));
        $passwordToStore = (string)($existing['MatKhau'] ?? '');
        if ($submittedPassword !== '' && $submittedPassword !== $passwordToStore) {
            $passwordToStore = $this->hashPassword($submittedPassword);
        }

        $stmt = $this->db->prepare('UPDATE ThanhVien SET HoTen = :HoTen, Email = :Email, MatKhau = :MatKhau, MaVaiTro = :MaVaiTro, NgayTao = :NgayTao WHERE MaThanhVien = :MaThanhVien');
        $stmt->execute([
            'HoTen' => $data['HoTen'] ?? '',
            'Email' => $data['Email'] ?? '',
            'MatKhau' => $passwordToStore,
            'MaVaiTro' => $data['MaVaiTro'] ?? 'TV',
            'NgayTao' => $data['NgayTao'] ?? date('Y-m-d H:i:s'),
            'MaThanhVien' => $maThanhVien,
        ]);
    }

    public function deleteMember(string $maThanhVien): void
    {
        $stmt = $this->db->prepare('DELETE FROM ThanhVien WHERE MaThanhVien = :MaThanhVien');
        $stmt->execute(['MaThanhVien' => $maThanhVien]);
    }

    public function login(string $email, string $password): ?array
    {
        // Lấy theo email trước, sau đó verify bằng password_verify để hỗ trợ hash an toàn.
        $member = $this->fetchOne('SELECT * FROM ThanhVien WHERE Email = :email LIMIT 1', ['email' => $email]);
        if (!$member || !$this->passwordMatches($password, (string)$member['MatKhau'])) {
            return null;
        }

        // Tài khoản cũ đang lưu plaintext sẽ được nâng cấp sang hash ngay sau lần đăng nhập hợp lệ.
        if (!$this->isPasswordHash((string)$member['MatKhau']) || password_needs_rehash((string)$member['MatKhau'], PASSWORD_DEFAULT)) {
            $member['MatKhau'] = $this->hashPassword($password);
            $this->storePasswordHash((string)$member['MaThanhVien'], (string)$member['MatKhau']);
        }
        return $member;
    }

    public function updatePassword(string $maThanhVien, string $oldPassword, string $newPassword): void
    {
        // Xác nhận mật khẩu cũ trước khi UPDATE để tránh đổi mật khẩu chỉ bằng mã thành viên.
        $member = $this->findMember($maThanhVien);
        if (!$member || !$this->passwordMatches($oldPassword, (string)$member['MatKhau'])) {
            throw new InvalidArgumentException('Mật khẩu cũ không đúng.');
        }
        if ($this->passwordMatches($newPassword, (string)$member['MatKhau'])) {
            throw new InvalidArgumentException('Mật khẩu mới không được trùng mật khẩu hiện tại.');
        }
        $this->storePasswordHash($maThanhVien, $this->hashPassword($newPassword));
    }

    public function resetPassword(string $maThanhVien, string $newPassword): void
    {
        // Luồng quên mật khẩu đã xác thực bằng token email nên không yêu cầu mật khẩu cũ.
        $this->storePasswordHash($maThanhVien, $this->hashPassword($newPassword));
    }

    public function passwordMatches(string $plainPassword, string $storedPassword): bool
    {
        // Tương thích dữ liệu cũ: hash thì verify, plaintext thì so sánh hằng thời gian.
        if ($this->isPasswordHash($storedPassword)) {
            return password_verify($plainPassword, $storedPassword);
        }
        return hash_equals($storedPassword, $plainPassword);
    }

    private function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    private function isPasswordHash(string $value): bool
    {
        return (password_get_info($value)['algoName'] ?? 'unknown') !== 'unknown';
    }

    private function storePasswordHash(string $maThanhVien, string $hash): void
    {
        $stmt = $this->db->prepare('UPDATE ThanhVien SET MatKhau = :matKhau WHERE MaThanhVien = :maThanhVien');
        $stmt->execute(['matKhau' => $hash, 'maThanhVien' => $maThanhVien]);
    }
}
