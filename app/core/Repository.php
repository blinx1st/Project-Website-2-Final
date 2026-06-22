<?php
require_once __DIR__ . '/repository/MemberRepositoryTrait.php';
require_once __DIR__ . '/repository/ClubRepositoryTrait.php';
require_once __DIR__ . '/repository/EventRepositoryTrait.php';
require_once __DIR__ . '/repository/ContentRepositoryTrait.php';
require_once __DIR__ . '/repository/RegistrationCheckinPointRepositoryTrait.php';
require_once __DIR__ . '/repository/ReportRepositoryTrait.php';
require_once __DIR__ . '/repository/ScopeRepositoryTrait.php';

// Repository là lớp làm việc với database bằng PDO và chứa các hàm nghiệp vụ chính của hệ thống.
class Repository
{
    // Mỗi trait gom một nhóm nghiệp vụ nhưng cùng dùng kết nối PDO và helper query của Repository.
    use MemberRepositoryTrait;
    use ClubRepositoryTrait;
    use EventRepositoryTrait;
    use ContentRepositoryTrait;
    use RegistrationCheckinPointRepositoryTrait;
    use ReportRepositoryTrait;
    use ScopeRepositoryTrait;

    private PDO $db;

    public function __construct()
    {
        // Database::connection() tái sử dụng một PDO duy nhất trong request.
        $this->db = Database::connection();
    }

