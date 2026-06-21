<?php
trait ScopeRepositoryTrait
{
    public function canManageClub(string $maCLB, string $maThanhVien): bool
    {
        $stmt = $this->db->prepare("SELECT 1 FROM CLB WHERE MaCLB = :maCLB AND ChuNhiem = :maThanhVien UNION SELECT 1 FROM ThanhVienCLB WHERE MaCLB = :maCLB2 AND MaThanhVien = :maThanhVien2 AND VaiTroCLB IN ('Chủ nhiệm', 'Ban tổ chức') LIMIT 1");
        $stmt->execute([
            'maCLB' => $maCLB,
            'maThanhVien' => $maThanhVien,
            'maCLB2' => $maCLB,
            'maThanhVien2' => $maThanhVien,
        ]);
        return (bool)$stmt->fetchColumn();
    }

    public function canManageEvent(string $maSuKien, string $maThanhVien): bool
    {
        $stmt = $this->db->prepare("SELECT 1 FROM SuKien WHERE MaSuKien = :maSuKien AND (NguoiToChuc = :owner OR MaCLB IN (SELECT MaCLB FROM ThanhVienCLB WHERE MaThanhVien = :member AND VaiTroCLB IN ('Chủ nhiệm', 'Ban tổ chức'))) LIMIT 1");
        $stmt->execute(['maSuKien' => $maSuKien, 'owner' => $maThanhVien, 'member' => $maThanhVien]);
        return (bool)$stmt->fetchColumn();
    }
}
