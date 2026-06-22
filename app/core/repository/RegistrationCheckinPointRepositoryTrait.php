<?php
// Gom toàn bộ vòng đời tham gia sự kiện: đăng ký, check-in, cộng điểm và cấp chứng nhận.
trait RegistrationCheckinPointRepositoryTrait
{
    // Bộ lọc thành viên phục vụ trang cá nhân; bộ lọc trợ giảng bảo vệ phạm vi sự kiện được quản lý.
    public function listEventRegistrations(?string $maThanhVien = null, ?string $assistantId = null): array
    {
        $sql = $this->eventRegistrationSelectSql();
        $where = [];
        $params = [];
        if ($maThanhVien) {
            $where[] = 'ThanhVienSuKien.MaThanhVien = :maThanhVien';
            $params['maThanhVien'] = $maThanhVien;
        }
        if ($assistantId) {
            $where[] = $this->assistantEventWhere();
            $params['assistantOwner'] = $assistantId;
            $params['assistantClubMember'] = $assistantId;
        }
        if ($where) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }
        $sql .= ' ORDER BY ThanhVienSuKien.NgayDangKy DESC';
        return $this->fetchAll($sql, $params);
    }

    public function findEventRegistration(string $maSuKien, string $maThanhVien): ?array
    {
        // ThanhVienSuKien dùng khóa chính kép nên một đăng ký được nhận diện bởi cả hai mã.
        return $this->fetchOne($this->eventRegistrationSelectSql() . ' WHERE ThanhVienSuKien.MaSuKien = :MaSuKien AND ThanhVienSuKien.MaThanhVien = :MaThanhVien LIMIT 1', [
            'MaSuKien' => $maSuKien,
            'MaThanhVien' => $maThanhVien,
        ]);
    }

    public function createEventRegistration(array $data): void
    {
        $stmt = $this->db->prepare('INSERT INTO ThanhVienSuKien (MaSuKien, MaThanhVien, NgayDangKy, TrangThaiThamGia, NgayXacNhan, XacNhanBoi) VALUES (:MaSuKien, :MaThanhVien, :NgayDangKy, :TrangThaiThamGia, :NgayXacNhan, :XacNhanBoi)');
        $stmt->execute([
            'MaSuKien' => $data['MaSuKien'] ?? '',
            'MaThanhVien' => $data['MaThanhVien'] ?? '',
            'NgayDangKy' => $data['NgayDangKy'] ?? date('Y-m-d H:i:s'),
            'TrangThaiThamGia' => $data['TrangThaiThamGia'] ?? 'Đã đăng ký',
            'NgayXacNhan' => $data['NgayXacNhan'] ?? null,
            'XacNhanBoi' => $data['XacNhanBoi'] ?? null,
        ]);
    }

    public function updateEventRegistration(string $maSuKien, string $maThanhVien, array $data): void
    {
        $stmt = $this->db->prepare('UPDATE ThanhVienSuKien SET NgayDangKy = :NgayDangKy, TrangThaiThamGia = :TrangThaiThamGia, NgayXacNhan = :NgayXacNhan, XacNhanBoi = :XacNhanBoi WHERE MaSuKien = :MaSuKien AND MaThanhVien = :MaThanhVien');
        $stmt->execute([
            'NgayDangKy' => $data['NgayDangKy'] ?? date('Y-m-d H:i:s'),
            'TrangThaiThamGia' => $data['TrangThaiThamGia'] ?? 'Đã đăng ký',
            'NgayXacNhan' => $data['NgayXacNhan'] ?? null,
            'XacNhanBoi' => $data['XacNhanBoi'] ?? null,
            'MaSuKien' => $maSuKien,
            'MaThanhVien' => $maThanhVien,
        ]);
    }

    public function deleteEventRegistration(string $maSuKien, string $maThanhVien): void
    {
        $stmt = $this->db->prepare('DELETE FROM ThanhVienSuKien WHERE MaSuKien = :MaSuKien AND MaThanhVien = :MaThanhVien');
        $stmt->execute(['MaSuKien' => $maSuKien, 'MaThanhVien' => $maThanhVien]);
    }

    public function listCheckins(?string $maThanhVien = null, ?string $assistantId = null): array
    {
        $sql = $this->checkinSelectSql();
        $where = [];
        $params = [];
        if ($maThanhVien) {
            $where[] = 'CheckinSuKien.MaThanhVien = :maThanhVien';
            $params['maThanhVien'] = $maThanhVien;
        }
        if ($assistantId) {
            $where[] = $this->assistantEventWhere();
            $params['assistantOwner'] = $assistantId;
            $params['assistantClubMember'] = $assistantId;
        }
        if ($where) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }
        $sql .= ' ORDER BY CheckinSuKien.ThoiGianCheckin DESC, CheckinSuKien.MaCheckin DESC';
        return $this->fetchAll($sql, $params);
    }

    public function findCheckin($maCheckin): ?array
    {
        return $this->fetchOne($this->checkinSelectSql() . ' WHERE CheckinSuKien.MaCheckin = :MaCheckin LIMIT 1', ['MaCheckin' => $maCheckin]);
    }

    public function listTrainingRules(): array
    {
        return $this->fetchAll($this->trainingRuleSelectSql() . ' ORDER BY QuyTacDiemRenLuyen.NamHoc DESC, QuyTacDiemRenLuyen.HocKy ASC');
    }

    public function findTrainingRule($maQuyTac): ?array
    {
        return $this->fetchOne($this->trainingRuleSelectSql() . ' WHERE QuyTacDiemRenLuyen.MaQuyTac = :MaQuyTac LIMIT 1', ['MaQuyTac' => $maQuyTac]);
    }

    public function createTrainingRule(array $data): void
    {
        $stmt = $this->db->prepare('INSERT INTO QuyTacDiemRenLuyen (MaLoaiSuKien, HocKy, NamHoc, Diem, GhiChu) VALUES (:MaLoaiSuKien, :HocKy, :NamHoc, :Diem, :GhiChu)');
        $stmt->execute([
            'MaLoaiSuKien' => $data['MaLoaiSuKien'] ?? '',
            'HocKy' => $data['HocKy'] ?? '',
            'NamHoc' => $data['NamHoc'] ?? '',
            'Diem' => $data['Diem'] ?? 0,
            'GhiChu' => $data['GhiChu'] ?? '',
        ]);
    }

    public function updateTrainingRule($maQuyTac, array $data): void
    {
        $stmt = $this->db->prepare('UPDATE QuyTacDiemRenLuyen SET MaLoaiSuKien = :MaLoaiSuKien, HocKy = :HocKy, NamHoc = :NamHoc, Diem = :Diem, GhiChu = :GhiChu WHERE MaQuyTac = :MaQuyTac');
        $stmt->execute([
            'MaLoaiSuKien' => $data['MaLoaiSuKien'] ?? '',
            'HocKy' => $data['HocKy'] ?? '',
            'NamHoc' => $data['NamHoc'] ?? '',
            'Diem' => $data['Diem'] ?? 0,
            'GhiChu' => $data['GhiChu'] ?? '',
            'MaQuyTac' => $maQuyTac,
        ]);
    }

    public function deleteTrainingRule($maQuyTac): void
    {
        $stmt = $this->db->prepare('DELETE FROM QuyTacDiemRenLuyen WHERE MaQuyTac = :MaQuyTac');
        $stmt->execute(['MaQuyTac' => $maQuyTac]);
    }

    public function listPoints(?string $hocKy = null, ?string $namHoc = null, ?string $maThanhVien = null, ?string $assistantId = null): array
    {
        // Các điều kiện tùy chọn cho phép cùng một hàm cấp dữ liệu cho admin, trợ giảng và thành viên.
        $where = [];
        $params = [];
        if ($hocKy) {
            $where[] = 'DiemRenLuyen.HocKy = :hocKy';
            $params['hocKy'] = $hocKy;
        }
        if ($namHoc) {
            $where[] = 'DiemRenLuyen.NamHoc = :namHoc';
            $params['namHoc'] = $namHoc;
        }
        if ($maThanhVien) {
            $where[] = 'DiemRenLuyen.MaThanhVien = :maThanhVien';
            $params['maThanhVien'] = $maThanhVien;
        }
        if ($assistantId) {
            $where[] = $this->assistantEventWhere();
            $params['assistantOwner'] = $assistantId;
            $params['assistantClubMember'] = $assistantId;
        }
        $sql = $this->pointSelectSql();
        if ($where) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }
        $sql .= ' ORDER BY DiemRenLuyen.NgayCong DESC, DiemRenLuyen.MaDiem DESC';
        return $this->fetchAll($sql, $params);
    }

    public function findPoint($maDiem): ?array
    {
        return $this->fetchOne($this->pointSelectSql() . ' WHERE DiemRenLuyen.MaDiem = :MaDiem LIMIT 1', ['MaDiem' => $maDiem]);
    }

    public function listPointTotals(?string $maThanhVien = null): array
    {
        $sql = $this->pointTotalSelectSql();
        $params = [];
        if ($maThanhVien) {
            $sql .= ' WHERE TongDiemRenLuyen.MaThanhVien = :maThanhVien';
            $params['maThanhVien'] = $maThanhVien;
        }
        $sql .= ' ORDER BY TongDiemRenLuyen.NamHoc DESC, TongDiemRenLuyen.HocKy ASC, ThanhVien.MaThanhVien ASC';
        return $this->fetchAll($sql, $params);
    }

    public function findPointTotal($maTongDiem): ?array
    {
        return $this->fetchOne($this->pointTotalSelectSql() . ' WHERE TongDiemRenLuyen.MaTongDiem = :MaTongDiem LIMIT 1', ['MaTongDiem' => $maTongDiem]);
    }

    public function listCertificates(?string $maSuKien = null, ?string $maThanhVien = null, ?string $assistantId = null): array
    {
        $where = [];
        $params = [];
        if ($maSuKien) {
            $where[] = 'ChungNhan.MaSuKien = :maSuKien';
            $params['maSuKien'] = $maSuKien;
        }
        if ($maThanhVien) {
            $where[] = 'ChungNhan.MaThanhVien = :maThanhVien';
            $params['maThanhVien'] = $maThanhVien;
        }
        if ($assistantId) {
            $where[] = $this->assistantEventWhere();
            $params['assistantOwner'] = $assistantId;
            $params['assistantClubMember'] = $assistantId;
        }
        $sql = $this->certificateSelectSql();
        if ($where) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }
        $sql .= ' ORDER BY ChungNhan.NgayCap DESC';
        return $this->fetchAll($sql, $params);
    }

    public function findCertificate(string $maChungNhan): ?array
    {
        return $this->fetchOne($this->certificateSelectSql() . ' WHERE ChungNhan.MaChungNhan = :MaChungNhan LIMIT 1', ['MaChungNhan' => $maChungNhan]);
    }

    public function createCertificate(array $data): void
    {
        $stmt = $this->db->prepare('INSERT INTO ChungNhan (MaChungNhan, MaSuKien, MaThanhVien, NgayCap, NoiDung, CapBoi) VALUES (:MaChungNhan, :MaSuKien, :MaThanhVien, :NgayCap, :NoiDung, :CapBoi)');
        $stmt->execute([
            'MaChungNhan' => $data['MaChungNhan'] ?? '',
            'MaSuKien' => $data['MaSuKien'] ?? '',
            'MaThanhVien' => $data['MaThanhVien'] ?? '',
            'NgayCap' => $data['NgayCap'] ?? date('Y-m-d H:i:s'),
            'NoiDung' => $data['NoiDung'] ?? '',
            'CapBoi' => $data['CapBoi'] ?? null,
        ]);
    }

    public function updateCertificate(string $maChungNhan, array $data): void
    {
        $stmt = $this->db->prepare('UPDATE ChungNhan SET MaSuKien = :MaSuKien, MaThanhVien = :MaThanhVien, NgayCap = :NgayCap, NoiDung = :NoiDung, CapBoi = :CapBoi WHERE MaChungNhan = :MaChungNhan');
        $stmt->execute([
            'MaSuKien' => $data['MaSuKien'] ?? '',
            'MaThanhVien' => $data['MaThanhVien'] ?? '',
            'NgayCap' => $data['NgayCap'] ?? date('Y-m-d H:i:s'),
            'NoiDung' => $data['NoiDung'] ?? '',
            'CapBoi' => $data['CapBoi'] ?? null,
            'MaChungNhan' => $maChungNhan,
        ]);
    }

    public function deleteCertificate(string $maChungNhan): void
    {
        $stmt = $this->db->prepare('DELETE FROM ChungNhan WHERE MaChungNhan = :MaChungNhan');
        $stmt->execute(['MaChungNhan' => $maChungNhan]);
    }

    public function registerEvent(string $maSuKien, string $maThanhVien): array
    {
        // Transaction và FOR UPDATE ngăn hai yêu cầu đồng thời cùng chiếm suất cuối của sự kiện.
        $this->db->beginTransaction();
        try {
            $eventStmt = $this->db->prepare('SELECT * FROM SuKien WHERE MaSuKien = :maSuKien FOR UPDATE');
            $eventStmt->execute(['maSuKien' => $maSuKien]);
            $event = $eventStmt->fetch();
            if (!$event) {
                throw new InvalidArgumentException('Không tìm thấy sự kiện.');
            }

            $existingStmt = $this->db->prepare('SELECT * FROM ThanhVienSuKien WHERE MaSuKien = :maSuKien AND MaThanhVien = :maThanhVien FOR UPDATE');
            $existingStmt->execute(['maSuKien' => $maSuKien, 'maThanhVien' => $maThanhVien]);
            $existing = $existingStmt->fetch();
            if ($existing && ($existing['TrangThaiThamGia'] ?? '') !== 'Đã hủy') {
                $this->db->commit();
                return ['status' => 'exists', 'message' => 'Bạn đã đăng ký sự kiện này.'];
            }

            $countStmt = $this->db->prepare("SELECT COUNT(*) FROM ThanhVienSuKien WHERE MaSuKien = :maSuKien AND TrangThaiThamGia <> 'Đã hủy'");
            $countStmt->execute(['maSuKien' => $maSuKien]);
            $registered = (int)$countStmt->fetchColumn();
            if ($registered >= (int)$event['SucChua']) {
                throw new InvalidArgumentException('Sự kiện đã đủ số lượng đăng ký.');
            }

            if ($existing) {
                // Bản ghi đã hủy được khôi phục thay vì chèn thêm vì bảng dùng khóa chính kép.
                $update = $this->db->prepare("UPDATE ThanhVienSuKien SET TrangThaiThamGia = 'Đã đăng ký', NgayDangKy = NOW(), NgayXacNhan = NULL, XacNhanBoi = NULL WHERE MaSuKien = :maSuKien AND MaThanhVien = :maThanhVien");
                $update->execute(['maSuKien' => $maSuKien, 'maThanhVien' => $maThanhVien]);
                $this->db->commit();
                return ['status' => 'restored', 'message' => 'Đăng ký lại sự kiện thành công.'];
            }

            $stmt = $this->db->prepare("INSERT INTO ThanhVienSuKien (MaSuKien, MaThanhVien, TrangThaiThamGia) VALUES (:maSuKien, :maThanhVien, 'Đã đăng ký')");
            $stmt->execute(['maSuKien' => $maSuKien, 'maThanhVien' => $maThanhVien]);
            $this->db->commit();
            return ['status' => 'created', 'message' => 'Đăng ký sự kiện thành công.'];
        } catch (Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function cancelEventRegistration(string $maSuKien, string $maThanhVien): array
    {
        // Hủy là đổi trạng thái để giữ lịch sử, không xóa vật lý bản ghi đăng ký.
        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare('SELECT * FROM ThanhVienSuKien WHERE MaSuKien = :maSuKien AND MaThanhVien = :maThanhVien FOR UPDATE');
            $stmt->execute(['maSuKien' => $maSuKien, 'maThanhVien' => $maThanhVien]);
            $registration = $stmt->fetch();
            if (!$registration) {
                throw new InvalidArgumentException('Bạn chưa đăng ký sự kiện này.');
            }
            if (($registration['TrangThaiThamGia'] ?? '') === 'Đã tham gia') {
                throw new InvalidArgumentException('Không thể hủy vì bạn đã check-in/tham gia sự kiện.');
            }
            if (($registration['TrangThaiThamGia'] ?? '') === 'Đã hủy') {
                $this->db->commit();
                return ['status' => 'exists', 'message' => 'Đăng ký này đã được hủy trước đó.'];
            }
            $checkStmt = $this->db->prepare('SELECT MaCheckin FROM CheckinSuKien WHERE MaSuKien = :maSuKien AND MaThanhVien = :maThanhVien LIMIT 1');
            $checkStmt->execute(['maSuKien' => $maSuKien, 'maThanhVien' => $maThanhVien]);
            if ($checkStmt->fetch()) {
                throw new InvalidArgumentException('Không thể hủy vì bạn đã check-in sự kiện.');
            }
            $update = $this->db->prepare("UPDATE ThanhVienSuKien SET TrangThaiThamGia = 'Đã hủy', NgayXacNhan = NULL, XacNhanBoi = NULL WHERE MaSuKien = :maSuKien AND MaThanhVien = :maThanhVien");
            $update->execute(['maSuKien' => $maSuKien, 'maThanhVien' => $maThanhVien]);
            $this->db->commit();
            return ['status' => 'cancelled', 'message' => 'Đã hủy đăng ký sự kiện.'];
        } catch (Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function confirmAttendance(string $maSuKien, string $maThanhVien, string $xacNhanBoi, string $phuongThuc = 'Thủ công'): array
    {
        // Một lần xác nhận cập nhật nhiều bảng nên tất cả phải thành công hoặc cùng rollback.
        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare('SELECT tvsk.*, sk.TenSuKien, sk.MaLoaiSuKien, sk.HocKy, sk.NamHoc, tv.HoTen
                FROM ThanhVienSuKien tvsk
                INNER JOIN SuKien sk ON sk.MaSuKien = tvsk.MaSuKien
                INNER JOIN ThanhVien tv ON tv.MaThanhVien = tvsk.MaThanhVien
                WHERE tvsk.MaSuKien = :maSuKien AND tvsk.MaThanhVien = :maThanhVien
                FOR UPDATE');
            $stmt->execute(['maSuKien' => $maSuKien, 'maThanhVien' => $maThanhVien]);
            $registration = $stmt->fetch();
            if (!$registration) {
                throw new InvalidArgumentException('Sinh viên chưa đăng ký sự kiện này.');
            }
            if (($registration['TrangThaiThamGia'] ?? '') === 'Đã hủy') {
                throw new InvalidArgumentException('Đăng ký này đã bị hủy, không thể xác nhận tham gia.');
            }

            $ruleStmt = $this->db->prepare('SELECT * FROM QuyTacDiemRenLuyen WHERE MaLoaiSuKien = :maLoaiSuKien AND HocKy = :hocKy AND NamHoc = :namHoc LIMIT 1');
            $ruleStmt->execute([
                'maLoaiSuKien' => $registration['MaLoaiSuKien'],
                'hocKy' => $registration['HocKy'],
                'namHoc' => $registration['NamHoc'],
            ]);
            $rule = $ruleStmt->fetch();
            if (!$rule) {
                throw new InvalidArgumentException('Chưa cấu hình điểm rèn luyện cho loại sự kiện/học kỳ/năm học này.');
            }

            // Chuỗi tác vụ: xác nhận đăng ký -> check-in -> điểm -> tổng điểm -> chứng nhận.
            $update = $this->db->prepare("UPDATE ThanhVienSuKien SET TrangThaiThamGia = 'Đã tham gia', NgayXacNhan = NOW(), XacNhanBoi = :xacNhanBoi WHERE MaSuKien = :maSuKien AND MaThanhVien = :maThanhVien");
            $update->execute(['xacNhanBoi' => $xacNhanBoi, 'maSuKien' => $maSuKien, 'maThanhVien' => $maThanhVien]);

            $checkin = $this->db->prepare('INSERT IGNORE INTO CheckinSuKien (MaSuKien, MaThanhVien, PhuongThuc, XacNhanBoi) VALUES (:maSuKien, :maThanhVien, :phuongThuc, :xacNhanBoi)');
            $checkin->execute(['maSuKien' => $maSuKien, 'maThanhVien' => $maThanhVien, 'phuongThuc' => $phuongThuc, 'xacNhanBoi' => $xacNhanBoi]);

            $point = $this->db->prepare("INSERT INTO DiemRenLuyen (MaThanhVien, MaSuKien, MaQuyTac, HocKy, NamHoc, SoDiem, GhiChu) VALUES (:maThanhVien, :maSuKien, :maQuyTac, :hocKy, :namHoc, :soDiem, :ghiChu) ON DUPLICATE KEY UPDATE MaQuyTac = VALUES(MaQuyTac), HocKy = VALUES(HocKy), NamHoc = VALUES(NamHoc), SoDiem = VALUES(SoDiem), GhiChu = VALUES(GhiChu)");
            $point->execute([
                'maThanhVien' => $maThanhVien,
                'maSuKien' => $maSuKien,
                'maQuyTac' => $rule['MaQuyTac'],
                'hocKy' => $registration['HocKy'],
                'namHoc' => $registration['NamHoc'],
                'soDiem' => $rule['Diem'],
                'ghiChu' => 'Cộng tự động khi xác nhận tham gia sự kiện.',
            ]);

            $total = $this->db->prepare('INSERT INTO TongDiemRenLuyen (MaThanhVien, HocKy, NamHoc, TongDiem)
                SELECT :maThanhVien, :hocKy, :namHoc, COALESCE(SUM(SoDiem), 0)
                FROM DiemRenLuyen
                WHERE MaThanhVien = :maThanhVien2 AND HocKy = :hocKy2 AND NamHoc = :namHoc2
                ON DUPLICATE KEY UPDATE TongDiem = VALUES(TongDiem), CapNhatLuc = CURRENT_TIMESTAMP');
            $total->execute([
                'maThanhVien' => $maThanhVien,
                'hocKy' => $registration['HocKy'],
                'namHoc' => $registration['NamHoc'],
                'maThanhVien2' => $maThanhVien,
                'hocKy2' => $registration['HocKy'],
                'namHoc2' => $registration['NamHoc'],
            ]);

            $maChungNhan = 'CN-' . $maSuKien . '-' . $maThanhVien;
            $certificate = $this->db->prepare('INSERT IGNORE INTO ChungNhan (MaChungNhan, MaSuKien, MaThanhVien, NoiDung, CapBoi) VALUES (:maChungNhan, :maSuKien, :maThanhVien, :noiDung, :capBoi)');
            $certificate->execute([
                'maChungNhan' => $maChungNhan,
                'maSuKien' => $maSuKien,
                'maThanhVien' => $maThanhVien,
                'noiDung' => 'Chứng nhận đã tham gia sự kiện ' . $registration['TenSuKien'],
                'capBoi' => $xacNhanBoi,
            ]);

            $this->db->commit();
            return [
                'MaSuKien' => $maSuKien,
                'MaThanhVien' => $maThanhVien,
                'TrangThaiThamGia' => 'Đã tham gia',
                'SoDiem' => (float)$rule['Diem'],
                'MaChungNhan' => $maChungNhan,
            ];
        } catch (Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function checkInEvent(string $maSuKien, string $maThanhVien, string $token): array
    {
        // QR chỉ là cổng kiểm tra token và thời gian; phần ghi dữ liệu dùng lại confirmAttendance().
        $event = $this->findEvent($maSuKien);
        if (!$event) {
            throw new InvalidArgumentException('Không tìm thấy sự kiện.');
        }
        if (!hash_equals((string)($event['CheckinToken'] ?? ''), $token)) {
            throw new InvalidArgumentException('Mã QR check-in không hợp lệ.');
        }
        $now = time();
        $openAt = strtotime((string)($event['CheckinMoLuc'] ?? ''));
        $closeAt = strtotime((string)($event['CheckinDongLuc'] ?? ''));
        if ($openAt !== false && $now < $openAt) {
            throw new InvalidArgumentException('QR check-in chưa đến thời gian hiệu lực. Vui lòng quay lại từ ' . date('d/m/Y H:i', $openAt) . '.');
        }
        if ($closeAt !== false && $now > $closeAt) {
            throw new InvalidArgumentException('QR check-in đã hết hiệu lực lúc ' . date('d/m/Y H:i', $closeAt) . '.');
        }
        $exists = $this->db->prepare('SELECT MaCheckin FROM CheckinSuKien WHERE MaSuKien = :maSuKien AND MaThanhVien = :maThanhVien LIMIT 1');
        $exists->execute(['maSuKien' => $maSuKien, 'maThanhVien' => $maThanhVien]);
        if ($exists->fetch()) {
            throw new InvalidArgumentException('Bạn đã check-in sự kiện này trước đó.');
        }
        return $this->confirmAttendance($maSuKien, $maThanhVien, $maThanhVien, 'QR');
    }

    public function ensureEventToken(string $maSuKien): string
    {
        // Token đã có được tái sử dụng để mã QR ổn định; chỉ sinh ngẫu nhiên khi còn trống.
        $event = $this->findEvent($maSuKien);
        if (!$event) {
            throw new InvalidArgumentException('Không tìm thấy sự kiện.');
        }
        if (!empty($event['CheckinToken'])) {
            return (string)$event['CheckinToken'];
        }
        $token = bin2hex(random_bytes(16));
        $stmt = $this->db->prepare('UPDATE SuKien SET CheckinToken = :token WHERE MaSuKien = :maSuKien');
        $stmt->execute(['token' => $token, 'maSuKien' => $maSuKien]);
        return $token;
    }

    public function registrationsForEvent(string $maSuKien): array
    {
        return $this->fetchAll($this->eventRegistrationSelectSql() . ' WHERE ThanhVienSuKien.MaSuKien = :maSuKien ORDER BY ThanhVienSuKien.NgayDangKy DESC', ['maSuKien' => $maSuKien]);
    }

    public function syncTrainingPointsFromRules(): array
    {
        // Đồng bộ toàn bộ là thao tác tái tạo dữ liệu dẫn xuất từ người đã tham gia và bảng quy tắc.
        $this->db->beginTransaction();
        try {
            $missingSql = "SELECT sk.MaLoaiSuKien, COALESCE(lsk.TenLoaiSuKien, sk.MaLoaiSuKien) AS TenLoaiSuKien, sk.HocKy, sk.NamHoc, GROUP_CONCAT(DISTINCT sk.MaSuKien ORDER BY sk.MaSuKien SEPARATOR ', ') AS SuKienThieu
                FROM ThanhVienSuKien tvsk
                INNER JOIN SuKien sk ON sk.MaSuKien = tvsk.MaSuKien
                LEFT JOIN LoaiSuKien lsk ON lsk.MaLoaiSuKien = sk.MaLoaiSuKien
                LEFT JOIN QuyTacDiemRenLuyen qt ON qt.MaLoaiSuKien = sk.MaLoaiSuKien AND qt.HocKy = sk.HocKy AND qt.NamHoc = sk.NamHoc
                WHERE tvsk.TrangThaiThamGia = 'Đã tham gia' AND qt.MaQuyTac IS NULL
                GROUP BY sk.MaLoaiSuKien, TenLoaiSuKien, sk.HocKy, sk.NamHoc
                ORDER BY sk.NamHoc DESC, sk.HocKy ASC, sk.MaLoaiSuKien ASC";
            $missing = $this->db->query($missingSql)->fetchAll();
            // Dừng trước khi xóa dữ liệu cũ nếu có sự kiện chưa được cấu hình quy tắc điểm.
            if ($missing) {
                $parts = array_map(static function (array $row): string {
                    return sprintf('%s (%s, %s, sự kiện: %s)', $row['TenLoaiSuKien'], $row['HocKy'], $row['NamHoc'], $row['SuKienThieu']);
                }, $missing);
                throw new InvalidArgumentException('Chưa cấu hình điểm rèn luyện cho: ' . implode('; ', $parts) . '.');
            }

            // Xóa tổng trước chi tiết để tuân theo thứ tự phụ thuộc dữ liệu.
            $this->db->exec('DELETE FROM TongDiemRenLuyen');
            $this->db->exec('DELETE FROM DiemRenLuyen');

            $points = $this->db->prepare("INSERT INTO DiemRenLuyen (MaThanhVien, MaSuKien, MaQuyTac, HocKy, NamHoc, SoDiem, GhiChu)
                SELECT tvsk.MaThanhVien, tvsk.MaSuKien, qt.MaQuyTac, sk.HocKy, sk.NamHoc, qt.Diem, 'Đồng bộ tự động từ quy tắc điểm.'
                FROM ThanhVienSuKien tvsk
                INNER JOIN SuKien sk ON sk.MaSuKien = tvsk.MaSuKien
                INNER JOIN QuyTacDiemRenLuyen qt ON qt.MaLoaiSuKien = sk.MaLoaiSuKien AND qt.HocKy = sk.HocKy AND qt.NamHoc = sk.NamHoc
                WHERE tvsk.TrangThaiThamGia = 'Đã tham gia'");
            $points->execute();
            $pointRows = $points->rowCount();

            $totals = $this->db->prepare('INSERT INTO TongDiemRenLuyen (MaThanhVien, HocKy, NamHoc, TongDiem)
                SELECT MaThanhVien, HocKy, NamHoc, SUM(SoDiem)
                FROM DiemRenLuyen
                GROUP BY MaThanhVien, HocKy, NamHoc');
            $totals->execute();
            $totalRows = $totals->rowCount();

            $this->db->commit();
            return [
                'SoDongDiem' => $pointRows,
                'SoDongTong' => $totalRows,
                'message' => 'Đã đồng bộ điểm rèn luyện từ quy tắc điểm.',
            ];
        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }

    public function termPointTotals(string $hocKy, string $namHoc, ?string $maCLB = null): array
    {
        // Có CLB thì tính trực tiếp từ chi tiết sự kiện; không có CLB thì dùng bảng tổng đã đồng bộ.
        if ($maCLB) {
            return $this->fetchAll('SELECT DiemRenLuyen.MaThanhVien, ThanhVien.HoTen, ThanhVien.Email, DiemRenLuyen.HocKy, DiemRenLuyen.NamHoc, SUM(DiemRenLuyen.SoDiem) AS TongDiem, MAX(DiemRenLuyen.NgayCong) AS CapNhatLuc
                FROM DiemRenLuyen
                INNER JOIN ThanhVien ON ThanhVien.MaThanhVien = DiemRenLuyen.MaThanhVien
                INNER JOIN SuKien ON SuKien.MaSuKien = DiemRenLuyen.MaSuKien
                WHERE DiemRenLuyen.HocKy = :hocKy AND DiemRenLuyen.NamHoc = :namHoc AND SuKien.MaCLB = :maCLB
                GROUP BY DiemRenLuyen.MaThanhVien, ThanhVien.HoTen, ThanhVien.Email, DiemRenLuyen.HocKy, DiemRenLuyen.NamHoc
                ORDER BY DiemRenLuyen.MaThanhVien ASC', ['hocKy' => $hocKy, 'namHoc' => $namHoc, 'maCLB' => $maCLB]);
        }
        return $this->fetchAll('SELECT TongDiemRenLuyen.*, ThanhVien.HoTen, ThanhVien.Email
            FROM TongDiemRenLuyen
            INNER JOIN ThanhVien ON ThanhVien.MaThanhVien = TongDiemRenLuyen.MaThanhVien
            WHERE TongDiemRenLuyen.HocKy = :hocKy AND TongDiemRenLuyen.NamHoc = :namHoc
            ORDER BY ThanhVien.MaThanhVien ASC', ['hocKy' => $hocKy, 'namHoc' => $namHoc]);
    }
}