    private function fetchAll(string $sql, array $params = []): array
    {
        // Prepared statement tách dữ liệu khỏi câu SQL và trả toàn bộ dòng dạng associative array.
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    private function fetchOne(string $sql, array $params = []): ?array
    {
        // Chuẩn hóa trường hợp không có bản ghi thành null để controller kiểm tra thống nhất.
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function options(array $relation): array
    {
        // Chuyển bảng quan hệ thành cặp value/label mà generic/form.php dùng để tạo option.
        $sql = sprintf('SELECT %s AS value, %s AS label FROM %s ORDER BY %s', $relation['value'], $relation['label'], $relation['table'], $relation['label']);
        return $this->db->query($sql)->fetchAll();
    }

    private function clubSelectSql(): string
    {
        // Các hàm *SelectSql tạo câu JOIN nền; trait có thể nối thêm WHERE/ORDER BY theo role.
        return 'SELECT CLB.*, ThanhVien.HoTen AS ChuNhiemTen
            FROM CLB
            LEFT JOIN ThanhVien ON ThanhVien.MaThanhVien = CLB.ChuNhiem';
    }

    private function clubMemberSelectSql(): string
    {
        return 'SELECT ThanhVienCLB.*, CLB.TenCLB, ThanhVien.HoTen, ThanhVien.Email
            FROM ThanhVienCLB
            LEFT JOIN CLB ON CLB.MaCLB = ThanhVienCLB.MaCLB
            LEFT JOIN ThanhVien ON ThanhVien.MaThanhVien = ThanhVienCLB.MaThanhVien';
    }

    private function eventSelectSql(): string
    {
        return "SELECT SuKien.*, CLB.TenCLB, ThanhVien.HoTen AS NguoiToChucTen, LoaiSuKien.TenLoaiSuKien,
                (SELECT COUNT(*) FROM ThanhVienSuKien tvsk WHERE tvsk.MaSuKien = SuKien.MaSuKien AND tvsk.TrangThaiThamGia <> 'Đã hủy') AS SoDangKy,
                (SELECT COUNT(*) FROM CheckinSuKien ck WHERE ck.MaSuKien = SuKien.MaSuKien) AS SoCheckin,
                GREATEST(SuKien.SucChua - (SELECT COUNT(*) FROM ThanhVienSuKien tvsk2 WHERE tvsk2.MaSuKien = SuKien.MaSuKien AND tvsk2.TrangThaiThamGia <> 'Đã hủy'), 0) AS SoChoConLai
            FROM SuKien
            LEFT JOIN CLB ON CLB.MaCLB = SuKien.MaCLB
            LEFT JOIN ThanhVien ON ThanhVien.MaThanhVien = SuKien.NguoiToChuc
            LEFT JOIN LoaiSuKien ON LoaiSuKien.MaLoaiSuKien = SuKien.MaLoaiSuKien";
    }

    private function studyGroupSelectSql(): string
    {
        return 'SELECT NhomHocTap.*, ThanhVien.HoTen AS TroGiangTen
            FROM NhomHocTap
            LEFT JOIN ThanhVien ON ThanhVien.MaThanhVien = NhomHocTap.TroGiang';
    }

    private function studyGroupMemberSelectSql(): string
    {
        return 'SELECT ThanhVienNhom.*, NhomHocTap.TenNhom, NhomHocTap.TroGiang,
                ThanhVien.HoTen, ThanhVien.Email
            FROM ThanhVienNhom
            INNER JOIN NhomHocTap ON NhomHocTap.MaNhom = ThanhVienNhom.MaNhom
            INNER JOIN ThanhVien ON ThanhVien.MaThanhVien = ThanhVienNhom.MaThanhVien';
    }

    private function attendanceSelectSql(): string
    {
        return 'SELECT DiemDanh.*, NhomHocTap.TenNhom, ThanhVien.HoTen
            FROM DiemDanh
            LEFT JOIN NhomHocTap ON NhomHocTap.MaNhom = DiemDanh.MaNhom
            LEFT JOIN ThanhVien ON ThanhVien.MaThanhVien = DiemDanh.MaThanhVien';
    }

    private function postSelectSql(): string
    {
        return 'SELECT BaiDang.*, ThanhVien.HoTen AS TacGiaTen
            FROM BaiDang
            LEFT JOIN ThanhVien ON ThanhVien.MaThanhVien = BaiDang.TacGia';
    }

    private function eventRegistrationSelectSql(): string
    {
        return 'SELECT ThanhVienSuKien.*, SuKien.TenSuKien, SuKien.MaCLB, SuKien.NguoiToChuc, ThanhVien.HoTen, NguoiXacNhan.HoTen AS XacNhanBoiTen
            FROM ThanhVienSuKien
            LEFT JOIN SuKien ON SuKien.MaSuKien = ThanhVienSuKien.MaSuKien
            LEFT JOIN ThanhVien ON ThanhVien.MaThanhVien = ThanhVienSuKien.MaThanhVien
            LEFT JOIN ThanhVien NguoiXacNhan ON NguoiXacNhan.MaThanhVien = ThanhVienSuKien.XacNhanBoi';
    }

    private function checkinSelectSql(): string
    {
        return 'SELECT CheckinSuKien.*, SuKien.TenSuKien, SuKien.MaCLB, SuKien.NguoiToChuc, CLB.TenCLB, ThanhVien.HoTen, NguoiXacNhan.HoTen AS XacNhanBoiTen
            FROM CheckinSuKien
            LEFT JOIN SuKien ON SuKien.MaSuKien = CheckinSuKien.MaSuKien
            LEFT JOIN CLB ON CLB.MaCLB = SuKien.MaCLB
            LEFT JOIN ThanhVien ON ThanhVien.MaThanhVien = CheckinSuKien.MaThanhVien
            LEFT JOIN ThanhVien NguoiXacNhan ON NguoiXacNhan.MaThanhVien = CheckinSuKien.XacNhanBoi';
    }

    private function trainingRuleSelectSql(): string
    {
        return 'SELECT QuyTacDiemRenLuyen.*, LoaiSuKien.TenLoaiSuKien
            FROM QuyTacDiemRenLuyen
            LEFT JOIN LoaiSuKien ON LoaiSuKien.MaLoaiSuKien = QuyTacDiemRenLuyen.MaLoaiSuKien';
    }

    private function pointSelectSql(): string
    {
        return 'SELECT DiemRenLuyen.*, ThanhVien.HoTen, SuKien.TenSuKien, SuKien.MaCLB, SuKien.NguoiToChuc, QuyTacDiemRenLuyen.Diem AS DiemQuyTac
            FROM DiemRenLuyen
            LEFT JOIN ThanhVien ON ThanhVien.MaThanhVien = DiemRenLuyen.MaThanhVien
            LEFT JOIN SuKien ON SuKien.MaSuKien = DiemRenLuyen.MaSuKien
            LEFT JOIN QuyTacDiemRenLuyen ON QuyTacDiemRenLuyen.MaQuyTac = DiemRenLuyen.MaQuyTac';
    }

    private function pointTotalSelectSql(): string
    {
        return 'SELECT TongDiemRenLuyen.*, ThanhVien.HoTen
            FROM TongDiemRenLuyen
            LEFT JOIN ThanhVien ON ThanhVien.MaThanhVien = TongDiemRenLuyen.MaThanhVien';
    }

    private function certificateSelectSql(): string
    {
        return 'SELECT ChungNhan.*, SuKien.TenSuKien, SuKien.MaCLB, SuKien.NguoiToChuc, ThanhVien.HoTen, NguoiCap.HoTen AS CapBoiTen
            FROM ChungNhan
            LEFT JOIN SuKien ON SuKien.MaSuKien = ChungNhan.MaSuKien
            LEFT JOIN ThanhVien ON ThanhVien.MaThanhVien = ChungNhan.MaThanhVien
            LEFT JOIN ThanhVien NguoiCap ON NguoiCap.MaThanhVien = ChungNhan.CapBoi';
    }

    private function reportSelectSql(): string
    {
        return 'SELECT BaoCao.*, ThanhVien.HoTen AS NopBoiTen
            FROM BaoCao
            LEFT JOIN ThanhVien ON ThanhVien.MaThanhVien = BaoCao.NopBoi';
    }

    private function assistantClubSubquery(): string
    {
        // TVTG chỉ quản lý CLB nơi họ là chủ nhiệm hoặc thuộc ban tổ chức.
        return "SELECT MaCLB FROM ThanhVienCLB WHERE MaThanhVien = :assistantClubMember AND VaiTroCLB IN ('Chủ nhiệm', 'Ban tổ chức')";
    }

    private function assistantClubWhere(): string
    {
        return '(CLB.ChuNhiem = :assistantOwner OR CLB.MaCLB IN (' . $this->assistantClubSubquery() . '))';
    }

    private function assistantEventWhere(): string
    {
        return '(SuKien.NguoiToChuc = :assistantOwner OR SuKien.MaCLB IN (' . $this->assistantClubSubquery() . '))';
    }
}
