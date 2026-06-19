<?php
trait ContentRepositoryTrait
{
    public function listStudyGroups(): array
    {
        return $this->fetchAll($this->studyGroupSelectSql() . ' ORDER BY NhomHocTap.MaNhom ASC');
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

    public function listAttendance(?string $maThanhVien = null): array
    {
        $sql = $this->attendanceSelectSql();
        $params = [];
        if ($maThanhVien !== null && $maThanhVien !== '') {
            $sql .= ' WHERE DiemDanh.MaThanhVien = :maThanhVien';
            $params['maThanhVien'] = $maThanhVien;
        }
        $sql .= ' ORDER BY DiemDanh.NgayDiemDanh DESC, DiemDanh.MaDiemDanh DESC';
        return $this->fetchAll($sql, $params);
    }

    public function findAttendance($maDiemDanh, ?string $maThanhVien = null): ?array
    {
        $sql = $this->attendanceSelectSql() . ' WHERE DiemDanh.MaDiemDanh = :maDiemDanh';
        $params = ['maDiemDanh' => $maDiemDanh];
        if ($maThanhVien !== null && $maThanhVien !== '') {
            $sql .= ' AND DiemDanh.MaThanhVien = :maThanhVien';
            $params['maThanhVien'] = $maThanhVien;
        }
        return $this->fetchOne($sql . ' LIMIT 1', $params);
    }

    public function createAttendance(array $data): void
    {
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
