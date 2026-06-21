<?php
trait ClubRepositoryTrait
{
    public function listEventTypes(): array
    {
        return $this->db->query('SELECT LoaiSuKien.* FROM LoaiSuKien ORDER BY LoaiSuKien.MaLoaiSuKien ASC')->fetchAll();
    }

    public function findEventType(string $maLoaiSuKien): ?array
    {
        return $this->fetchOne('SELECT LoaiSuKien.* FROM LoaiSuKien WHERE MaLoaiSuKien = :MaLoaiSuKien LIMIT 1', ['MaLoaiSuKien' => $maLoaiSuKien]);
    }

    public function createEventType(array $data): void
    {
        $stmt = $this->db->prepare('INSERT INTO LoaiSuKien (MaLoaiSuKien, TenLoaiSuKien, MoTa) VALUES (:MaLoaiSuKien, :TenLoaiSuKien, :MoTa)');
        $stmt->execute([
            'MaLoaiSuKien' => $data['MaLoaiSuKien'] ?? '',
            'TenLoaiSuKien' => $data['TenLoaiSuKien'] ?? '',
            'MoTa' => $data['MoTa'] ?? '',
        ]);
    }

    public function updateEventType(string $maLoaiSuKien, array $data): void
    {
        $stmt = $this->db->prepare('UPDATE LoaiSuKien SET TenLoaiSuKien = :TenLoaiSuKien, MoTa = :MoTa WHERE MaLoaiSuKien = :MaLoaiSuKien');
        $stmt->execute([
            'TenLoaiSuKien' => $data['TenLoaiSuKien'] ?? '',
            'MoTa' => $data['MoTa'] ?? '',
            'MaLoaiSuKien' => $maLoaiSuKien,
        ]);
    }

    public function deleteEventType(string $maLoaiSuKien): void
    {
        $stmt = $this->db->prepare('DELETE FROM LoaiSuKien WHERE MaLoaiSuKien = :MaLoaiSuKien');
        $stmt->execute(['MaLoaiSuKien' => $maLoaiSuKien]);
    }

    public function listClubs(?string $assistantId = null): array
    {
        $sql = $this->clubSelectSql();
        $params = [];
        if ($assistantId) {
            $sql .= ' WHERE ' . $this->assistantClubWhere();
            $params = ['assistantOwner' => $assistantId, 'assistantClubMember' => $assistantId];
        }
        $sql .= ' ORDER BY CLB.MaCLB ASC';
        return $this->fetchAll($sql, $params);
    }

    public function findClub(string $maCLB): ?array
    {
        return $this->fetchOne($this->clubSelectSql() . ' WHERE CLB.MaCLB = :MaCLB LIMIT 1', ['MaCLB' => $maCLB]);
    }

    public function createClub(array $data): void
    {
        $stmt = $this->db->prepare('INSERT INTO CLB (MaCLB, TenCLB, MoTa, ChuNhiem, NgayThanhLap) VALUES (:MaCLB, :TenCLB, :MoTa, :ChuNhiem, :NgayThanhLap)');
        $stmt->execute([
            'MaCLB' => $data['MaCLB'] ?? '',
            'TenCLB' => $data['TenCLB'] ?? '',
            'MoTa' => $data['MoTa'] ?? '',
            'ChuNhiem' => $data['ChuNhiem'] ?? null,
            'NgayThanhLap' => $data['NgayThanhLap'] ?? null,
        ]);
    }

    public function updateClub(string $maCLB, array $data): void
    {
        $stmt = $this->db->prepare('UPDATE CLB SET TenCLB = :TenCLB, MoTa = :MoTa, ChuNhiem = :ChuNhiem, NgayThanhLap = :NgayThanhLap WHERE MaCLB = :MaCLB');
        $stmt->execute([
            'TenCLB' => $data['TenCLB'] ?? '',
            'MoTa' => $data['MoTa'] ?? '',
            'ChuNhiem' => $data['ChuNhiem'] ?? null,
            'NgayThanhLap' => $data['NgayThanhLap'] ?? null,
            'MaCLB' => $maCLB,
        ]);
    }

    public function deleteClub(string $maCLB): void
    {
        $stmt = $this->db->prepare('DELETE FROM CLB WHERE MaCLB = :MaCLB');
        $stmt->execute(['MaCLB' => $maCLB]);
    }

    public function listClubMembers(?string $assistantId = null): array
    {
        $sql = $this->clubMemberSelectSql();
        $params = [];
        if ($assistantId) {
            $sql .= ' WHERE ThanhVienCLB.MaCLB IN (' . $this->assistantClubSubquery() . ')';
            $params['assistantClubMember'] = $assistantId;
        }
        $sql .= ' ORDER BY CLB.TenCLB ASC, ThanhVien.HoTen ASC';
        return $this->fetchAll($sql, $params);
    }

    public function findClubMember(string $maCLB, string $maThanhVien): ?array
    {
        return $this->fetchOne($this->clubMemberSelectSql() . ' WHERE ThanhVienCLB.MaCLB = :MaCLB AND ThanhVienCLB.MaThanhVien = :MaThanhVien LIMIT 1', [
            'MaCLB' => $maCLB,
            'MaThanhVien' => $maThanhVien,
        ]);
    }

    public function createClubMember(array $data): void
    {
        $stmt = $this->db->prepare('INSERT INTO ThanhVienCLB (MaCLB, MaThanhVien, VaiTroCLB, NgayThamGia) VALUES (:MaCLB, :MaThanhVien, :VaiTroCLB, :NgayThamGia)');
        $stmt->execute([
            'MaCLB' => $data['MaCLB'] ?? '',
            'MaThanhVien' => $data['MaThanhVien'] ?? '',
            'VaiTroCLB' => $data['VaiTroCLB'] ?? '',
            'NgayThamGia' => $data['NgayThamGia'] ?? date('Y-m-d H:i:s'),
        ]);
    }

    public function updateClubMember(string $maCLB, string $maThanhVien, array $data): void
    {
        $stmt = $this->db->prepare('UPDATE ThanhVienCLB SET VaiTroCLB = :VaiTroCLB, NgayThamGia = :NgayThamGia WHERE MaCLB = :MaCLB AND MaThanhVien = :MaThanhVien');
        $stmt->execute([
            'VaiTroCLB' => $data['VaiTroCLB'] ?? '',
            'NgayThamGia' => $data['NgayThamGia'] ?? date('Y-m-d H:i:s'),
            'MaCLB' => $maCLB,
            'MaThanhVien' => $maThanhVien,
        ]);
    }

    public function deleteClubMember(string $maCLB, string $maThanhVien): void
    {
        $stmt = $this->db->prepare('DELETE FROM ThanhVienCLB WHERE MaCLB = :MaCLB AND MaThanhVien = :MaThanhVien');
        $stmt->execute(['MaCLB' => $maCLB, 'MaThanhVien' => $maThanhVien]);
    }
}
