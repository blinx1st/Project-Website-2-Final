<?php
// Các hàm scope trả lời câu hỏi "người hiện tại có được quản lý bản ghi này không?".
trait ScopeRepositoryTrait
{
    // Có quyền nếu là chủ nhiệm trực tiếp hoặc có vai trò quản lý trong bảng thành viên CLB.
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

    // Người tổ chức hoặc người quản lý CLB sở hữu sự kiện đều được thao tác.
    public function canManageEvent(string $maSuKien, string $maThanhVien): bool
    {
        $stmt = $this->db->prepare("SELECT 1 FROM SuKien WHERE MaSuKien = :maSuKien AND (NguoiToChuc = :owner OR MaCLB IN (SELECT MaCLB FROM ThanhVienCLB WHERE MaThanhVien = :member AND VaiTroCLB IN ('Chủ nhiệm', 'Ban tổ chức'))) LIMIT 1");
        $stmt->execute(['maSuKien' => $maSuKien, 'owner' => $maThanhVien, 'member' => $maThanhVien]);
        return (bool)$stmt->fetchColumn();
    }

    // Với nhóm học tập, quyền của trợ giảng được ánh xạ trực tiếp qua cột TroGiang.
    public function canManageStudyGroup(string $maNhom, string $maThanhVien): bool
    {
        $stmt = $this->db->prepare('SELECT 1 FROM NhomHocTap WHERE MaNhom = :maNhom AND TroGiang = :maThanhVien LIMIT 1');
        $stmt->execute(['maNhom' => $maNhom, 'maThanhVien' => $maThanhVien]);
        return (bool)$stmt->fetchColumn();
    }
}
