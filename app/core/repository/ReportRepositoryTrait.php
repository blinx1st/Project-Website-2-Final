<?php
// Quản lý báo cáo và tổng hợp số liệu cho dashboard quản trị.
trait ReportRepositoryTrait
{
    public function listReports(): array
    {
        return $this->fetchAll($this->reportSelectSql() . ' ORDER BY BaoCao.NgayNop DESC');
    }

    public function findReport($maBaoCao): ?array
    {
        return $this->fetchOne($this->reportSelectSql() . ' WHERE BaoCao.MaBaoCao = :MaBaoCao LIMIT 1', ['MaBaoCao' => $maBaoCao]);
    }

    public function createReport(array $data): void
    {
        $stmt = $this->db->prepare('INSERT INTO BaoCao (TieuDe, NoiDung, NopBoi, NgayNop) VALUES (:TieuDe, :NoiDung, :NopBoi, :NgayNop)');
        $stmt->execute([
            'TieuDe' => $data['TieuDe'] ?? '',
            'NoiDung' => $data['NoiDung'] ?? '',
            'NopBoi' => $data['NopBoi'] ?? '',
            'NgayNop' => $data['NgayNop'] ?? date('Y-m-d H:i:s'),
        ]);
    }

    public function updateReport($maBaoCao, array $data): void
    {
        $stmt = $this->db->prepare('UPDATE BaoCao SET TieuDe = :TieuDe, NoiDung = :NoiDung, NopBoi = :NopBoi, NgayNop = :NgayNop WHERE MaBaoCao = :MaBaoCao');
        $stmt->execute([
            'TieuDe' => $data['TieuDe'] ?? '',
            'NoiDung' => $data['NoiDung'] ?? '',
            'NopBoi' => $data['NopBoi'] ?? '',
            'NgayNop' => $data['NgayNop'] ?? date('Y-m-d H:i:s'),
            'MaBaoCao' => $maBaoCao,
        ]);
    }

    public function deleteReport($maBaoCao): void
    {
        $stmt = $this->db->prepare('DELETE FROM BaoCao WHERE MaBaoCao = :MaBaoCao');
        $stmt->execute(['MaBaoCao' => $maBaoCao]);
    }

    public function dashboardStats(?string $hocKy = null, ?string $namHoc = null, ?string $maCLB = null): array
    {
        // Tạo điều kiện một lần rồi tái sử dụng cho các chỉ số cùng phạm vi lọc.
        $eventWhere = [];
        $params = [];
        if ($hocKy) {
            $eventWhere[] = 'SuKien.HocKy = :hocKy';
            $params['hocKy'] = $hocKy;
        }
        if ($namHoc) {
            $eventWhere[] = 'SuKien.NamHoc = :namHoc';
            $params['namHoc'] = $namHoc;
        }
        if ($maCLB) {
            $eventWhere[] = 'SuKien.MaCLB = :maCLB';
            $params['maCLB'] = $maCLB;
        }
        $eventCondition = $eventWhere ? ' WHERE ' . implode(' AND ', $eventWhere) : '';
        // Phiên bản có AND dùng khi điều kiện sự kiện được nối ngay trong mệnh đề JOIN.
        $joinEventCondition = $eventWhere ? ' AND ' . implode(' AND ', $eventWhere) : '';

        // Bốn truy vấn đầu tạo các thẻ tổng quan: sự kiện, đăng ký, check-in và điểm.
        $events = $this->db->prepare('SELECT COUNT(*) FROM SuKien' . $eventCondition);
        $events->execute($params);
        $registrations = $this->db->prepare("SELECT COUNT(*) FROM ThanhVienSuKien INNER JOIN SuKien ON SuKien.MaSuKien = ThanhVienSuKien.MaSuKien" . ($eventWhere ? ' WHERE ' . implode(' AND ', $eventWhere) . " AND ThanhVienSuKien.TrangThaiThamGia <> 'Đã hủy'" : " WHERE ThanhVienSuKien.TrangThaiThamGia <> 'Đã hủy'"));
        $registrations->execute($params);
        $checkins = $this->db->prepare('SELECT COUNT(*) FROM CheckinSuKien INNER JOIN SuKien ON SuKien.MaSuKien = CheckinSuKien.MaSuKien' . $eventCondition);
        $checkins->execute($params);
        $points = $this->db->prepare('SELECT COALESCE(SUM(DiemRenLuyen.SoDiem), 0) FROM DiemRenLuyen INNER JOIN SuKien ON SuKien.MaSuKien = DiemRenLuyen.MaSuKien' . $eventCondition);
        $points->execute($params);

        // Hai truy vấn sau tạo bảng thống kê theo CLB và bảng xếp hạng thành viên.
        $byClub = $this->db->prepare("SELECT CLB.MaCLB, CLB.TenCLB, COUNT(DISTINCT SuKien.MaSuKien) AS SoSuKien, COUNT(DISTINCT CheckinSuKien.MaCheckin) AS SoCheckin, COALESCE(SUM(DISTINCT DiemRenLuyen.SoDiem), 0) AS TongDiem FROM CLB LEFT JOIN SuKien ON SuKien.MaCLB = CLB.MaCLB" . $joinEventCondition . ' LEFT JOIN CheckinSuKien ON CheckinSuKien.MaSuKien = SuKien.MaSuKien LEFT JOIN DiemRenLuyen ON DiemRenLuyen.MaSuKien = SuKien.MaSuKien GROUP BY CLB.MaCLB, CLB.TenCLB ORDER BY CLB.TenCLB ASC');
        $byClub->execute($params);

        $top = $this->db->prepare("SELECT ThanhVien.MaThanhVien, ThanhVien.HoTen, COUNT(DISTINCT CheckinSuKien.MaCheckin) AS SoLuotThamGia, COALESCE(SUM(DiemRenLuyen.SoDiem), 0) AS TongDiem FROM ThanhVien LEFT JOIN CheckinSuKien ON CheckinSuKien.MaThanhVien = ThanhVien.MaThanhVien LEFT JOIN SuKien ON SuKien.MaSuKien = CheckinSuKien.MaSuKien LEFT JOIN DiemRenLuyen ON DiemRenLuyen.MaThanhVien = ThanhVien.MaThanhVien AND DiemRenLuyen.MaSuKien = SuKien.MaSuKien WHERE ThanhVien.MaVaiTro = 'TV'" . ($eventWhere ? ' AND ' . implode(' AND ', $eventWhere) : '') . ' GROUP BY ThanhVien.MaThanhVien, ThanhVien.HoTen ORDER BY TongDiem DESC, SoLuotThamGia DESC, ThanhVien.MaThanhVien ASC LIMIT 10');
        $top->execute($params);

        return [
            // Controller truyền nguyên cấu trúc này sang view dashboard để hiển thị từng khu vực.
            'summary' => [
                'SoSuKien' => (int)$events->fetchColumn(),
                'SoDangKy' => (int)$registrations->fetchColumn(),
                'SoCheckin' => (int)$checkins->fetchColumn(),
                'TongDiem' => (float)$points->fetchColumn(),
            ],
            'byClub' => $byClub->fetchAll(),
            'topStudents' => $top->fetchAll(),
        ];
    }
}
