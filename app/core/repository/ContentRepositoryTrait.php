<?php
trait ContentRepositoryTrait
{
    public function listStudyGroups(?string $assistantId = null): array
    {
        $sql = $this->studyGroupSelectSql();
        $params = [];
        if ($assistantId !== null && $assistantId !== '') {
            $sql .= ' WHERE NhomHocTap.TroGiang = :assistantId';
            $params['assistantId'] = $assistantId;
        }
        return $this->fetchAll($sql . ' ORDER BY NhomHocTap.MaNhom ASC', $params);
    }

    public function listStudyGroupsForMember(string $maThanhVien): array
    {
        return $this->fetchAll($this->studyGroupSelectSql() . '
            INNER JOIN ThanhVienNhom ON ThanhVienNhom.MaNhom = NhomHocTap.MaNhom
            WHERE ThanhVienNhom.MaThanhVien = :MaThanhVien
            ORDER BY NhomHocTap.MaNhom ASC', ['MaThanhVien' => $maThanhVien]);
    }

    public function findStudyGroup(string $maNhom): ?array
    {
        return $this->fetchOne($this->studyGroupSelectSql() . ' WHERE NhomHocTap.MaNhom = :MaNhom LIMIT 1', ['MaNhom' => $maNhom]);
    }

    public function createStudyGroup(array $data): void
    {
        $stmt = $this->db->prepare('INSERT INTO NhomHocTap (MaNhom, TenNhom, TroGiang, NgayTao, MoTa) VALUES (:MaNhom, :TenNhom, :TroGiang, :NgayTao, :MoTa)');
        $stmt->execute([
            'MaNhom' => $data['MaNhom'] ?? '',
            'TenNhom' => $data['TenNhom'] ?? '',
            'TroGiang' => $data['TroGiang'] ?? '',
            'NgayTao' => $data['NgayTao'] ?? date('Y-m-d H:i:s'),
            'MoTa' => $data['MoTa'] ?? '',
        ]);
    }

    public function updateStudyGroup(string $maNhom, array $data): void
    {
        $stmt = $this->db->prepare('UPDATE NhomHocTap SET TenNhom = :TenNhom, TroGiang = :TroGiang, NgayTao = :NgayTao, MoTa = :MoTa WHERE MaNhom = :MaNhom');
        $stmt->execute([
            'TenNhom' => $data['TenNhom'] ?? '',
            'TroGiang' => $data['TroGiang'] ?? '',
            'NgayTao' => $data['NgayTao'] ?? date('Y-m-d H:i:s'),
            'MoTa' => $data['MoTa'] ?? '',
            'MaNhom' => $maNhom,
        ]);
    }

    public function deleteStudyGroup(string $maNhom): void
    {
        $stmt = $this->db->prepare('DELETE FROM NhomHocTap WHERE MaNhom = :MaNhom');
        $stmt->execute(['MaNhom' => $maNhom]);
    }

    public function listStudyGroupMembers(?string $assistantId = null, ?string $maNhom = null): array
    {
        $sql = $this->studyGroupMemberSelectSql();
        $where = [];
        $params = [];
        if ($assistantId !== null && $assistantId !== '') {
            $where[] = 'NhomHocTap.TroGiang = :assistantId';
            $params['assistantId'] = $assistantId;
        }
        if ($maNhom !== null && $maNhom !== '') {
            $where[] = 'ThanhVienNhom.MaNhom = :MaNhom';
            $params['MaNhom'] = $maNhom;
        }
        if ($where) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }
        return $this->fetchAll($sql . ' ORDER BY NhomHocTap.TenNhom ASC, ThanhVien.HoTen ASC', $params);
    }

