<?php
trait EventRepositoryTrait
{
    public function listEvents(?string $assistantId = null): array
    {
        $sql = $this->eventSelectSql();
        $params = [];
        if ($assistantId) {
            $sql .= ' WHERE ' . $this->assistantEventWhere();
            $params = ['assistantOwner' => $assistantId, 'assistantClubMember' => $assistantId];
        }
        $sql .= ' ORDER BY SuKien.NgayBatDau DESC, SuKien.MaSuKien ASC';
        return $this->fetchAll($sql, $params);
    }

    public function searchEvents(string $maSuKien, string $tenSuKien, string $maCLB = '', string $maLoaiSuKien = '', string $hocKy = '', string $namHoc = '', ?string $assistantId = null): array
    {
        $where = [
            "(:ma = '' OR SuKien.MaSuKien LIKE :maLike)",
            "(:ten = '' OR SuKien.TenSuKien LIKE :tenLike)",
            "(:maCLB = '' OR SuKien.MaCLB = :maCLBValue)",
            "(:maLoaiSuKien = '' OR SuKien.MaLoaiSuKien = :maLoaiSuKienValue)",
            "(:hocKy = '' OR SuKien.HocKy = :hocKyValue)",
            "(:namHoc = '' OR SuKien.NamHoc = :namHocValue)",
        ];
        $params = [
            'ma' => $maSuKien,
            'maLike' => '%' . $maSuKien . '%',
            'ten' => $tenSuKien,
            'tenLike' => '%' . $tenSuKien . '%',
            'maCLB' => $maCLB,
            'maCLBValue' => $maCLB,
            'maLoaiSuKien' => $maLoaiSuKien,
            'maLoaiSuKienValue' => $maLoaiSuKien,
            'hocKy' => $hocKy,
            'hocKyValue' => $hocKy,
            'namHoc' => $namHoc,
            'namHocValue' => $namHoc,
        ];
        if ($assistantId) {
            $where[] = $this->assistantEventWhere();
            $params['assistantOwner'] = $assistantId;
            $params['assistantClubMember'] = $assistantId;
        }
        return $this->fetchAll($this->eventSelectSql() . ' WHERE ' . implode(' AND ', $where) . ' ORDER BY SuKien.NgayBatDau DESC, SuKien.MaSuKien ASC', $params);
    }

    public function findEvent(string $maSuKien): ?array
    {
        return $this->fetchOne($this->eventSelectSql() . ' WHERE SuKien.MaSuKien = :MaSuKien LIMIT 1', ['MaSuKien' => $maSuKien]);
    }

    public function createEvent(array $data): void
    {
        if (empty($data['CheckinMoLuc']) && !empty($data['NgayBatDau'])) {
            $data['CheckinMoLuc'] = $data['NgayBatDau'];
        }
        if (empty($data['CheckinDongLuc']) && !empty($data['NgayKetThuc'])) {
            $data['CheckinDongLuc'] = $data['NgayKetThuc'];
        }
        $stmt = $this->db->prepare('INSERT INTO SuKien (MaSuKien, TenSuKien, MaCLB, MaLoaiSuKien, HocKy, NamHoc, MoTa, NgayBatDau, NgayKetThuc, NguoiToChuc, SucChua, CheckinMoLuc, CheckinDongLuc) VALUES (:MaSuKien, :TenSuKien, :MaCLB, :MaLoaiSuKien, :HocKy, :NamHoc, :MoTa, :NgayBatDau, :NgayKetThuc, :NguoiToChuc, :SucChua, :CheckinMoLuc, :CheckinDongLuc)');
        $stmt->execute([
            'MaSuKien' => $data['MaSuKien'] ?? '',
            'TenSuKien' => $data['TenSuKien'] ?? '',
            'MaCLB' => $data['MaCLB'] ?? '',
            'MaLoaiSuKien' => $data['MaLoaiSuKien'] ?? '',
            'HocKy' => $data['HocKy'] ?? '',
            'NamHoc' => $data['NamHoc'] ?? '',
            'MoTa' => $data['MoTa'] ?? '',
            'NgayBatDau' => $data['NgayBatDau'] ?? date('Y-m-d H:i:s'),
            'NgayKetThuc' => $data['NgayKetThuc'] ?? date('Y-m-d H:i:s'),
            'NguoiToChuc' => $data['NguoiToChuc'] ?? '',
            'SucChua' => $data['SucChua'] ?? 1,
            'CheckinMoLuc' => $data['CheckinMoLuc'] ?? date('Y-m-d H:i:s'),
            'CheckinDongLuc' => $data['CheckinDongLuc'] ?? date('Y-m-d H:i:s'),
        ]);
    }

    public function updateEvent(string $maSuKien, array $data): void
    {
        if (empty($data['CheckinMoLuc']) && !empty($data['NgayBatDau'])) {
            $data['CheckinMoLuc'] = $data['NgayBatDau'];
        }
        if (empty($data['CheckinDongLuc']) && !empty($data['NgayKetThuc'])) {
            $data['CheckinDongLuc'] = $data['NgayKetThuc'];
        }
        $stmt = $this->db->prepare('UPDATE SuKien SET TenSuKien = :TenSuKien, MaCLB = :MaCLB, MaLoaiSuKien = :MaLoaiSuKien, HocKy = :HocKy, NamHoc = :NamHoc, MoTa = :MoTa, NgayBatDau = :NgayBatDau, NgayKetThuc = :NgayKetThuc, NguoiToChuc = :NguoiToChuc, SucChua = :SucChua, CheckinMoLuc = :CheckinMoLuc, CheckinDongLuc = :CheckinDongLuc WHERE MaSuKien = :MaSuKien');
        $stmt->execute([
            'TenSuKien' => $data['TenSuKien'] ?? '',
            'MaCLB' => $data['MaCLB'] ?? '',
            'MaLoaiSuKien' => $data['MaLoaiSuKien'] ?? '',
            'HocKy' => $data['HocKy'] ?? '',
            'NamHoc' => $data['NamHoc'] ?? '',
            'MoTa' => $data['MoTa'] ?? '',
            'NgayBatDau' => $data['NgayBatDau'] ?? date('Y-m-d H:i:s'),
            'NgayKetThuc' => $data['NgayKetThuc'] ?? date('Y-m-d H:i:s'),
            'NguoiToChuc' => $data['NguoiToChuc'] ?? '',
            'SucChua' => $data['SucChua'] ?? 1,
            'CheckinMoLuc' => $data['CheckinMoLuc'] ?? date('Y-m-d H:i:s'),
            'CheckinDongLuc' => $data['CheckinDongLuc'] ?? date('Y-m-d H:i:s'),
            'MaSuKien' => $maSuKien,
        ]);
    }

    public function deleteEvent(string $maSuKien): void
    {
        $stmt = $this->db->prepare('DELETE FROM SuKien WHERE MaSuKien = :MaSuKien');
        $stmt->execute(['MaSuKien' => $maSuKien]);
    }
}
