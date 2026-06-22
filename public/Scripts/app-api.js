(function () {
    // IIFE giữ biến nội bộ không rò ra global; baseUrl do layout PHP truyền xuống.
    const baseUrl = (window.APP_BASE_URL || '').replace(/\/$/, '');
    const apiMessage = document.getElementById('api-message');

    function showMessage(message, ok) {
        // Ưu tiên vùng thông báo trong trang, chỉ dùng alert khi view không có vùng này.
        if (!apiMessage) {
            alert(message);
            return;
        }
        apiMessage.style.display = 'block';
        apiMessage.className = 'alert ' + (ok ? 'alert-success' : 'alert-danger');
        apiMessage.textContent = message;
    }

    async function readJson(response) {
        // Chuẩn hóa mọi API về một luồng lỗi duy nhất cho các khối try/catch phía dưới.
        const payload = await response.json().catch(() => ({}));
        if (!response.ok || payload.success === false) {
            throw new Error(payload.message || 'Thao tác không thành công.');
        }
        return payload;
    }

    document.addEventListener('click', async function (event) {
        // Event delegation cho phép một listener xử lý cả các nút được render động trong bảng.
        const confirmButton = event.target.closest('.js-confirm-attendance');
        if (confirmButton) {
            confirmButton.disabled = true;
            try {
                const body = new URLSearchParams({
                    MaSuKien: confirmButton.dataset.event || '',
                    MaThanhVien: confirmButton.dataset.member || ''
                });
                const payload = await fetch(baseUrl + '/Api_64131060/XacNhanThamGia', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body
                }).then(readJson);
                const row = confirmButton.closest('tr');
                const statusCell = row ? row.querySelector('.js-status-cell') : null;
                if (statusCell) {
                    statusCell.textContent = 'Đã tham gia';
                }
                confirmButton.remove();
                showMessage(payload.message || 'Đã xác nhận tham gia, cộng điểm và cấp chứng nhận.', true);
            } catch (error) {
                confirmButton.disabled = false;
                showMessage(error.message, false);
            }
            return;
        }

        const registerButton = event.target.closest('.js-register-event');
        if (registerButton) {
            // Khóa nút trong lúc chờ để tránh gửi nhiều đăng ký liên tiếp.
            registerButton.disabled = true;
            try {
                const url = baseUrl + '/Api_64131060/DangKySuKien?MaSuKien=' + encodeURIComponent(registerButton.dataset.event || '');
                const payload = await fetch(url, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                }).then(readJson);
                showMessage(payload.message || 'Đăng ký sự kiện thành công.', true);
            } catch (error) {
                registerButton.disabled = false;
                showMessage(error.message, false);
            }
            return;
        }

        const cancelButton = event.target.closest('.js-cancel-registration');
        if (cancelButton) {
            // Hủy làm thay đổi dữ liệu nên gửi POST với body dạng form URL encoded.
            cancelButton.disabled = true;
            try {
                const body = new URLSearchParams({ MaSuKien: cancelButton.dataset.event || '' });
                const payload = await fetch(baseUrl + '/Api_64131060/HuyDangKySuKien', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body
                }).then(readJson);
                showMessage(payload.message || 'Đã hủy đăng ký sự kiện.', true);
            } catch (error) {
                cancelButton.disabled = false;
                showMessage(error.message, false);
            }
        }
    });

    document.querySelectorAll('form[data-dependent-group-members]').forEach(function (form) {
        // Chỉ form điểm danh có cờ này; select thành viên phải phụ thuộc nhóm đang chọn.
        const groupSelect = form.querySelector('[name="MaNhom"]');
        const memberSelect = form.querySelector('[name="MaThanhVien"]');
        if (!groupSelect || !memberSelect) {
            return;
        }

        async function loadMembers(preserveValue) {
            // Xóa option cũ trước khi gọi API để không chọn nhầm thành viên của nhóm trước đó.
            const groupId = groupSelect.value || '';
            memberSelect.disabled = true;
            memberSelect.innerHTML = '<option value="">-- Chọn --</option>';
            if (!groupId) {
                memberSelect.disabled = false;
                return;
            }
            try {
                const url = baseUrl + '/Api_64131060/DanhSachThanhVienNhom?MaNhom=' + encodeURIComponent(groupId);
                const payload = await fetch(url, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                }).then(readJson);
                (payload.data || []).forEach(function (item) {
                    // API trả value/label đúng định dạng để có thể dựng option trực tiếp.
                    const option = document.createElement('option');
                    option.value = item.value;
                    option.textContent = item.label;
                    option.selected = String(item.value) === String(preserveValue || '');
                    memberSelect.appendChild(option);
                });
            } catch (error) {
                showMessage(error.message, false);
            } finally {
                memberSelect.disabled = false;
            }
        }

        // Khi mở form Edit, giữ lại lựa chọn hiện có sau khi danh sách mới được tải.
        const initialMember = memberSelect.value || '';
        groupSelect.addEventListener('change', function () {
            loadMembers('');
        });
        if (groupSelect.value) {
            loadMembers(initialMember);
        }
    });

    document.addEventListener('submit', function (event) {
        // Kiểm tra client giúp phản hồi sớm; Validator PHP vẫn là lớp kiểm tra bắt buộc phía server.
        const form = event.target.closest('form[data-validate-resource]');
        if (!form) {
            return;
        }
        const start = form.querySelector('[name="NgayBatDau"]');
        const end = form.querySelector('[name="NgayKetThuc"]');
        if (start && end && start.value && end.value && new Date(end.value) < new Date(start.value)) {
            event.preventDefault();
            showMessage('Ngày kết thúc phải sau hoặc bằng ngày bắt đầu.', false);
            end.focus();
            return;
        }
        const checkinOpen = form.querySelector('[name="CheckinMoLuc"]');
        const checkinClose = form.querySelector('[name="CheckinDongLuc"]');
        if (checkinOpen && checkinClose && checkinOpen.value && checkinClose.value && new Date(checkinClose.value) < new Date(checkinOpen.value)) {
            event.preventDefault();
            showMessage('Thời gian đóng QR phải sau hoặc bằng thời gian mở QR.', false);
            checkinClose.focus();
            return;
        }
        const year = form.querySelector('[name="NamHoc"]');
        if (year && year.value && !/^\d{4}-\d{4}$/.test(year.value)) {
            event.preventDefault();
            showMessage('Năm học phải có dạng 2024-2025.', false);
            year.focus();
            return;
        }
        const newPassword = form.querySelector('[name="MatKhauMoi"]');
        const confirmPassword = form.querySelector('[name="NhapLaiMatKhau"]');
        if (newPassword && confirmPassword && newPassword.value !== confirmPassword.value) {
            event.preventDefault();
            showMessage('Mật khẩu nhập lại không khớp.', false);
            confirmPassword.focus();
        }
    });
})();