    public function findStudyGroupMember(string $maNhom, string $maThanhVien): ?array
    {
        return $this->fetchOne($this->studyGroupMemberSelectSql() . '
            WHERE ThanhVienNhom.MaNhom = :MaNhom AND ThanhVienNhom.MaThanhVien = :MaThanhVien
            LIMIT 1', ['MaNhom' => $maNhom, 'MaThanhVien' => $maThanhVien]);
    }

    public function createStudyGroupMember(array $data): void
    {
        $maNhom = (string)($data['MaNhom'] ?? '');
        $maThanhVien = (string)($data['MaThanhVien'] ?? '');
        if ($this->isStudyGroupMember($maNhom, $maThanhVien)) {
            throw new InvalidArgumentException('Thành viên đã thuộc nhóm học tập này.');
        }
        $stmt = $this->db->prepare('INSERT INTO ThanhVienNhom (MaNhom, MaThanhVien, NgayThamGia) VALUES (:MaNhom, :MaThanhVien, :NgayThamGia)');
        $stmt->execute([
            'MaNhom' => $maNhom,
            'MaThanhVien' => $maThanhVien,
            'NgayThamGia' => $data['NgayThamGia'] ?? date('Y-m-d H:i:s'),
        ]);
    }

    public function updateStudyGroupMember(string $maNhom, string $maThanhVien, array $data): void
    {
        $stmt = $this->db->prepare('UPDATE ThanhVienNhom SET NgayThamGia = :NgayThamGia WHERE MaNhom = :MaNhom AND MaThanhVien = :MaThanhVien');
        $stmt->execute([
            'NgayThamGia' => $data['NgayThamGia'] ?? date('Y-m-d H:i:s'),
            'MaNhom' => $maNhom,
            'MaThanhVien' => $maThanhVien,
        ]);
    }

    public function deleteStudyGroupMember(string $maNhom, string $maThanhVien): void
    {
        $stmt = $this->db->prepare('DELETE FROM ThanhVienNhom WHERE MaNhom = :MaNhom AND MaThanhVien = :MaThanhVien');
        $stmt->execute(['MaNhom' => $maNhom, 'MaThanhVien' => $maThanhVien]);
    }

    public function membersForStudyGroup(string $maNhom): array
    {
        return $this->fetchAll("SELECT ThanhVien.MaThanhVien AS value,
                CONCAT(ThanhVien.HoTen, ' - ', ThanhVien.MaThanhVien) AS label
            FROM ThanhVienNhom
            INNER JOIN ThanhVien ON ThanhVien.MaThanhVien = ThanhVienNhom.MaThanhVien
            WHERE ThanhVienNhom.MaNhom = :MaNhom
            ORDER BY ThanhVien.HoTen ASC", ['MaNhom' => $maNhom]);
    }

    public function isStudyGroupMember(string $maNhom, string $maThanhVien): bool
    {
        if ($maNhom === '' || $maThanhVien === '') {
            return false;
        }
        $stmt = $this->db->prepare('SELECT 1 FROM ThanhVienNhom WHERE MaNhom = :MaNhom AND MaThanhVien = :MaThanhVien LIMIT 1');
        $stmt->execute(['MaNhom' => $maNhom, 'MaThanhVien' => $maThanhVien]);
        return (bool)$stmt->fetchColumn();
    }

    public function listAttendance(?string $maThanhVien = null, ?string $assistantId = null): array
    {
        $sql = $this->attendanceSelectSql();
        $where = [];
        $params = [];
        if ($maThanhVien !== null && $maThanhVien !== '') {
            $where[] = 'DiemDanh.MaThanhVien = :maThanhVien';
            $params['maThanhVien'] = $maThanhVien;
        }
        if ($assistantId !== null && $assistantId !== '') {
            $where[] = 'NhomHocTap.TroGiang = :assistantId';
            $params['assistantId'] = $assistantId;
        }
        if ($where) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }
        return $this->fetchAll($sql . ' ORDER BY DiemDanh.NgayDiemDanh DESC, DiemDanh.MaDiemDanh DESC', $params);
    }

    public function findAttendance($maDiemDanh, ?string $maThanhVien = null, ?string $assistantId = null): ?array
    {
        $sql = $this->attendanceSelectSql() . ' WHERE DiemDanh.MaDiemDanh = :maDiemDanh';
        $params = ['maDiemDanh' => $maDiemDanh];
        if ($maThanhVien !== null && $maThanhVien !== '') {
            $sql .= ' AND DiemDanh.MaThanhVien = :maThanhVien';
            $params['maThanhVien'] = $maThanhVien;
        }
        if ($assistantId !== null && $assistantId !== '') {
            $sql .= ' AND NhomHocTap.TroGiang = :assistantId';
            $params['assistantId'] = $assistantId;
        }
        return $this->fetchOne($sql . ' LIMIT 1', $params);
    }

    public function createAttendance(array $data): void
    {
        $this->validateAttendanceAssignment($data);
        $stmt = $this->db->prepare('INSERT INTO DiemDanh (MaNhom, MaThanhVien, NgayDiemDanh, TrangThai, GhiChu) VALUES (:MaNhom, :MaThanhVien, :NgayDiemDanh, :TrangThai, :GhiChu)');
        $stmt->execute([
            'MaNhom' => $data['MaNhom'] ?? '',
            'MaThanhVien' => $data['MaThanhVien'] ?? '',
            'NgayDiemDanh' => $data['NgayDiemDanh'] ?? date('Y-m-d'),
            'TrangThai' => $data['TrangThai'] ?? '',
            'GhiChu' => $data['GhiChu'] ?? '',
        ]);
    }

    public function updateAttendance($maDiemDanh, array $data): void
    {
        $this->validateAttendanceAssignment($data, (string)$maDiemDanh);
        $stmt = $this->db->prepare('UPDATE DiemDanh SET MaNhom = :MaNhom, MaThanhVien = :MaThanhVien, NgayDiemDanh = :NgayDiemDanh, TrangThai = :TrangThai, GhiChu = :GhiChu WHERE MaDiemDanh = :MaDiemDanh');
        $stmt->execute([
            'MaNhom' => $data['MaNhom'] ?? '',
            'MaThanhVien' => $data['MaThanhVien'] ?? '',
            'NgayDiemDanh' => $data['NgayDiemDanh'] ?? date('Y-m-d'),
            'TrangThai' => $data['TrangThai'] ?? '',
            'GhiChu' => $data['GhiChu'] ?? '',
            'MaDiemDanh' => $maDiemDanh,
        ]);
    }

    public function deleteAttendance($maDiemDanh): void
    {
        $stmt = $this->db->prepare('DELETE FROM DiemDanh WHERE MaDiemDanh = :MaDiemDanh');
        $stmt->execute(['MaDiemDanh' => $maDiemDanh]);
    }

    private function validateAttendanceAssignment(array $data, ?string $excludeId = null): void
    {
        $maNhom = (string)($data['MaNhom'] ?? '');
        $maThanhVien = (string)($data['MaThanhVien'] ?? '');
        $ngayDiemDanh = (string)($data['NgayDiemDanh'] ?? '');
        if (!$this->isStudyGroupMember($maNhom, $maThanhVien)) {
            throw new InvalidArgumentException('Thành viên không thuộc nhóm học tập đã chọn.');
        }
        $sql = 'SELECT 1 FROM DiemDanh WHERE MaNhom = :MaNhom AND MaThanhVien = :MaThanhVien AND NgayDiemDanh = :NgayDiemDanh';
        $params = ['MaNhom' => $maNhom, 'MaThanhVien' => $maThanhVien, 'NgayDiemDanh' => $ngayDiemDanh];
        if ($excludeId !== null && $excludeId !== '') {
            $sql .= ' AND MaDiemDanh <> :ExcludeId';
            $params['ExcludeId'] = $excludeId;
        }
        if ($this->fetchOne($sql . ' LIMIT 1', $params)) {
            throw new InvalidArgumentException('Thành viên đã được điểm danh trong nhóm này vào ngày đã chọn.');
        }
    }

    public function listPosts(): array
    {
        return $this->fetchAll($this->postSelectSql() . ' ORDER BY BaiDang.NgayTao DESC, BaiDang.MaBaiDang DESC');
    }

    public function findPost(string $maBaiDang): ?array
    {
        return $this->fetchOne($this->postSelectSql() . ' WHERE BaiDang.MaBaiDang = :MaBaiDang LIMIT 1', ['MaBaiDang' => $maBaiDang]);
    }

    public function createPost(array $data): void
    {
        $stmt = $this->db->prepare('INSERT INTO BaiDang (MaBaiDang, TieuDe, Anh, NoiDung, TacGia, NgayTao) VALUES (:MaBaiDang, :TieuDe, :Anh, :NoiDung, :TacGia, :NgayTao)');
        $stmt->execute([
            'MaBaiDang' => $data['MaBaiDang'] ?? '',
            'TieuDe' => $data['TieuDe'] ?? '',
            'Anh' => $data['Anh'] ?? '',
            'NoiDung' => $data['NoiDung'] ?? '',
            'TacGia' => $data['TacGia'] ?? '',
            'NgayTao' => $data['NgayTao'] ?? date('Y-m-d H:i:s'),
        ]);
    }

    public function updatePost(string $maBaiDang, array $data): void
    {
        $stmt = $this->db->prepare('UPDATE BaiDang SET TieuDe = :TieuDe, Anh = :Anh, NoiDung = :NoiDung, TacGia = :TacGia, NgayTao = :NgayTao WHERE MaBaiDang = :MaBaiDang');
        $stmt->execute([
            'TieuDe' => $data['TieuDe'] ?? '',
            'Anh' => $data['Anh'] ?? '',
            'NoiDung' => $data['NoiDung'] ?? '',
            'TacGia' => $data['TacGia'] ?? '',
            'NgayTao' => $data['NgayTao'] ?? date('Y-m-d H:i:s'),
            'MaBaiDang' => $maBaiDang,
        ]);
    }

    public function deletePost(string $maBaiDang): void
    {
        $stmt = $this->db->prepare('DELETE FROM BaiDang WHERE MaBaiDang = :MaBaiDang');
        $stmt->execute(['MaBaiDang' => $maBaiDang]);
    }
}
