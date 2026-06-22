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

    public function createMember(array $data): void
    {
        $stmt = $this->db->prepare('INSERT INTO ThanhVien (MaThanhVien, HoTen, Email, MatKhau, MaVaiTro, NgayTao) VALUES (:MaThanhVien, :HoTen, :Email, :MatKhau, :MaVaiTro, :NgayTao)');
        $stmt->execute([
            'MaThanhVien' => $data['MaThanhVien'] ?? '',
            'HoTen' => $data['HoTen'] ?? '',
            'Email' => $data['Email'] ?? '',
            'MatKhau' => $data['MatKhau'] ?? '',
            'MaVaiTro' => $data['MaVaiTro'] ?? 'TV',
            'NgayTao' => $data['NgayTao'] ?? date('Y-m-d H:i:s'),
        ]);
    }

    public function updateMember(string $maThanhVien, array $data): void
    {
        $stmt = $this->db->prepare('UPDATE ThanhVien SET HoTen = :HoTen, Email = :Email, MatKhau = :MatKhau, MaVaiTro = :MaVaiTro, NgayTao = :NgayTao WHERE MaThanhVien = :MaThanhVien');
        $stmt->execute([
            'HoTen' => $data['HoTen'] ?? '',
            'Email' => $data['Email'] ?? '',
            'MatKhau' => $data['MatKhau'] ?? '',
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
        // Bản ghi trả về chứa MaVaiTro; Login Controller sao chép role vào session để phân quyền.
        return $this->fetchOne('SELECT * FROM ThanhVien WHERE Email = :email AND MatKhau = :password LIMIT 1', ['email' => $email, 'password' => $password]);
    }

    public function updatePassword(string $maThanhVien, string $oldPassword, string $newPassword): void
    {
        // Xác nhận mật khẩu cũ trước khi UPDATE để tránh đổi mật khẩu chỉ bằng mã thành viên.
        $member = $this->findMember($maThanhVien);
        if (!$member || (string)$member['MatKhau'] !== $oldPassword) {
            throw new InvalidArgumentException('Mật khẩu cũ không đúng.');
        }
        $stmt = $this->db->prepare('UPDATE ThanhVien SET MatKhau = :matKhau WHERE MaThanhVien = :maThanhVien');
        $stmt->execute(['matKhau' => $newPassword, 'maThanhVien' => $maThanhVien]);
    }
}
