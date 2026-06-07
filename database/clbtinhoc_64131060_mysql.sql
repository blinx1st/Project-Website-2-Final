CREATE DATABASE IF NOT EXISTS clbtinhoc_64131060 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE clbtinhoc_64131060;
SET NAMES utf8mb4;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS ChungNhan;
DROP TABLE IF EXISTS TongDiemRenLuyen;
DROP TABLE IF EXISTS DiemRenLuyen;
DROP TABLE IF EXISTS QuyTacDiemRenLuyen;
DROP TABLE IF EXISTS BaoCao;
DROP TABLE IF EXISTS BaiDang;
DROP TABLE IF EXISTS DiemDanh;
DROP TABLE IF EXISTS ThanhVienNhom;
DROP TABLE IF EXISTS NhomHocTap;
DROP TABLE IF EXISTS CheckinSuKien;
DROP TABLE IF EXISTS ThanhVienSuKien;
DROP TABLE IF EXISTS SuKien;
DROP TABLE IF EXISTS ThanhVienCLB;
DROP TABLE IF EXISTS CLB;
DROP TABLE IF EXISTS LoaiSuKien;
DROP TABLE IF EXISTS ThanhVien;
DROP TABLE IF EXISTS VaiTro;
SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE VaiTro (
    MaVaiTro VARCHAR(50) PRIMARY KEY,
    TenVaiTro VARCHAR(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE ThanhVien (
    MaThanhVien VARCHAR(50) PRIMARY KEY,
    HoTen VARCHAR(100) NOT NULL,
    Email VARCHAR(100) UNIQUE NOT NULL,
    MatKhau VARCHAR(255) NOT NULL,
    MaVaiTro VARCHAR(50) NOT NULL,
    NgayTao DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_thanhvien_vaitro FOREIGN KEY (MaVaiTro) REFERENCES VaiTro(MaVaiTro)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE LoaiSuKien (
    MaLoaiSuKien VARCHAR(50) PRIMARY KEY,
    TenLoaiSuKien VARCHAR(100) NOT NULL,
    MoTa TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE CLB (
    MaCLB VARCHAR(50) PRIMARY KEY,
    TenCLB VARCHAR(150) NOT NULL,
    MoTa TEXT,
    ChuNhiem VARCHAR(50) NULL,
    NgayThanhLap DATE NULL,
    CONSTRAINT fk_clb_chunhiem FOREIGN KEY (ChuNhiem) REFERENCES ThanhVien(MaThanhVien)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE ThanhVienCLB (
    MaCLB VARCHAR(50) NOT NULL,
    MaThanhVien VARCHAR(50) NOT NULL,
    VaiTroCLB VARCHAR(50) NOT NULL DEFAULT 'Thành viên',
    NgayThamGia DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (MaCLB, MaThanhVien),
    CONSTRAINT fk_tvclb_clb FOREIGN KEY (MaCLB) REFERENCES CLB(MaCLB),
    CONSTRAINT fk_tvclb_thanhvien FOREIGN KEY (MaThanhVien) REFERENCES ThanhVien(MaThanhVien)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE SuKien (
    MaSuKien VARCHAR(50) PRIMARY KEY,
    TenSuKien VARCHAR(100) NOT NULL,
    MaCLB VARCHAR(50) NOT NULL,
    MaLoaiSuKien VARCHAR(50) NOT NULL,
    HocKy VARCHAR(10) NOT NULL,
    NamHoc VARCHAR(20) NOT NULL,
    MoTa TEXT,
    NgayBatDau DATETIME NOT NULL,
    NgayKetThuc DATETIME NOT NULL,
    NguoiToChuc VARCHAR(50) NOT NULL,
    SucChua INT NOT NULL DEFAULT 50,
    CheckinToken VARCHAR(64) NOT NULL DEFAULT '',
    CheckinMoLuc DATETIME NOT NULL,
    CheckinDongLuc DATETIME NOT NULL,
    CONSTRAINT fk_sukien_clb FOREIGN KEY (MaCLB) REFERENCES CLB(MaCLB),
    CONSTRAINT fk_sukien_loai FOREIGN KEY (MaLoaiSuKien) REFERENCES LoaiSuKien(MaLoaiSuKien),
    CONSTRAINT fk_sukien_nguoitochuc FOREIGN KEY (NguoiToChuc) REFERENCES ThanhVien(MaThanhVien)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE ThanhVienSuKien (
    MaSuKien VARCHAR(50) NOT NULL,
    MaThanhVien VARCHAR(50) NOT NULL,
    NgayDangKy DATETIME DEFAULT CURRENT_TIMESTAMP,
    TrangThaiThamGia VARCHAR(50) NOT NULL DEFAULT 'Đã đăng ký',
    NgayXacNhan DATETIME NULL,
    XacNhanBoi VARCHAR(50) NULL,
    PRIMARY KEY (MaSuKien, MaThanhVien),
    CONSTRAINT fk_tvsk_sukien FOREIGN KEY (MaSuKien) REFERENCES SuKien(MaSuKien),
    CONSTRAINT fk_tvsk_thanhvien FOREIGN KEY (MaThanhVien) REFERENCES ThanhVien(MaThanhVien),
    CONSTRAINT fk_tvsk_xacnhan FOREIGN KEY (XacNhanBoi) REFERENCES ThanhVien(MaThanhVien)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE CheckinSuKien (
    MaCheckin INT PRIMARY KEY AUTO_INCREMENT,
    MaSuKien VARCHAR(50) NOT NULL,
    MaThanhVien VARCHAR(50) NOT NULL,
    ThoiGianCheckin DATETIME DEFAULT CURRENT_TIMESTAMP,
    PhuongThuc VARCHAR(50) NOT NULL DEFAULT 'QR',
    XacNhanBoi VARCHAR(50) NULL,
    UNIQUE KEY uq_checkin_sukien_thanhvien (MaSuKien, MaThanhVien),
    CONSTRAINT fk_checkin_sukien FOREIGN KEY (MaSuKien) REFERENCES SuKien(MaSuKien),
    CONSTRAINT fk_checkin_thanhvien FOREIGN KEY (MaThanhVien) REFERENCES ThanhVien(MaThanhVien),
    CONSTRAINT fk_checkin_xacnhan FOREIGN KEY (XacNhanBoi) REFERENCES ThanhVien(MaThanhVien)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE NhomHocTap (
    MaNhom VARCHAR(50) PRIMARY KEY,
    TenNhom VARCHAR(100) NOT NULL,
    TroGiang VARCHAR(50) NOT NULL,
    NgayTao DATETIME DEFAULT CURRENT_TIMESTAMP,
    MoTa TEXT,
    CONSTRAINT fk_nhom_trogiang FOREIGN KEY (TroGiang) REFERENCES ThanhVien(MaThanhVien)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE ThanhVienNhom (
    MaNhom VARCHAR(50) NOT NULL,
    MaThanhVien VARCHAR(50) NOT NULL,
    NgayThamGia DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (MaNhom, MaThanhVien),
    CONSTRAINT fk_tvnhom_nhom FOREIGN KEY (MaNhom) REFERENCES NhomHocTap(MaNhom),
    CONSTRAINT fk_tvnhom_thanhvien FOREIGN KEY (MaThanhVien) REFERENCES ThanhVien(MaThanhVien)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE DiemDanh (
    MaDiemDanh INT PRIMARY KEY AUTO_INCREMENT,
    MaNhom VARCHAR(50) NOT NULL,
    MaThanhVien VARCHAR(50) NOT NULL,
    NgayDiemDanh DATE NOT NULL,
    TrangThai VARCHAR(50) NOT NULL,
    GhiChu TEXT,
    CONSTRAINT fk_diemdanh_nhom FOREIGN KEY (MaNhom) REFERENCES NhomHocTap(MaNhom),
    CONSTRAINT fk_diemdanh_thanhvien FOREIGN KEY (MaThanhVien) REFERENCES ThanhVien(MaThanhVien)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE BaiDang (
    MaBaiDang VARCHAR(50) PRIMARY KEY NOT NULL,
    TieuDe TEXT NOT NULL,
    Anh VARCHAR(100) NOT NULL,
    NoiDung TEXT NOT NULL,
    TacGia VARCHAR(50) NOT NULL,
    NgayTao DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_baidang_tacgia FOREIGN KEY (TacGia) REFERENCES ThanhVien(MaThanhVien)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE BaoCao (
    MaBaoCao INT PRIMARY KEY AUTO_INCREMENT,
    TieuDe VARCHAR(100) NOT NULL,
    NoiDung TEXT NOT NULL,
    NopBoi VARCHAR(50) NOT NULL,
    NgayNop DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_baocao_nopboi FOREIGN KEY (NopBoi) REFERENCES ThanhVien(MaThanhVien)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE QuyTacDiemRenLuyen (
    MaQuyTac INT PRIMARY KEY AUTO_INCREMENT,
    MaLoaiSuKien VARCHAR(50) NOT NULL,
    HocKy VARCHAR(10) NOT NULL,
    NamHoc VARCHAR(20) NOT NULL,
    Diem DECIMAL(5,2) NOT NULL,
    GhiChu TEXT,
    UNIQUE KEY uq_quytac_loai_hocky_namhoc (MaLoaiSuKien, HocKy, NamHoc),
    CONSTRAINT fk_quytac_loai FOREIGN KEY (MaLoaiSuKien) REFERENCES LoaiSuKien(MaLoaiSuKien)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE DiemRenLuyen (
    MaDiem INT PRIMARY KEY AUTO_INCREMENT,
    MaThanhVien VARCHAR(50) NOT NULL,
    MaSuKien VARCHAR(50) NOT NULL,
    MaQuyTac INT NOT NULL,
    HocKy VARCHAR(10) NOT NULL,
    NamHoc VARCHAR(20) NOT NULL,
    SoDiem DECIMAL(5,2) NOT NULL,
    NgayCong DATETIME DEFAULT CURRENT_TIMESTAMP,
    GhiChu TEXT,
    UNIQUE KEY uq_diem_sinhvien_sukien (MaThanhVien, MaSuKien),
    CONSTRAINT fk_diem_thanhvien FOREIGN KEY (MaThanhVien) REFERENCES ThanhVien(MaThanhVien),
    CONSTRAINT fk_diem_sukien FOREIGN KEY (MaSuKien) REFERENCES SuKien(MaSuKien),
    CONSTRAINT fk_diem_quytac FOREIGN KEY (MaQuyTac) REFERENCES QuyTacDiemRenLuyen(MaQuyTac)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE TongDiemRenLuyen (
    MaTongDiem INT PRIMARY KEY AUTO_INCREMENT,
    MaThanhVien VARCHAR(50) NOT NULL,
    HocKy VARCHAR(10) NOT NULL,
    NamHoc VARCHAR(20) NOT NULL,
    TongDiem DECIMAL(6,2) NOT NULL DEFAULT 0,
    CapNhatLuc DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_tongdiem_sinhvien_ky (MaThanhVien, HocKy, NamHoc),
    CONSTRAINT fk_tongdiem_thanhvien FOREIGN KEY (MaThanhVien) REFERENCES ThanhVien(MaThanhVien)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE ChungNhan (
    MaChungNhan VARCHAR(100) PRIMARY KEY,
    MaSuKien VARCHAR(50) NOT NULL,
    MaThanhVien VARCHAR(50) NOT NULL,
    NgayCap DATETIME DEFAULT CURRENT_TIMESTAMP,
    NoiDung TEXT NOT NULL,
    CapBoi VARCHAR(50) NULL,
    UNIQUE KEY uq_chungnhan_sukien_sinhvien (MaSuKien, MaThanhVien),
    CONSTRAINT fk_chungnhan_sukien FOREIGN KEY (MaSuKien) REFERENCES SuKien(MaSuKien),
    CONSTRAINT fk_chungnhan_thanhvien FOREIGN KEY (MaThanhVien) REFERENCES ThanhVien(MaThanhVien),
    CONSTRAINT fk_chungnhan_capboi FOREIGN KEY (CapBoi) REFERENCES ThanhVien(MaThanhVien)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO VaiTro (MaVaiTro, TenVaiTro) VALUES
('TVCN', 'Admin / Phòng CTSV'),
('TVTG', 'Ban tổ chức CLB'),
('TV', 'Sinh viên');

INSERT INTO ThanhVien (MaThanhVien, HoTen, Email, MatKhau, MaVaiTro) VALUES
('64132127', 'Trần Thanh Thái', 'quochieuu@vnuis.edu.vn', '123', 'TVCN'),
('64131060', 'Phạm Tuấn Kiệt', 'trang@vnuis.edu.vn', '123', 'TVTG'),
('64132677', 'Vương Minh Trí', 'tri.vm.64cntt@vnuis.edu.vn', '123', 'TVTG'),
('64130378', 'Trần Diệp Hồng Dung', 'hongphuong@vnuis.edu.vn', '123', 'TV'),
('64132848', 'Trịnh Ngọc Tuấn', 'tuan.tn.64cntt@vnuis.edu.vn', '123', 'TV'),
('64130493', 'Cao Linh Hà', 'ha.cl.64cntt@vnuis.edu.vn', '123', 'TV'),
('64130152', 'Nguyễn Hồ Thanh Bình', 'binh.nht.64cntt@vnuis.edu.vn', '123', 'TV'),
('64131973', 'Nguyễn Hiểu Quyên', 'quyen.nh.64cntt@vnuis.edu.vn', '123', 'TV'),
('64132409', 'Vĩnh Thuận', 'thuan.v.64cntt@vnuis.edu.vn', '123', 'TV');

INSERT INTO LoaiSuKien (MaLoaiSuKien, TenLoaiSuKien, MoTa) VALUES
('WORKSHOP', 'Workshop chuyên môn', 'Hoạt động học thuật, kỹ năng và chuyên môn.'),
('CONTEST', 'Cuộc thi', 'Hoạt động thi đua, hackathon, cuộc thi học thuật.'),
('COMMUNITY', 'Hoạt động cộng đồng', 'Hoạt động ngoại khóa, định hướng và phục vụ cộng đồng.');

INSERT INTO CLB (MaCLB, TenCLB, MoTa, ChuNhiem, NgayThanhLap) VALUES
('CLBTH', 'CLB Tin học', 'Câu lạc bộ học thuật về công nghệ thông tin, lập trình và hoạt động ngoại khóa.', '64132127', '2020-09-01'),
('CLBWEB', 'Nhóm Web Infotech', 'Nhóm phụ trách các buổi học và workshop phát triển Web.', '64131060', '2022-09-01');

INSERT INTO ThanhVienCLB (MaCLB, MaThanhVien, VaiTroCLB) VALUES
('CLBTH', '64132127', 'Chủ nhiệm'),
('CLBTH', '64131060', 'Ban tổ chức'),
('CLBTH', '64132677', 'Ban tổ chức'),
('CLBTH', '64130378', 'Thành viên'),
('CLBTH', '64132848', 'Thành viên'),
('CLBWEB', '64131060', 'Chủ nhiệm'),
('CLBWEB', '64130152', 'Thành viên');

INSERT INTO SuKien (MaSuKien, TenSuKien, MaCLB, MaLoaiSuKien, HocKy, NamHoc, MoTa, NgayBatDau, NgayKetThuc, NguoiToChuc, SucChua, CheckinToken, CheckinMoLuc, CheckinDongLuc) VALUES
('SK001', 'Workshop Kỹ năng', 'CLBTH', 'WORKSHOP', 'HK1', '2024-2025', 'Buổi workshop về kỹ năng làm việc nhóm.', '2024-12-01 08:00:00', '2024-12-01 11:00:00', '64132127', 80, 'token-sk001-64131060', '2024-12-01 07:30:00', '2024-12-01 11:30:00'),
('SK002', 'Hackathon', 'CLBTH', 'CONTEST', 'HK1', '2024-2025', 'Cuộc thi lập trình kéo dài 48 giờ.', '2024-12-15 08:00:00', '2024-12-17 18:00:00', '64132127', 60, 'token-sk002-64131060', '2024-12-15 07:30:00', '2024-12-17 18:30:00'),
('SK003', 'Chào đón Tân Sinh Viên', 'CLBTH', 'COMMUNITY', 'HK1', '2024-2025', 'Hoạt động chào đón và định hướng.', '2024-11-30 08:00:00', '2024-11-30 11:00:00', '64132127', 120, 'token-sk003-64131060', '2024-11-30 07:30:00', '2024-11-30 11:30:00'),
('SK004', 'Buổi học Python cơ bản', 'CLBWEB', 'WORKSHOP', 'HK2', '2024-2025', 'Dành cho người mới bắt đầu học lập trình Python.', '2025-03-05 08:00:00', '2025-03-05 11:00:00', '64131060', 40, 'token-sk004-64131060', '2025-03-05 07:30:00', '2025-03-05 11:30:00');

INSERT INTO ThanhVienSuKien (MaSuKien, MaThanhVien, TrangThaiThamGia, NgayXacNhan, XacNhanBoi) VALUES
('SK001', '64130378', 'Đã tham gia', '2024-12-01 11:05:00', '64132127'),
('SK001', '64132848', 'Đã tham gia', '2024-12-01 11:08:00', '64132127'),
('SK001', '64130493', 'Đã đăng ký', NULL, NULL),
('SK002', '64130152', 'Đã đăng ký', NULL, NULL),
('SK002', '64131973', 'Đã đăng ký', NULL, NULL),
('SK003', '64132409', 'Đã đăng ký', NULL, NULL);

INSERT INTO CheckinSuKien (MaSuKien, MaThanhVien, ThoiGianCheckin, PhuongThuc, XacNhanBoi) VALUES
('SK001', '64130378', '2024-12-01 11:05:00', 'Thủ công', '64132127'),
('SK001', '64132848', '2024-12-01 11:08:00', 'Thủ công', '64132127');

INSERT INTO NhomHocTap (MaNhom, TenNhom, TroGiang, MoTa) VALUES
('MNLT', 'Nhóm Nhập môn lập trình', '64132127', 'Nhóm học tập về lập trình căn bản.'),
('KTLT', 'Nhóm Kỹ thuật lập trình', '64132677', 'Nhóm học tập về lập trình nâng cao.'),
('PTUDW', 'Nhóm Web', '64131060', 'Nhóm phát triển ứng dụng Web.');

INSERT INTO ThanhVienNhom (MaNhom, MaThanhVien) VALUES
('MNLT', '64130378'),
('MNLT', '64132848'),
('KTLT', '64130493'),
('PTUDW', '64130152');

INSERT INTO DiemDanh (MaNhom, MaThanhVien, NgayDiemDanh, TrangThai, GhiChu) VALUES
('MNLT', '64130378', '2024-10-06', 'Có mặt', ''),
('MNLT', '64132848', '2024-10-06', 'Vắng', 'Có việc đột xuất'),
('PTUDW', '64130152', '2024-10-05', 'Có mặt', '');

INSERT INTO BaiDang (MaBaiDang, TieuDe, Anh, NoiDung, TacGia) VALUES
('BD001', 'Thành viên mới và 10 vạn câu hỏi cần giải đáp', 'BD001.jpg', 'Thành viên mới và 10 vạn câu hỏi cần giải đáp', '64132127'),
('BD002', 'THÔNG BÁO TỪ CLB TIN HỌC', 'B002.jpg', 'Thông báo lịch học', '64132127'),
('BD003', 'CHÚC MỪNG NGÀY PHỤ NỮ VIỆT NAM 20/10', 'B003.jpg', 'CLB Tin học gửi lời chúc tốt đẹp đến các bạn nữ IT.', '64132127');

INSERT INTO BaoCao (TieuDe, NoiDung, NopBoi) VALUES
('Báo cáo Workshop Kỹ năng', 'Tổng kết buổi workshop ngày 01/12.', '64132127'),
('Báo cáo cuộc thi Hackathon', 'Báo cáo kết quả cuộc thi Hackathon.', '64132127');

INSERT INTO QuyTacDiemRenLuyen (MaLoaiSuKien, HocKy, NamHoc, Diem, GhiChu) VALUES
('WORKSHOP', 'HK1', '2024-2025', 5, 'Cộng điểm khi xác nhận tham gia workshop.'),
('CONTEST', 'HK1', '2024-2025', 10, 'Cộng điểm khi xác nhận tham gia cuộc thi.'),
('COMMUNITY', 'HK1', '2024-2025', 7, 'Cộng điểm khi xác nhận tham gia hoạt động cộng đồng.'),
('WORKSHOP', 'HK2', '2024-2025', 5, 'Cộng điểm khi xác nhận tham gia workshop.');

INSERT INTO DiemRenLuyen (MaThanhVien, MaSuKien, MaQuyTac, HocKy, NamHoc, SoDiem, GhiChu)
SELECT tvsk.MaThanhVien, tvsk.MaSuKien, qt.MaQuyTac, sk.HocKy, sk.NamHoc, qt.Diem, 'Dữ liệu mẫu: đã xác nhận tham gia.'
FROM ThanhVienSuKien tvsk
INNER JOIN SuKien sk ON sk.MaSuKien = tvsk.MaSuKien
INNER JOIN QuyTacDiemRenLuyen qt ON qt.MaLoaiSuKien = sk.MaLoaiSuKien AND qt.HocKy = sk.HocKy AND qt.NamHoc = sk.NamHoc
WHERE tvsk.TrangThaiThamGia = 'Đã tham gia';

INSERT INTO TongDiemRenLuyen (MaThanhVien, HocKy, NamHoc, TongDiem)
SELECT MaThanhVien, HocKy, NamHoc, SUM(SoDiem)
FROM DiemRenLuyen
GROUP BY MaThanhVien, HocKy, NamHoc;

INSERT INTO ChungNhan (MaChungNhan, MaSuKien, MaThanhVien, NoiDung, CapBoi)
SELECT CONCAT('CN-', tvsk.MaSuKien, '-', tvsk.MaThanhVien), tvsk.MaSuKien, tvsk.MaThanhVien,
       CONCAT('Chứng nhận đã tham gia sự kiện ', sk.TenSuKien),
       tvsk.XacNhanBoi
FROM ThanhVienSuKien tvsk
INNER JOIN SuKien sk ON sk.MaSuKien = tvsk.MaSuKien
WHERE tvsk.TrangThaiThamGia = 'Đã tham gia';
